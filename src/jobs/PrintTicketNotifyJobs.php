<?php

namespace Ledc\CrmebXiaoHongShu\jobs;

use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\CrmebXiaoHongShu\services\PrinterService;
use Ledc\ThinkModelTrait\Contracts\HasJobs;
use Ledc\XiaoHongShu\Enums\NotifyMsgTagEnums;

/**
 * 打印小票通知
 * - 售后申请、退款申请、买家收货信息变更
 */
class PrintTicketNotifyJobs
{
    use HasJobs;

    /**
     * 执行
     * @param string $orderId 小红书订单ID
     * @param string $msgTag 消息标签
     * @return bool
     */
    public function execute(string $orderId = '', string $msgTag = ''): bool
    {
        if (empty($orderId) || empty($msgTag)) {
            return true;
        }

        $xhsOrder = EbXhsOrder::uniqueQuery($orderId);
        if (!$xhsOrder) {
            return true;
        }

        switch ($msgTag) {
            case NotifyMsgTagEnums::msg_fulfillment_receiver_change:
                return PrinterService::orderReceiverChangePrintTicket($xhsOrder);
            case NotifyMsgTagEnums::msg_after_sale_create:
                return PrinterService::orderAfterSaleCreatePrintTicket($xhsOrder);
            default:
                return true;
        }
    }
}
