<?php

namespace Ledc\CrmebXiaoHongShu\services;

use Ledc\CrmebIntraCity\enums\OrderChangeTypeEnums;
use Ledc\CrmebIntraCity\enums\TransOrderStatusEnums;
use Ledc\CrmebIntraCity\parameters\ShanSongParameters;
use Ledc\CrmebIntraCity\services\ShanSongService as BaseShanSongService;
use Ledc\CrmebIntraCity\services\SystemStoreService;
use Ledc\CrmebIntraCity\ServiceTransEnums;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderLogistics;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderLogs;
use Ledc\ShanSong\Conversion;
use Ledc\ShanSong\Enums\OrderingSourceTypeEnums;
use Ledc\ShanSong\Parameters\OrderCalculate;
use Ledc\ShanSong\Parameters\OrderCalculateReceiver;
use Ledc\ShanSong\Parameters\OrderCalculateReceiverList;
use Ledc\ShanSong\Parameters\OrderCalculateResponse;
use Ledc\ShanSong\Parameters\OrderPlaceResponse;
use think\exception\ValidateException;
use think\facade\Log;
use Throwable;

/**
 * 闪送服务
 */
class ShanSongService extends BaseShanSongService
{
    /**
     * 订单计费
     * @param EbXhsOrder $xhsOrder
     * @param ShanSongParameters $shanSongParameters
     * @return OrderCalculateResponse
     */
    public function calculate(EbXhsOrder $xhsOrder, ShanSongParameters $shanSongParameters): OrderCalculateResponse
    {
        CreateOrderValidate::beforeValidate($xhsOrder);

        if ($shanSongParameters->goodType === null) {
            throw new ValidateException('物品类型不能为空');
        }
        $systemStore = SystemStoreService::getSystemStore(0);
        $xhsOrderLogistics = EbXhsOrderLogistics::findByOid($xhsOrder->id);
        // 发件人信息
        $sender = self::builderSender($systemStore);

        // 收件人信息
        $receiver = new OrderCalculateReceiver();
        $receiver->orderNo = $xhsOrder->order_id;
        $receiver->toAddress = $xhsOrder->receiver_address;
        if ($xhsOrder->receiver_lbs_type) {
            $bd09 = Conversion::GCJ02ToBD09($xhsOrder->receiver_longitude, $xhsOrder->receiver_latitude);
            $receiver->toLatitude = $bd09['latitude'];
            $receiver->toLongitude = $bd09['longitude'];
        } else {
            $receiver->toLatitude = $xhsOrder->receiver_latitude;
            $receiver->toLongitude = $xhsOrder->receiver_longitude;
        }
        $receiver->toReceiverName = $xhsOrder->receiver_name;
        $receiver->toMobile = $xhsOrder->receiver_phone;
        if ($xhsOrder->paid_time && $xhsOrder->order_seq) {
            $receiver->orderingSourceType = OrderingSourceTypeEnums::INT_1;
            $receiver->orderingSourceNo = get_order_seq($xhsOrder->paid_time, $xhsOrder->order_seq);
        }
        // 期望送达时间
        if ($xhsOrderLogistics && $xhsOrderLogistics->expected_finished_start_time && $xhsOrderLogistics->expected_finished_end_time) {
            $expected_finished_start_time = strtotime($xhsOrderLogistics->expected_finished_start_time);
            $expected_finished_end_time = strtotime($xhsOrderLogistics->expected_finished_end_time);
            if (time() < $expected_finished_start_time && $expected_finished_start_time < $expected_finished_end_time) {
                $receiver->expectStartTime = $expected_finished_start_time * 1000;
                $receiver->expectEndTime = $expected_finished_end_time * 1000;
            }
        }

        $orderCalculate = new OrderCalculate();
        $orderCalculate->cityName = $systemStore->shansong_city_name;
        $orderCalculate->storeId = $this->getConfig()->isDebug() ? $systemStore->shansong_store_id_test : $systemStore->shansong_store_id;
        $orderCalculate->deliveryPwd = 1;
        $orderCalculate->sender = $sender;
        // 重量、物品类型
        $cargo_weight = $xhsOrder->total_net_weight_amount * 1000;
        $receiver->goodType = $shanSongParameters->goodType;
        $receiver->weight = ceil($cargo_weight) ?: 1;
        $receiver->remarks = '流水号：' . get_order_seq($xhsOrder->paid_time, $xhsOrder->order_seq);

        // 创建闪送订单的附加参数
        if ($shanSongParameters->isExists()) {
            $orderCalculate->appointType = $shanSongParameters->appointType;
            $orderCalculate->appointmentDate = $shanSongParameters->appointmentDate;
            $orderCalculate->travelWay = $shanSongParameters->travelWay;
            $receiver->additionFee = $shanSongParameters->additionFee;
            $receiver->qualityDelivery = $shanSongParameters->qualityDelivery;
            $receiver->goodsSizeId = $shanSongParameters->goodsSizeId;
            $receiver->insuranceFlag = $shanSongParameters->insuranceFlag;
            $receiver->goodsPrice = $shanSongParameters->goodsPrice;
        }

        $orderCalculate->receiverList = (new OrderCalculateReceiverList())->add($receiver);
        log_develop('闪送订单计费构造参数: ' . json_encode($orderCalculate, JSON_UNESCAPED_UNICODE));
        return $this->merchant->orderCalculate($orderCalculate);
    }

