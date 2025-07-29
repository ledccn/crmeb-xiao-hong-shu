<?php

namespace Ledc\CrmebXiaoHongShu\model;

/**
 * eb_xhs_order_product XHS小红书订单商品SKU表
 * @property integer $id (主键)
 * @property integer $oid 内部订单主键
 * @property string $order_id 订单号
 * @property string $sku_id 商品id（SKU）
 * @property string $sku_name 商品名称（SKU）
 * @property string $erp_code 商家编码
 * @property string $sku_spec 规格
 * @property string $sku_image 规格
 * @property integer $sku_quantity 商品数量
 * @property string $sku_detail_list 商品sku信息列表,单品非渠道商品为自身信息，组合品为子商品信息，多包组和渠道商品为其对应非渠道单品信息
 * @property string $sku_identify_code_info 商品序列号等信息，仅部分类目的国补订单存在
 * @property integer $total_paid_amount 总支付金额（考虑总件数）商品总实付
 * @property integer $total_merchant_discount 商家承担总优惠
 * @property integer $total_red_discount 平台承担总优惠
 * @property integer $total_tax_amount 商品税金
 * @property integer $total_net_weight 商品总净重
 * @property integer $sku_tag 是否赠品：1赠品 0普通商品
 * @property integer $is_channel 是否是渠道商品
 * @property integer $delivery_mode 是否支持无物流发货, 1支持无物流发货 0不支持无物流发货
 * @property string $kol_id 达人id
 * @property string $kol_name 达人名称
 * @property integer $sku_after_sale_status Sku售后状态 1无售后 2售后处理中 3售后完成 4售后拒绝 5售后关闭 6平台介入中 7售后取消
 * @property string $item_id 商品id
 * @property string $item_name 商品名称
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
trait HasEbXhsOrderProduct
{
}
