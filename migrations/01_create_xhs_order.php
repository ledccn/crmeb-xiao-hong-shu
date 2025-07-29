<?php

use Ledc\ThinkModelTrait\Contracts\Column;
use think\migration\Migrator;

/**
 * 创建XHS小红书订单表
 */
class CreateXhsOrder extends Migrator
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('xhs_order', ['comment' => 'XHS小红书订单表', 'signed' => false]);
        $table->addColumn(Column::string('order_id', 50)->setComment('订单号')->setNull(false))
            ->addColumn(Column::smallInteger('order_type')->setComment('订单类型')->setNull(false))
            ->addColumn(Column::mediumInteger('order_status')->setComment('订单状态')->setNull(false))
            ->addColumn(Column::smallInteger('order_after_sales_status')->setComment('售后状态')->setNull(false))
            ->addColumn(Column::smallInteger('cancel_status')->setComment('申请取消状态')->setNull(false))
            ->addColumn(Column::unsignedInteger('paid_time')->setComment('支付时间')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('delivery_time')->setComment('订单发货时间')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('cancel_time')->setComment('订单取消时间')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('finish_time')->setComment('订单完成时间')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('promise_last_delivery_time')->setComment('承诺最晚发货时间')->setDefault(0)->setNull(false))
            ->addColumn(Column::string('receiver_province_id', 200)->setComment('收件人省份id')->setDefault('')->setNull(false))
            ->addColumn(Column::string('receiver_province_name', 200)->setComment('收件人省份')->setDefault('')->setNull(false))
            ->addColumn(Column::string('receiver_city_id', 200)->setComment('收件人城市id')->setDefault('')->setNull(false))
            ->addColumn(Column::string('receiver_city_name', 200)->setComment('收件人城市')->setDefault('')->setNull(false))
            ->addColumn(Column::string('receiver_district_id', 200)->setComment('收件人区县id')->setDefault('')->setNull(false))
            ->addColumn(Column::string('receiver_district_name', 200)->setComment('收件人区县名称')->setDefault('')->setNull(false))
            ->addColumn(Column::string('receiver_name', 100)->setComment('收件人姓名')->setDefault('')->setNull(false))
            ->addColumn(Column::string('receiver_phone', 50)->setComment('收件人手机')->setDefault('')->setNull(false))
            ->addColumn(Column::string('receiver_address', 300)->setComment('收件人地址')->setDefault('')->setNull(false))
            ->addColumn(Column::string('open_address_id', 50)->setComment('收件人姓名+手机+地址等计算得出，用来查询收件人详情')->setDefault('')->setNull(false))
            ->addColumn(Column::unsignedMediumInteger('order_seq')->setComment('订单流水号')->setNull(false)->setDefault(0))
            ->addColumn(Column::string('receiver_longitude', 30)->setComment('收件人经度')->setDefault('')->setNull(false))
            ->addColumn(Column::string('receiver_latitude', 30)->setComment('收件人纬度')->setDefault('')->setNull(false))
            ->addColumn(Column::unsignedTinyInteger('receiver_lbs_type')->setComment('收件人坐标类型 0:百度坐标系，1:国测局标准坐标系')->setDefault(1))
            ->addColumn(Column::unsignedInteger('expected_finished_time')->setComment('预期送达时间')->setDefault(0)->setNull(false))
            ->addColumn(Column::dateTime('expected_finished_start_time')->setComment('预期送达开始时间')->setNull(true))
            ->addColumn(Column::dateTime('expected_finished_end_time')->setComment('预期送达结束时间')->setNull(true))
            ->addColumn(Column::string('customer_remark', 200)->setComment('用户备注')->setDefault('')->setNull(false))
            ->addColumn(Column::string('seller_remark', 200)->setComment('商家标记备注')->setDefault('')->setNull(false))
            ->addColumn(Column::smallInteger('seller_remark_flag')->setComment('商家标记优先级，旗子颜色 1灰旗 2红旗 3黄旗 4绿旗 5蓝旗 6紫旗')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('presale_delivery_start_time')->setComment('预售最早发货时间')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('presale_delivery_end_time')->setComment('预售最晚发货时间')->setDefault(0)->setNull(false))
            ->addColumn(Column::string('original_order_id', 50)->setComment('原始关联订单号(退换订单的原订单)')->setDefault('')->setNull(false))
            ->addColumn(Column::unsignedInteger('total_net_weight_amount')->setComment('订单商品总净重 单位g')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('total_pay_amount')->setComment('订单实付金额(包含运费和定金)')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('total_shipping_free')->setComment('订单实付运费')->setDefault(0)->setNull(false))
            ->addColumn(Column::boolean('unpack')->setComment('是否拆包 true已拆包 false未拆包')->setDefault(0)->setNull(false))
            ->addColumn(Column::string('express_tracking_no', 50)->setComment('快递单号')->setDefault('')->setNull(false))
            ->addColumn(Column::string('express_company_code', 50)->setComment('快递公司编码')->setDefault('')->setNull(false))
            ->addColumn(Column::text('simple_delivery_order_list')->setComment('拆包信息节点')->setNull(true))
            ->addColumn(Column::text('order_tag_list')->setComment('订单标签列表')->setNull(true))
            ->addColumn(Column::string('plan_info_id', 40)->setComment('物流方案id')->setDefault('')->setNull(false))
            ->addColumn(Column::string('plan_info_name', 200)->setComment('物流方案名称')->setDefault('')->setNull(false))
            ->addColumn(Column::string('logistics', 50)->setComment('物流模式red_express三方备货直邮(备货海外仓),red_domestic_trade(三方备货内贸),red_standard(三方备货保税仓),red_auto(三方自主发货),red_box(三方小包),red_bonded(三方保税)')->setDefault('')->setNull(false))
            ->addColumn(Column::smallInteger('logistics_mode')->setComment('物流模式 1: 普通内贸 2：保税bbc 3: 直邮bc 4:行邮cc')->setDefault(1)->setNull(false))
            ->addColumn(Column::unsignedInteger('total_deposit_amount')->setComment('订单定金')->setDefault(0)->setNull(false))
            ->addColumn(Column::integer('total_merchant_discount')->setComment('商家承担总优惠金额')->setDefault(0)->setNull(false))
            ->addColumn(Column::integer('total_red_discount')->setComment('平台承担总优惠金额')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('merchant_actual_receive_amount')->setComment('商家实收(=用户支付金额+定金+平台优惠)')->setDefault(0)->setNull(false))
            ->addColumn(Column::integer('total_change_price_amount')->setComment('改价总金额')->setDefault(0)->setNull(false))
            ->addColumn(Column::smallInteger('payment_type')->setComment('支付方式 1:支付宝 2:微信 3:apple内购 4:applePay 5:花呗分期 7:支付宝免密支付 8:云闪付 -1:其他')->setNull(false))
            ->addColumn(Column::integer('out_promotion_amount')->setComment('支付渠道优惠金额')->setDefault(0)->setNull(false))
            ->addColumn(Column::string('out_trade_no', 80)->setComment('三方支付渠道单号 举例:支付宝/微信/云闪付等单号 和paymentType对应')->setDefault('')->setNull(false))
            ->addColumn(Column::string('shop_id', 50)->setComment('店铺id')->setDefault('')->setNull(false))
            ->addColumn(Column::string('shop_name', 100)->setComment('店铺名称')->setDefault('')->setNull(false))
            ->addColumn(Column::string('user_id', 50)->setComment('用户id')->setDefault('')->setNull(false))
            ->addColumn(Column::unsignedInteger('created_time')->setComment('XHS创建时间')->setDefault(0)->setNull(false))
            ->addColumn(Column::unsignedInteger('updated_time')->setComment('XHS更新时间')->setDefault(0)->setNull(false))
            ->addColumn(Column::makeCreateTime())
            ->addColumn(Column::makeUpdateTime())
            ->addIndex(['order_id'], ['name' => 'order_id', 'unique' => true])
            ->addIndex('order_type')
            ->addIndex('order_status')
            ->addIndex('order_after_sales_status')
            ->addIndex('cancel_status')
            ->addIndex('promise_last_delivery_time')
            ->addIndex('receiver_phone')
            ->addIndex('express_tracking_no')
            ->addIndex('payment_type')
            ->addIndex('expected_finished_time')
            ->addIndex('shop_id')
            ->addIndex('user_id')
            ->addIndex('created_time')
            ->addIndex('updated_time')
            ->create();
    }
}
