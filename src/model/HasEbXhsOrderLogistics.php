<?php

namespace Ledc\CrmebXiaoHongShu\model;

/**
 * eb_xhs_order_logistics XHS小红书订单物流表
 * @property integer $id (主键)
 * @property integer $oid 内部订单主键
 * @property string $order_id 小红书订单号
 * @property string $receiver_longitude 收件人经度
 * @property string $receiver_latitude 收件人纬度
 * @property integer $receiver_lbs_type 收件人坐标类型 0:百度坐标系，1:国测局标准坐标系
 * @property string $sku_list 商品信息
 * @property integer $trans_processed 是否呼叫骑手
 * @property integer $trans_order_status 同城配送状态
 * @property string $service_store_id 同城配送门店编号
 * @property string $service_order_id 同城配送订单编号
 * @property string $service_order_status 同城配送订单状态
 * @property string $service_order_sub_status 同城配送订单子状态
 * @property string $service_trans_id 同城配送运力
 * @property string $trans_order_id 同城配送运力订单号
 * @property string $trans_waybill_id 同城配送运力配送单号
 * @property string $trans_pickup_password 同城配送取件密码（商家验证）
 * @property string $trans_delivery_password 同城配送收件密码（收货人验证）
 * @property integer $trans_distance 同城配送距离(米)
 * @property integer $trans_fee 同城配送费用(分)
 * @property integer $trans_order_create_time 同城配送运力单创建时间
 * @property integer $trans_order_update_time 同城配送运力单更新时间
 * @property integer $trans_order_cancel_time 同城配送运力单取消时间
 * @property integer $trans_order_accept_time 同城配送配送员接单时间
 * @property integer $trans_order_fetch_time 同城配送配送员取货时间
 * @property integer $trans_order_finish_time 同城配送配送员送达时间
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
trait HasEbXhsOrderLogistics
{
}