    /**
     * 创建配送单
     * @param EbXhsOrder $xhsOrder
     * @param ShanSongParameters $shanSongParameters
     * @return OrderPlaceResponse
     */
    public function create(EbXhsOrder $xhsOrder, ShanSongParameters $shanSongParameters): OrderPlaceResponse
    {
        try {
            $orderCalculateResponse = $this->calculate($xhsOrder, $shanSongParameters);
            $orderPlaceResponse = $this->merchant->orderPlace($orderCalculateResponse->orderNumber);

            // 记录订单变更日志
            EbXhsOrderLogs::create([
                'oid' => $xhsOrder->id,
                'order_id' => $xhsOrder->order_id,
                'operator' => '系统管理员',
                'action' => OrderChangeTypeEnums::CITY_CREATE_ORDER,
                'content' => '呼叫同城配送，运力：' . ServiceTransEnums::TRANS_SHANSONG . ' 运力订单号：' . $orderPlaceResponse->orderNumber,
            ]);

            // 更新订单表
            $xhsOrder->db()->transaction(function () use ($xhsOrder, $orderPlaceResponse) {
                $xhsOrderLogistics = EbXhsOrderLogistics::findByOid($xhsOrder->id);
                if (!$xhsOrderLogistics) {
                    $xhsOrderLogistics = new EbXhsOrderLogistics();
                    $xhsOrderLogistics->oid = $xhsOrder->id;
                    $xhsOrderLogistics->order_id = $xhsOrder->order_id;
                }

                $xhsOrderLogistics->receiver_longitude = $xhsOrder->receiver_longitude;
                $xhsOrderLogistics->receiver_latitude = $xhsOrder->receiver_latitude;
                $xhsOrderLogistics->receiver_lbs_type = 1;
                $xhsOrderLogistics->service_store_id = $this->getConfig()->autoShopId();
                $xhsOrderLogistics->service_order_id = $orderPlaceResponse->orderNumber;
                $xhsOrderLogistics->service_trans_id = ServiceTransEnums::TRANS_SHANSONG;
                $xhsOrderLogistics->trans_distance = $orderPlaceResponse->totalDistance;
                $xhsOrderLogistics->trans_order_id = $orderPlaceResponse->orderNumber;
                $xhsOrderLogistics->trans_waybill_id = $orderPlaceResponse->orderNumber;
                $xhsOrderLogistics->trans_fee = $orderPlaceResponse->totalFeeAfterSave;
                $xhsOrderLogistics->trans_pickup_password = '';
                $xhsOrderLogistics->trans_delivery_password = '';
                $xhsOrderLogistics->trans_processed = 1;
                $xhsOrderLogistics->trans_order_status = TransOrderStatusEnums::Assigned;
                $xhsOrderLogistics->trans_order_create_time = time();
                $xhsOrderLogistics->trans_order_update_time = time();
                $xhsOrderLogistics->save();
            });

            return $orderPlaceResponse;
        } catch (Throwable $throwable) {
            Log::error('闪送提交订单异常:' . $throwable->getMessage());
            throw new ValidateException($throwable->getMessage());
        }
    }
}
