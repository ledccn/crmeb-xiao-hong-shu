<?php

namespace Ledc\CrmebXiaoHongShu\jobs;

use Ledc\CrmebXiaoHongShu\services\XhsOrderService;
use Ledc\ThinkModelTrait\Contracts\HasJobs;

/**
 * 同步订单任务
 */
class SyncOrderJobs
{
    use HasJobs;

    /**
     * 执行
     */
    public function execute(): bool
    {
        XhsOrderService::scheduler();
        return true;
    }
}
