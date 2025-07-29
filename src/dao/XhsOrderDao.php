<?php

namespace Ledc\CrmebXiaoHongShu\dao;

use app\dao\BaseDao;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;

/**
 * XHS小红书订单数据访问层
 */
class XhsOrderDao extends BaseDao
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return EbXhsOrder::class;
    }
}
