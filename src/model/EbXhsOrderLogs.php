<?php

namespace Ledc\CrmebXiaoHongShu\model;

use think\db\BaseQuery;
use think\db\Query;
use think\Model;

/**
 * XHS小红书订单日志表
 */
class EbXhsOrderLogs extends Model
{
    use HasEbXhsOrderLogs;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'eb_xhs_order_logs';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    /**
     * 只读字段
     * @var string[]
     */
    protected $readonly = ['oid', 'order_id'];

    /**
     * 根据内部订单主键查询
     * @param int $oid 内部订单主键
     * @return Model|BaseQuery|Query
     */
    public static function queryByOid(int $oid): Query
    {
        return self::where('oid', $oid);
    }
}
