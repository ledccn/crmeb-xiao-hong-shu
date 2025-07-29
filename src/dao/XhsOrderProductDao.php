<?php

namespace Ledc\CrmebXiaoHongShu\dao;

use app\dao\BaseDao;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderProduct;

/**
 * XHS小红书订单商品SKU数据访问层
 */
class XhsOrderProductDao extends BaseDao
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return EbXhsOrderProduct::class;
    }
}
