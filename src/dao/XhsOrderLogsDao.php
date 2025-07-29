<?php

namespace Ledc\CrmebXiaoHongShu\dao;

use app\dao\BaseDao;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderLogs;

/**
 * XHS小红书订单日志数据访问层
 */
class XhsOrderLogsDao extends BaseDao
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return EbXhsOrderLogs::class;
    }
}
