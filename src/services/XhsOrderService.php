<?php

namespace Ledc\CrmebXiaoHongShu\services;

use app\services\BaseServices;
use Ledc\CrmebIntraCity\LbsTencentHelper;
use Ledc\CrmebIntraCity\parameters\Address2LocationParameters;
use Ledc\CrmebIntraCity\services\LbsTencentService;
use Ledc\CrmebXiaoHongShu\dao\XhsOrderDao;
use Ledc\CrmebXiaoHongShu\jobs\PrintOrderJobs;
use Ledc\CrmebXiaoHongShu\jobs\SyncOrderReceiverInfoJobs;
use Ledc\CrmebXiaoHongShu\locker\OrderLocker;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderLogs;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\XiaoHongShu\Enums\OrderStatusEnums;
use Ledc\XiaoHongShu\Parameters\Order\GetOrderList;
use ReflectionException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\facade\Log;
use Throwable;

/**
 * XHS小红书订单服务层
 */
class XhsOrderService extends BaseServices
{
    /**
     * @var XhsOrderDao
     */
    protected $dao;

    /**
     * @param XhsOrderDao $dao
     */
    public function __construct(XhsOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @return XhsOrderDao
     */
    public function getDao(): XhsOrderDao
    {
        return $this->dao;
    }

    /**
     * 立刻同步订单信息
     * @param string $orderId 小红书订单号
     * @return void
     */
    public static function doSyncOrderDetail(string $orderId): void
    {
        $client = XiaoHongShuHelper::merchant()->getOrderClient();
        $result = $client->getOrderDetail($orderId);
        self::syncOrderDetail($result);
    }

    /**
     * 同步订单详情
     * @param array $item
     * @return void
     */
    public static function syncOrderDetail(array $item): void
    {
        $orderId = $item['orderId'];
        $locker = OrderLocker::create($orderId);
        if (!$locker->acquire()) {
            return;
        }

        $openAddressId = $item['openAddressId'] ?? '';
        $model = EbXhsOrder::where('order_id', $orderId)->findOrEmpty();
        if ($model->isEmpty()) {
            $model = new EbXhsOrder();
            $model->order_id = $orderId;
            $model->order_seq = generate_order_seq();
            $force = (bool)$openAddressId;
        } else {
            $force = $openAddressId && $model->open_address_id !== $openAddressId;
        }

        try {
            // 数据库事务
            $model->db()->transaction(function () use ($model, $item, $openAddressId) {
                $model->order_type = $item['orderType'];
                $model->order_status = $item['orderStatus'];
                $model->order_after_sales_status = $item['orderAfterSalesStatus'];
                $model->cancel_status = $item['cancelStatus'];
                $model->created_time = self::convertMillisecondToSecond($item['createdTime']);
                $model->paid_time = self::convertMillisecondToSecond($item['paidTime'] ?? 0);
                $model->updated_time = self::convertMillisecondToSecond($item['updateTime'] ?? 0);
                $model->delivery_time = self::convertMillisecondToSecond($item['deliveryTime'] ?? 0);
                $model->cancel_time = self::convertMillisecondToSecond($item['cancelTime'] ?? 0);
                $model->finish_time = self::convertMillisecondToSecond($item['finishTime'] ?? 0);
                $model->promise_last_delivery_time = self::convertMillisecondToSecond($item['promiseLastDeliveryTime'] ?? 0);
                $model->plan_info_id = $item['planInfoId'];
                $model->plan_info_name = $item['planInfoName'];
                $model->receiver_province_id = $item['receiverProvinceId'] ?? '';
                $model->receiver_province_name = $item['receiverProvinceName'] ?? '';
                $model->receiver_city_id = $item['receiverCityId'] ?? '';
                $model->receiver_city_name = $item['receiverCityName'] ?? '';
                $model->receiver_district_id = $item['receiverDistrictId'] ?? '';
                $model->receiver_district_name = $item['receiverDistrictName'] ?? '';
                if (!empty($item['customerRemark'])) {
                    $model->customer_remark = $item['customerRemark'];
                }
                if (!empty($item['sellerRemark'])) {
                    $model->seller_remark = $item['sellerRemark'];
                }
                // 收件人姓名+手机+地址等计算得出，用来查询收件人详情
                if (!empty($openAddressId)) {
                    $model->open_address_id = $openAddressId;
                }
                $model->seller_remark_flag = $item['sellerRemarkFlag'];
                $model->presale_delivery_start_time = self::convertMillisecondToSecond($item['presaleDeliveryStartTime'] ?? 0);
                $model->presale_delivery_end_time = self::convertMillisecondToSecond($item['presaleDeliveryEndTime'] ?? 0);
                if (!empty($item['originalOrderId'])) {
                    $model->original_order_id = $item['originalOrderId'];
                }
                $model->total_net_weight_amount = $item['totalNetWeightAmount'];
                $model->total_pay_amount = $item['totalPayAmount'];
                $model->total_shipping_free = $item['totalShippingFree'];
                $model->unpack = $item['unpack'];
                if (!empty($item['expressTrackingNo'])) {
                    $model->express_tracking_no = $item['expressTrackingNo'];
                }
                if (!empty($item['expressCompanyCode'])) {
                    $model->express_company_code = $item['expressCompanyCode'];
                }
                $model->simple_delivery_order_list = $item['simpleDeliveryOrderList'];
                $model->order_tag_list = $item['orderTagList'];
                $model->logistics = $item['logistics'];
                $model->logistics_mode = $item['logisticsMode'];
                $model->total_deposit_amount = $item['totalDepositAmount'];
                $model->total_merchant_discount = $item['totalMerchantDiscount'];
                $model->total_red_discount = $item['totalRedDiscount'];
                $model->merchant_actual_receive_amount = $item['merchantActualReceiveAmount'];
                $model->total_change_price_amount = $item['totalChangePriceAmount'];
                $model->payment_type = $item['paymentType'];
                $model->shop_id = $item['shopId'];
                $model->shop_name = $item['shopName'] ?? '';
                $model->user_id = $item['userId'];
                $model->out_promotion_amount = $item['outPromotionAmount'] ?? 0;
                $model->out_trade_no = $item['outTradeNo'] ?? '';
                $model->save();

                // 同步订单商品SKU信息
                XhsOrderProductService::syncOrderSkuList($model, $item['skuList']);

                return $model;
            });
        } catch (Throwable $throwable) {
            $locker->release();
            throw new ValidateException($throwable->getMessage());
        } finally {
            $locker->release();
        }

        // 异步队列：获取&解密订单收货人信息
        SyncOrderReceiverInfoJobs::dispatch([$orderId, $force]);
    }

    /**
     * 把毫秒时间戳转换为秒时间戳
     * @param string $millisecond
     * @return int
     */
    public static function convertMillisecondToSecond(string $millisecond): int
    {
        if (empty($millisecond) || $millisecond <= 0) {
            return 0;
        }
        return intval($millisecond / 1000);
    }

    /**
     * 获取&解密订单收货人信息
     * @param EbXhsOrder $model
     * @param bool $force 强制同步
     * @return bool
     */
    public static function syncOrderReceiverInfo(EbXhsOrder $model, bool $force = false): bool
    {
        try {
            if (!$model->paid_time || $model->paid_time <= 0) {
                return false;
            }
            if (!$model->open_address_id) {
                // 意外情况
                return false;
            }
            $locker = OrderLocker::syncOrderReceiverInfo($model->order_id);
            if (!$locker->acquire()) {
                return false;
            }
            if (false === $force && $model->receiver_address !== '' && $model->receiver_phone !== '' && $model->receiver_name !== '') {
                // 已同步
                return true;
            }

            return $model->db()->transaction(function () use ($model, $force) {
                $client = XiaoHongShuHelper::merchant()->getOrderClient();
                $receiverQueries = [];
                $receiverQuery = [
                    'orderId' => $model->order_id,
                    'openAddressId' => $model->open_address_id
                ];
                $receiverQueries[] = $receiverQuery;
                $_receiverInfos = $client->getOrderReceiverInfo($receiverQueries);
                if (empty($_receiverInfos['receiverInfos'])) {
                    return false;
                }
                $receiverInfos = $_receiverInfos['receiverInfos'];
                $receiverInfo = current($receiverInfos);
                if (empty($receiverInfo['receiverAddress']) || empty($receiverInfo['receiverPhone']) || empty($receiverInfo['receiverName'])) {
                    // 订单完成后，就取不到收货人信息啦
                    return false;
                }

                $baseInfos = [
                    ['dataTag' => $model->order_id, 'encryptedData' => $receiverInfo['receiverAddress']],
                    ['dataTag' => $model->order_id, 'encryptedData' => $receiverInfo['receiverPhone']],
                    ['dataTag' => $model->order_id, 'encryptedData' => $receiverInfo['receiverName']]
                ];
                $actionType = 1;
                $dataInfoList = $client->batchDecrypt($baseInfos, $actionType);
                if (empty($dataInfoList['dataInfoList'])) {
                    return false;
                }

                $fieldMaps = [
                    'receiver_address' => $receiverInfo['receiverAddress'],
                    'receiver_phone' => $receiverInfo['receiverPhone'],
                    'receiver_name' => $receiverInfo['receiverName']
                ];
                // ThinkORM模型有更新
                $isUpdateModel = false;
                // 是否为首次打印
                $firstPrintTicket = false;
                // 收获人信息是否变更
                $isReceiverChanged = false;
                $dataInfoList = $dataInfoList['dataInfoList'];
                foreach ($dataInfoList as $dataInfo) {
                    $encryptedData = $dataInfo['encryptedData'];
                    $decryptedData = $dataInfo['decryptedData'] ?? '';
                    if ('' === $decryptedData || null === $decryptedData) {
                        continue;
                    }
                    foreach ($fieldMaps as $key => $value) {
                        if ($value === $encryptedData) {
                            $modelCurrentKeyValue = $model->$key;
                            if ($modelCurrentKeyValue !== $decryptedData) {
                                $isUpdateModel = true;
                            }
                            if (empty($modelCurrentKeyValue)) {
                                $firstPrintTicket = true;
                                $reason = '首次同步';
                            } else {
                                $isReceiverChanged = true;
                                $reason = '变更';
                            }

                            // 先：更新订单信息
                            $model->$key = $decryptedData;
                            // 后：记录订单日志
                            EbXhsOrderLogs::create([
                                'oid' => $model->id,
                                'order_id' => $model->order_id,
                                'operator' => '小红书消息订阅推送',
                                'action' => 'syncOrderReceiverInfo',
                                'content' => '同步&解密订单收货人信息，' . "[$reason] " . $key . ': ' . $decryptedData,
                            ]);
                        }
                    }
                }
                $model->save();

                // 异步队列：打印小红书订单小票
                if ($isUpdateModel) {
                    // 首次打印 或 开启订单变更重打小票开关
                    if ($firstPrintTicket || ($isReceiverChanged && XiaoHongShuHelper::isOrderReceiverChangeReprint())) {
                        PrintOrderJobs::dispatch($model->order_id);
                    }
                }

                // 地址转经纬度
                self::address2location($model);

                return $isUpdateModel;
            });
        } catch (Throwable $throwable) {
            Log::error('同步&解密订单收货人信息异常：' . $throwable->getMessage());
        }

        return false;
    }

    /**
     * 地址转经纬度并更新模型
     * @param EbXhsOrder $model
     * @return void
     */
    public static function address2location(EbXhsOrder $model): bool
    {
        try {
            if (!$model->receiver_address) {
                return false;
            }

            $key = LbsTencentHelper::getDomainKey();
            if (empty($key)) {
                throw new ValidateException('请先在：系统设置->地图配置，配置地图KEY，才能把小红书订单的地址转换为经纬度');
            }
            $parameter = new Address2LocationParameters($key, $model->receiver_address);
            $result = LbsTencentService::address2location($parameter);
            if (empty($result['location'])) {
                throw new ValidateException('地址转经纬度失败：' . $model->receiver_address);
            }
            log_develop('地址转经纬度：' . $model->receiver_address . ' 转换为：' . json_encode($result, JSON_UNESCAPED_UNICODE));
            $location = $result['location'];
            $model->receiver_latitude = $location['lat'];
            $model->receiver_longitude = $location['lng'];
            // 国测局标准坐标系：腾讯、谷歌、高德
            $model->receiver_lbs_type = 1;
            $model->save();
            return true;
        } catch (Throwable $throwable) {
            Log::error('小红书订单，地址转经纬度异常：' . $throwable->getMessage());
        }

        return false;
    }

    /**
     * 定时同步24小时内的订单
     * - 建议每12小时执行一次
     * @return void
     */
    public static function scheduler(): void
    {
        if (!XiaoHongShuHelper::isEnabled()) {
            return;
        }
        try {
            XiaoHongShuHelper::clear();
            log_develop('定时同步24小时内的小红书订单：开始');
            $client = XiaoHongShuHelper::merchant()->getOrderClient();
            $parameter = GetOrderList::getDefault();

            do {
                $result = $client->getOrderList($parameter);
                $pageNo = $result['pageNo'];
                $maxPageNo = $result['maxPageNo'];
                $orderList = $result['orderList'];
                if (empty($orderList)) {
                    // 订单列表为空，处理结束条件
                    return;
                } else {
                    // 处理订单列表
                    array_map(function ($orderItem) use ($client) {
                        $orderId = $orderItem['orderId'];
                        $result = $client->getOrderDetail($orderId);
                        self::syncOrderDetail($result);
                    }, $orderList);
                    // 获取下一页
                    $parameter->pageNo += 1;
                }
            } while ($pageNo < $maxPageNo);
        } catch (Throwable $throwable) {
            Log::error('同步订单失败：' . $throwable->getMessage());
        } finally {
            log_develop('定时同步24小时内的小红书订单：结束');
        }
    }

    /**
     * 获取列表
     * @param array $where
     * @param string $order
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws ReflectionException
     */
    public function getList(array $where = [], string $order = 'id desc'): array
    {
        [$page, $limit] = $this->getPageValue();
        // 查询全部订单时，移除订单状态条件
        $order_status = (int)($where['order_status'] ?? OrderStatusEnums::ALL);
        if ($order_status === OrderStatusEnums::ALL) {
            unset($where['order_status']);
        }
        $list = $this->dao->selectList($where, '*', $page, $limit, $order, ['products', 'delivery']);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }
}
