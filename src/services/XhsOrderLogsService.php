<?php

namespace Ledc\CrmebXiaoHongShu\services;

use app\services\BaseServices;
use Ledc\CrmebXiaoHongShu\dao\XhsOrderLogsDao;
use ReflectionException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * XHS小红书订单日志服务层
 */
class XhsOrderLogsService extends BaseServices
{
    /**
     * @var XhsOrderLogsDao
     */
    protected $dao;

    /**
     * @param XhsOrderLogsDao $dao
     */
    public function __construct(XhsOrderLogsDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @return XhsOrderLogsDao
     */
    public function getDao(): XhsOrderLogsDao
    {
        return $this->dao;
    }

    /**
     * 获取列表
     * @param array $where
     * @param string $order
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws ReflectionException
     */
    public function getList(array $where = [], string $order = 'id DESC'): array
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->selectList($where, '*', $page, $limit, $order);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }
}
