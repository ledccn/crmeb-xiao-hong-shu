<?php

namespace Ledc\CrmebXiaoHongShu\jobs;

use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\CrmebXiaoHongShu\services\XhsOrderService;
use Ledc\ThinkModelTrait\Contracts\HasJobs;

/**
 * 获取&解密订单收货人信息
 */
class SyncOrderReceiverInfoJobs
{
    use HasJobs;

    /**
     * @param string $order_id
     * @param bool $force
     * @return bool|null
     */
    public function execute(string $order_id = '', bool $force = false): ?bool
    {
        $xhsOrder = EbXhsOrder::uniqueQuery($order_id);
        if ($xhsOrder) {
            XhsOrderService::syncOrderReceiverInfo($xhsOrder, $force);
        }
        return true;
    }
}
