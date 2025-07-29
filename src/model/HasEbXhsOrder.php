<?php

namespace Ledc\CrmebXiaoHongShu\model;

/**
 * eb_xhs_order XHS小红书订单表
 * @property integer $id (主键)
 * @property string $order_id 订单号
 * @property integer $order_type 订单类型
 * @property integer $order_status 订单状态
 * @property integer $order_after_sales_status 售后状态
 * @property integer $cancel_status 申请取消状态
 * @property integer $paid_time 支付时间
 * @property integer $delivery_time 订单发货时间
 * @property integer $cancel_time 订单取消时间
 * @property integer $finish_time 订单完成时间
 * @property integer $promise_last_delivery_time 承诺最晚发货时间
 * @property string $receiver_province_id 收件人省份id
 * @property string $receiver_province_name 收件人省份
 * @property string $receiver_city_id 收件人城市id
 * @property string $receiver_city_name 收件人城市
 * @property string $receiver_district_id 收件人区县id
 * @property string $receiver_district_name 收件人区县名称
 * @property string $receiver_name 收件人姓名
 * @property string $receiver_phone 收件人手机
 * @property string $receiver_address 收件人地址
 * @property string $open_address_id 收件人姓名+手机+地址等计算得出，用来查询收件人详情
 * @property integer $order_seq 订单流水号（方便骑手找单）
 * @property string $receiver_longitude 收件人经度
 * @property string $receiver_latitude 收件人纬度
 * @property integer $receiver_lbs_type 收件人坐标类型 0:百度坐标系，1:国测局标准坐标系
 * @property integer $expected_finished_time 预期送达时间
 * @property string $expected_finished_start_time 预期送达开始时间
 * @property string $expected_finished_end_time 预期送达结束时间
 * @property string $customer_remark 用户备注
 * @property string $seller_remark 商家标记备注
 * @property integer $seller_remark_flag 商家标记优先级，旗子颜色 1灰旗 2红旗 3黄旗 4绿旗 5蓝旗 6紫旗
 * @property integer $presale_delivery_start_time 预售最早发货时间
 * @property integer $presale_delivery_end_time 预售最晚发货时间
 * @property string $original_order_id 原始关联订单号(退换订单的原订单)
 * @property integer $total_net_weight_amount 订单商品总净重 单位g
 * @property integer $total_pay_amount 订单实付金额(包含运费和定金)
 * @property integer $total_shipping_free 订单实付运费
 * @property integer $unpack 是否拆包 true已拆包 false未拆包
 * @property string $express_tracking_no 快递单号
 * @property string $express_company_code 快递公司编码
 * @property string $simple_delivery_order_list 拆包信息节点
 * @property string $order_tag_list 订单标签列表
 * @property string $plan_info_id 物流方案id
 * @property string $plan_info_name 物流方案名称
 * @property string $logistics 物流模式red_express三方备货直邮(备货海外仓),red_domestic_trade(三方备货内贸),red_standard(三方备货保税仓),red_auto(三方自主发货),red_box(三方小包),red_bonded(三方保税)
 * @property integer $logistics_mode 物流模式 1: 普通内贸 2：保税bbc 3: 直邮bc 4:行邮cc
 * @property integer $total_deposit_amount 订单定金
 * @property integer $total_merchant_discount 商家承担总优惠金额
 * @property integer $total_red_discount 平台承担总优惠金额
 * @property integer $merchant_actual_receive_amount 商家实收(=用户支付金额+定金+平台优惠)
 * @property integer $total_change_price_amount 改价总金额
 * @property integer $payment_type 支付方式 1:支付宝 2:微信 3:apple内购 4:applePay 5:花呗分期 7:支付宝免密支付 8:云闪付 -1:其他
 * @property integer $out_promotion_amount 支付渠道优惠金额
 * @property string $out_trade_no 三方支付渠道单号 举例:支付宝/微信/云闪付等单号 和paymentType对应
 * @property string $shop_id 店铺id
 * @property string $shop_name 店铺名称
 * @property string $user_id 用户id
 * @property integer $created_time XHS创建时间
 * @property integer $updated_time XHS更新时间
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
trait HasEbXhsOrder
{
}
