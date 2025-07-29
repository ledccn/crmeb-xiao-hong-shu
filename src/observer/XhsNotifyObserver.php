<?php

namespace Ledc\CrmebXiaoHongShu\observer;

use Ledc\CrmebXiaoHongShu\jobs\PrintTicketNotifyJobs;
use Ledc\CrmebXiaoHongShu\services\XhsOrderService;
use SplObserver;
use SplSubject;

/**
 * 小红书消息订阅推送通知回调观察者
 */
class XhsNotifyObserver implements SplObserver
{
    /**
     * @param SplSubject|XhsNotifySubject $subject
     */
    public function update(SplSubject $subject): void
    {
        $notify = $subject->getNotify();
        $data = $notify->getData();
        $orderId = $data['orderId'] ?? '';
        if ($orderId) {
            // 同步订单详情
            $orderStatus = $data['orderStatus'] ?? '';
            $packageStatus = $data['packageStatus'] ?? '';
            XhsOrderService::doSyncOrderDetail($orderId);

            // 打印通知提醒类型的小票：售后申请、退款申请、买家收货信息变更
            switch (true) {
                case $notify->isMsgFulfillmentStatusChange():
                    if ($notify->isPaid()) {
                        // 订单支付成功发通知
                        // todo...
                    }
                    break;
                case $notify->isMsgFulfillmentReceiverChange():
                case $notify->isMsgAfterSaleCreate():
                    PrintTicketNotifyJobs::dispatch([$orderId, $notify->getMsgTag()], 2);
                    break;
                default:
                    break;
            }
        }
    }
}
