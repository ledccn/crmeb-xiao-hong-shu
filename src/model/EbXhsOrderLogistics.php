<?php

namespace Ledc\CrmebXiaoHongShu\model;

use Ledc\CrmebIntraCity\enums\TransOrderStatusEnums;
use think\db\BaseQuery;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\db\Query;
use think\Model;
use think\model\Collection;
use think\model\relation\HasOne;

/**
 * eb_xhs_order_logistics XHS小红书订单物流表
 */
class EbXhsOrderLogistics extends Model
{
    use HasEbXhsOrderLogistics;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'eb_xhs_order_logistics';

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
     * 字段类型
     * @var string[]
     */
    protected $type = [
        'sku_list' => 'array',
    ];

    /**
     * 追加字段
     * @var string[]
     */
    protected $append = ['can_cancel_delivery'];

    /**
     * 【获取器】是否允许取消发货
     * @return array|mixed
     */
    public function getCanCancelDeliveryAttr(): bool
    {
        return $this->trans_processed && TransOrderStatusEnums::isAllowCancel($this->trans_order_status);
    }

    /**
     * 【一对一】XHS小红书订单
     * @return HasOne
     */
    public function xiaohongshu(): HasOne
    {
        return $this->hasOne(EbXhsOrder::class, 'id', 'oid');
    }

    /**
     * 获取XHS小红书订单
     * @return EbXhsOrder
     */
    public function getXhsOrder(): EbXhsOrder
    {
        return EbXhsOrder::findOrEmpty($this->oid);
    }

    /**
     * 唯一查询
     * @param string $service_order_id 同城配送订单编号
     * @return self|null
     */
    public static function uniqueQueryByServiceOrderId(string $service_order_id): ?self
    {
        $model = self::where('service_order_id', $service_order_id)->findOrEmpty();
        return $model->isEmpty() ? null : $model;
    }

    /**
     * 唯一查询
     * @param string $trans_order_id 同城配送运力订单号
     * @return self|null
     */
    public static function uniqueQueryByTransOrderId(string $trans_order_id): ?self
    {
        $model = self::where('trans_order_id', $trans_order_id)->findOrEmpty();
        return $model->isEmpty() ? null : $model;
    }

    /**
     * 根据XHS小红书订单号查询
     * @param string $order_id XHS小红书订单号
     * @return self|null
     */
    public static function findByOrderId(string $order_id): ?self
    {
        $model = self::where('order_id', $order_id)->findOrEmpty();
        return $model->isEmpty() ? null : $model;
    }

    /**
     * 根据内部订单主键查询
     * @param int $oid
     * @return self|null
     */
    public static function findByOid(int $oid): ?self
    {
        $model = self::queryByOid($oid)->findOrEmpty();
        return $model->isEmpty() ? null : $model;
    }

    /**
     * 根据内部订单主键查询
     * @param int $oid
     * @return Collection|self[]|array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function selectByOid(int $oid): Collection
    {
        return self::queryByOid($oid)->select();
    }

    /**
     * 根据内部订单主键查询
     * @param int $oid 内部订单主键
     * @return Query|Model|BaseQuery
     */
    protected static function queryByOid(int $oid): Query
    {
        return self::where('oid', $oid);
    }
}
