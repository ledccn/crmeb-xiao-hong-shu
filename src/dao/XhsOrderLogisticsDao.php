<?php

namespace Ledc\CrmebXiaoHongShu\dao;

use app\dao\BaseDao;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderLogistics;

/**
 * XHS小红书订单物流数据访问层
 */
class XhsOrderLogisticsDao extends BaseDao
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return EbXhsOrderLogistics::class;
    }
}
