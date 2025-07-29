<?php

use Ledc\ThinkModelTrait\Contracts\Column;
use think\migration\Migrator;

/**
 * 创建XHS小红书订单商品SKU表
 */
class CreateXhsOrderProduct extends Migrator
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('xhs_order_product', ['comment' => 'XHS小红书订单商品SKU表', 'signed' => false]);
        $table->addColumn(Column::unsignedInteger('oid')->setComment('内部订单主键')->setNull(false))
            ->addColumn(Column::string('order_id', 50)->setComment('订单号')->setNull(false))
            ->addColumn(Column::string('sku_id', 50)->setComment('商品id（SKU）')->setNull(false))
            ->addColumn(Column::string('sku_name')->setComment('商品名称（SKU）')->setNull(false))
            ->addColumn(Column::string('erp_code', 50)->setComment('商家编码')->setNull(false))
            ->addColumn(Column::string('sku_spec')->setComment('规格')->setNull(false))
            ->addColumn(Column::string('sku_image')->setComment('规格')->setNull(false))
            ->addColumn(Column::unsignedMediumInteger('sku_quantity')->setComment('商品数量')->setNull(false))
            ->addColumn(Column::text('sku_detail_list')->setComment('商品sku信息列表,单品非渠道商品为自身信息，组合品为子商品信息，多包组和渠道商品为其对应非渠道单品信息')->setNull(false))
            ->addColumn(Column::text('sku_identify_code_info')->setComment('商品序列号等信息，仅部分类目的国补订单存在')->setNull(true))
            ->addColumn(Column::unsignedInteger('total_paid_amount')->setComment('总支付金额（考虑总件数）商品总实付')->setDefault(0)->setNull(false))
            ->addColumn(Column::integer('total_merchant_discount')->setComment('商家承担总优惠')->setDefault(0)->setNull(false))
            ->addColumn(Column::integer('total_red_discount')->setComment('平台承担总优惠')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('total_tax_amount')->setComment('商品税金')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('total_net_weight')->setComment('商品总净重')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedTinyInteger('sku_tag')->setComment('是否赠品：1赠品 0普通商品')->setDefault(0)->setNull(false))
            ->addColumn(Column::boolean('is_channel')->setComment('是否是渠道商品')->setDefault(0)->setNull(false))
            ->addColumn(Column::tinyInteger('delivery_mode')->setComment('是否支持无物流发货, 1支持无物流发货 0不支持无物流发货')->setDefault(0)->setNull(false))
            ->addColumn(Column::string('kol_id', 50)->setComment('达人id')->setNull(false))
            ->addColumn(Column::string('kol_name', 50)->setComment('达人名称')->setNull(false))
            ->addColumn(Column::smallInteger('sku_after_sale_status')->setComment('Sku售后状态 1无售后 2售后处理中 3售后完成 4售后拒绝 5售后关闭 6平台介入中 7售后取消')->setNull(false))
            ->addColumn(Column::string('item_id', 50)->setComment('商品id')->setNull(false))
            ->addColumn(Column::string('item_name')->setComment('商品名称')->setNull(false))
            ->addColumn(Column::makeCreateTime())
            ->addColumn(Column::makeUpdateTime())
            ->addForeignKey('oid', 'xhs_order', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addIndex('order_id')
            ->addIndex('sku_id')
            ->addIndex('item_id')
            ->addIndex('sku_after_sale_status')
            ->addIndex('delivery_mode')
            ->create();
    }
}
