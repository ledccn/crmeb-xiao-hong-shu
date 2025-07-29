<?php

namespace Ledc\CrmebXiaoHongShu\jobs;

use Ledc\CrmebXiaoHongShu\services\XhsCategoriesService;
use Ledc\ThinkModelTrait\Contracts\HasJobs;

/**
 * 同步分类
 */
class SyncCategoriesJobs
{
    use HasJobs;

    /**
     * 执行
     */
    public function execute(): bool
    {
        XhsCategoriesService::scheduler();
        return true;
    }
}