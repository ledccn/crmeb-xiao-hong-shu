<?php

namespace Ledc\CrmebXiaoHongShu\model;

use Ledc\XiaoHongShu\Enums\OrderAfterSalesStatusEnums;
use Ledc\XiaoHongShu\Enums\OrderStatusEnums;
use think\Model;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

/**
 * XHS小红书订单表
 */
class EbXhsOrder extends Model
{
    use HasEbXhsOrder;

    /**
     * 可排序的字段
     */
    public const ORDER_FIELD_MAP = ['id', 'expected_finished_time', 'created_time', 'updated_time', 'promise_last_delivery_time', 'trans_order_status'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'eb_xhs_order';

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
        'simple_delivery_order_list' => 'array',
        'order_tag_list' => 'array',
    ];

    /**
     * 追加字段
     * @var string[]
     */
    protected $append = ['can_delivery'];

    /**
     * 【获取器】是否允许发货
     * @return array|mixed
     */
    public function getCanDeliveryAttr(): bool
    {
        return OrderStatusEnums::canDelivery($this->order_status) &&
            !OrderAfterSalesStatusEnums::existsAfterSales($this->order_after_sales_status) &&
            !$this->cancel_status &&
            $this->paid_time &&
            !$this->unpack &&
            $this->logistics === 'red_auto' &&
            $this->logistics_mode === 1;
    }

    /**
     * 【唯一查询】获取订单数据模型
     * @param string $order_id
     * @return self|null
     */
    public static function uniqueQuery(string $order_id): ?self
    {
        $model = self::where('order_id', $order_id)->findOrEmpty();
        return $model->isEmpty() ? null : $model;
    }

    /**
     * 【一对多】订单商品SKU
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(EbXhsOrderProduct::class, 'oid', 'id');
    }

    /**
     * 【一对多】订单日志
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(EbXhsOrderLogs::class, 'oid', 'id');
    }

    /**
     * 【一对多】订单物流信息（同城配送、快递、物流）
     * @return HasMany
     */
    public function express(): HasMany
    {
        return $this->hasMany(EbXhsOrderLogistics::class, 'oid', 'id');
    }

    /**
     * 【一对一】订单物流信息（同城配送、快递、物流）
     * @return HasOne
     */
    public function delivery(): HasOne
    {
        return $this->hasOne(EbXhsOrderLogistics::class, 'oid', 'id');
    }

    /**
     * 【模型事件】新增前
     * @param self $model
     * @return bool|null
     */
    public static function onBeforeInsert(self $model): ?bool
    {
        if (!$model->order_seq) {
            $model->order_seq = generate_order_seq();
        }
        return true;
    }

    /**
     * 【模型事件】新增后
     * @param self $model
     * @return void
     */
    public static function onAfterInsert(self $model): void
    {
        $xhsOrderLogistics = new EbXhsOrderLogistics();
        $xhsOrderLogistics->oid = $model->id;
        $xhsOrderLogistics->order_id = $model->order_id;
        $xhsOrderLogistics->save();
    }
}
