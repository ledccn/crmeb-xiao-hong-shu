<?php

namespace Ledc\CrmebXiaoHongShu\services;

use app\services\BaseServices;
use Ledc\CrmebXiaoHongShu\dao\XhsOrderLogisticsDao;
use ReflectionException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * XHS小红书订单物流服务层
 */
class XhsOrderLogisticsService extends BaseServices
{
    /**
     * @var XhsOrderLogisticsDao
     */
    protected $dao;

    /**
     * @param XhsOrderLogisticsDao $dao
     */
    public function __construct(XhsOrderLogisticsDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @return XhsOrderLogisticsDao
     */
    public function getDao(): XhsOrderLogisticsDao
    {
        return $this->dao;
    }

    /**
     * 获取列表
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException|ReflectionException
     */
    public function getList(array $where = []): array
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->selectList($where, '*', $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }
}
