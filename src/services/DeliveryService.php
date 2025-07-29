<?php

namespace Ledc\CrmebXiaoHongShu\services;

use Ledc\CrmebIntraCity\enums\OrderChangeTypeEnums;
use Ledc\CrmebIntraCity\enums\TransOrderStatusEnums;
use Ledc\CrmebIntraCity\ServiceTransEnums;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderLogistics;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderLogs;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\XiaoHongShu\Parameters\Order\OrderDeliver;

/**
 * 小红书订单无物流发货（同城配送）服务类
 */
class DeliveryService
{
    /**
     * 配送单已取消
     * @param EbXhsOrderLogistics $orderLogistics
     * @param EbXhsOrder $xhsOrder
     */
    public static function doCancelled(EbXhsOrderLogistics $orderLogistics, EbXhsOrder $xhsOrder): void
    {
        $orderLogistics->trans_processed = 0;
        $orderLogistics->trans_order_status = TransOrderStatusEnums::Cancelled;
        $orderLogistics->trans_order_cancel_time = time();
        $orderLogistics->save();
    }

    /**
     * 订单发货
     * @param EbXhsOrderLogistics $orderLogistics
     * @param EbXhsOrder $xhsOrder
     */
    public static function doDelivery(EbXhsOrderLogistics $orderLogistics, EbXhsOrder $xhsOrder): void
    {
        $service_trans_label = ServiceTransEnums::cases()[$orderLogistics->service_trans_id];
        $orderLogistics->trans_order_status = TransOrderStatusEnums::InTransit;
        $orderLogistics->trans_order_fetch_time = time();
        $orderLogistics->save();

        // 无物流发货（同城配送）
        $parameter = new OrderDeliver();
        $parameter->orderId = $xhsOrder->order_id;
        $parameter->expressNo = $service_trans_label . ' 订单号:' . $orderLogistics->trans_order_id;
        $parameter->expressCompanyCode = 'selfdelivery';
        $parameter->expressCompanyName = $service_trans_label;
        $client = XiaoHongShuHelper::merchant()->getOrderClient();
        $result = $client->orderDeliver($parameter);
        // 记录订单变更日志
        EbXhsOrderLogs::create([
            'oid' => $xhsOrder->id,
            'order_id' => $xhsOrder->order_id,
            'operator' => '小红书订单发货',
            'action' => OrderChangeTypeEnums::CITY_DELIVERY,
            'content' => $service_trans_label . ' ' . $orderLogistics->trans_order_id . ' 骑手已取件；小红书订单号：' . $xhsOrder->order_id . ' 发货结果：' . json_encode($result, JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * 订单配送完成
     * @param EbXhsOrderLogistics $orderLogistics
     * @param EbXhsOrder $xhsOrder
     */
    public static function doCompleted(EbXhsOrderLogistics $orderLogistics, EbXhsOrder $xhsOrder): void
    {
        $orderLogistics->trans_order_status = TransOrderStatusEnums::Completed;
        $orderLogistics->trans_order_finish_time = time();
        $orderLogistics->save();
    }
}
