<?php

namespace Ledc\CrmebXiaoHongShu\model;

use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\db\Query;
use think\Model;

/**
 * XHS小红书订单商品SKU表
 */
class EbXhsOrderProduct extends Model
{
    use HasEbXhsOrderProduct;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'eb_xhs_order_product';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    /**
     * 字段类型
     * @var string[]
     */
    protected $type = [
        'sku_detail_list' => 'array',
        'sku_identify_code_info' => 'array',
    ];

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
        return (new static)->db()->where('oid', $oid);
    }

    /**
     * 获取订单商品SKU
     * @param int $oid 内部订单主键
     * @param string $skuId SKU ID
     * @return self|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function querySkuByOid(int $oid, string $skuId): ?self
    {
        return (new static)->db()->where('oid', $oid)->where('sku_id', $skuId)->find();
    }

    /**
     * 获取订单商品SKU
     * @param string $orderId 外部订单号
     * @param string $skuId SKU ID
     * @return self|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function querySkuByOrderId(string $orderId, string $skuId): ?self
    {
        return (new static)->db()->where('order_id', $orderId)->where('sku_id', $skuId)->find();
    }
}
