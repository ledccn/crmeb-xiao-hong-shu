<?php

namespace Ledc\CrmebXiaoHongShu\jobs;

use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\CrmebXiaoHongShu\services\PrinterService;
use Ledc\ThinkModelTrait\Contracts\HasJobs;
use Throwable;

/**
 * 小红书打印订单任务（异步打印）
 */
class PrintOrderJobs
{
    use HasJobs;

    /**
     * 打印订单
     * @param string $order_id 小红书订单ID
     * @return bool
     */
    public function execute(string $order_id = ''): bool
    {
        try {
            $xhsOrder = EbXhsOrder::uniqueQuery($order_id);
            if ($xhsOrder) {
                return PrinterService::orderPrintTicket($xhsOrder);
            }
        } catch (Throwable $e) {
            return false;
        }
        return true;
    }
}
