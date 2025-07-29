<?php
declare (strict_types=1);

namespace Ledc\CrmebXiaoHongShu\listener;

use Ledc\CrmebIntraCity\enums\OrderChangeTypeEnums;
use Ledc\CrmebIntraCity\enums\TransOrderStatusEnums;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderLogistics;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderLogs;
use Ledc\CrmebXiaoHongShu\services\DeliveryService;
use Ledc\ShanSong\Parameters\Notify;
use think\exception\ValidateException;

/**
 * 监听闪送订单状态回调通知
 */
class ShanSongNotifyListener
{
    /**
     * 事件监听处理
     * @param Notify $event
     * @return bool
     */
    public function handle(Notify $event): bool
    {
        $notify = $event;
        $issOrderNo = $notify->issOrderNo;
        $orderNo = $notify->orderNo;

        $orderLogistics = EbXhsOrderLogistics::findByOrderId($orderNo);
        if (!$orderLogistics) {
            // 可能是其他业务订单
            return true;
        }
        if ($orderLogistics->trans_order_id !== $notify->issOrderNo) {
            throw new ValidateException('闪送订单号校验失败');
        }
        $xhsOrder = $orderLogistics->getXhsOrder();

        if ($notify->subStatusDesc) {
            $desc = $notify->statusDesc === $notify->subStatusDesc ? $notify->statusDesc : $notify->statusDesc . '(' . $notify->subStatusDesc . ')';
        } else {
            $desc = $notify->statusDesc;
        }

        // 记录订单变更日志
        EbXhsOrderLogs::create([
            'oid' => $xhsOrder->id,
            'order_id' => $xhsOrder->order_id,
            'operator' => '闪送订单状态回调',
            'action' => OrderChangeTypeEnums::CITY_NOTIFY_CALLBACK,
            'content' => '闪送 ' . $issOrderNo . ' 订单状态变更：【' . $desc . '】',
        ]);

        // 更新订单状态
        $orderLogistics->trans_order_update_time = time();
        $orderLogistics->service_order_status = $notify->status;
        $orderLogistics->service_order_sub_status = $notify->subStatus;
        $orderLogistics->save();

        switch (true) {
            case $notify->isCancelled():
            case $notify->isCompletedRefund():
                DeliveryService::doCancelled($orderLogistics, $xhsOrder);
                break;
            case $notify->isRiderAcceptedAndAwaitingPickup():
                $orderLogistics->trans_order_status = TransOrderStatusEnums::PendingPickup;
                $orderLogistics->trans_delivery_password = $notify->deliveryPassword;
                $orderLogistics->trans_order_accept_time = time();
                if ($courier = $notify->courier) {
                    EbXhsOrderLogs::create([
                        'oid' => $xhsOrder->id,
                        'order_id' => $xhsOrder->order_id,
                        'operator' => '闪送订单状态回调',
                        'action' => OrderChangeTypeEnums::CITY_NOTIFY_CALLBACK,
                        'content' => '闪送 ' . $issOrderNo . ' 骑手名称：' . $courier->name . ' 电话：' . $courier->mobile,
                    ]);
                }
                $orderLogistics->save();
                break;
            case $notify->isDelivering():
                DeliveryService::doDelivery($orderLogistics, $xhsOrder);
                break;
            case $notify->isCompleted():
                DeliveryService::doCompleted($orderLogistics, $xhsOrder);
                break;
            default:
                break;
        }

        return true;
    }
}
