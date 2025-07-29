<?php

namespace Ledc\CrmebXiaoHongShu\dao;

use app\dao\BaseDao;
use Ledc\CrmebXiaoHongShu\model\EbXhsCategories;

/**
 * XHS小红书分类数据访问层
 */
class XhsCategoriesDao extends BaseDao
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return EbXhsCategories::class;
    }
}
