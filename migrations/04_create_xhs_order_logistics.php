<?php

use Ledc\ThinkModelTrait\Contracts\Column;
use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;

/**
 * 创建XHS小红书订单物流表
 */
class CreateXhsOrderLogistics extends Migrator
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('xhs_order_logistics', ['comment' => 'XHS小红书订单物流表', 'signed' => false]);
        $table->addColumn(Column::unsignedInteger('oid')->setComment('内部订单主键')->setNull(false))
            ->addColumn(Column::string('order_id', 50)->setComment('小红书订单号')->setNull(false))
            ->addColumn(Column::string('receiver_longitude', 30)->setComment('收件人经度')->setDefault('')->setNull(false))
            ->addColumn(Column::string('receiver_latitude', 30)->setComment('收件人纬度')->setDefault('')->setNull(false))
            ->addColumn(Column::unsignedTinyInteger('receiver_lbs_type')->setComment('收件人坐标类型 0:百度坐标系，1:国测局标准坐标系')->setDefault(1))
            ->addColumn(Column::text('sku_list')->setComment('商品信息')->setNull(true))
            ->addColumn(Column::boolean('trans_processed')->setComment('是否呼叫骑手')->setNull(false)->setDefault(0))
            ->addColumn(Column::tinyInteger('trans_order_status')->setComment('同城配送状态')->setNull(false)->setDefault(0)->setSigned(false))
            ->addColumn('service_store_id', AdapterInterface::PHINX_TYPE_STRING, ['comment' => '同城配送门店编号', 'null' => false, 'limit' => 50, 'default' => ''])
            ->addColumn('service_order_id', AdapterInterface::PHINX_TYPE_STRING, ['comment' => '同城配送订单编号', 'null' => true, 'limit' => 50])
            ->addColumn('service_order_status', AdapterInterface::PHINX_TYPE_STRING, ['comment' => '同城配送订单状态', 'null' => true, 'limit' => 20])
            ->addColumn('service_order_sub_status', AdapterInterface::PHINX_TYPE_STRING, ['comment' => '同城配送订单子状态', 'null' => true, 'limit' => 20])
            ->addColumn('service_trans_id', AdapterInterface::PHINX_TYPE_STRING, ['comment' => '同城配送运力', 'null' => false, 'limit' => 50, 'default' => ''])
            ->addColumn('trans_order_id', AdapterInterface::PHINX_TYPE_STRING, ['comment' => '同城配送运力订单号', 'null' => true, 'limit' => 50])
            ->addColumn('trans_waybill_id', AdapterInterface::PHINX_TYPE_STRING, ['comment' => '同城配送运力配送单号', 'null' => false, 'limit' => 50, 'default' => ''])
            ->addColumn('trans_pickup_password', AdapterInterface::PHINX_TYPE_STRING, ['comment' => '同城配送取件密码（商家验证）', 'null' => false, 'limit' => 20, 'default' => ''])
            ->addColumn('trans_delivery_password', AdapterInterface::PHINX_TYPE_STRING, ['comment' => '同城配送收件密码（收货人验证）', 'null' => false, 'limit' => 20, 'default' => ''])
            ->addColumn('trans_distance', AdapterInterface::PHINX_TYPE_INTEGER, ['comment' => '同城配送距离(米)', 'null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false])
            ->addColumn('trans_fee', AdapterInterface::PHINX_TYPE_INTEGER, ['comment' => '同城配送费用(分)', 'null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'signed' => false])
            ->addColumn(Column::unsignedInteger('trans_order_create_time')->setComment('同城配送运力单创建时间')->setNull(false)->setDefault(0))
            ->addColumn(Column::unsignedInteger('trans_order_update_time')->setComment('同城配送运力单更新时间')->setNull(false)->setDefault(0))
            ->addColumn(Column::unsignedInteger('trans_order_cancel_time')->setComment('同城配送运力单取消时间')->setNull(false)->setDefault(0))
            ->addColumn(Column::unsignedInteger('trans_order_accept_time')->setComment('同城配送配送员接单时间')->setNull(false)->setDefault(0))
            ->addColumn(Column::unsignedInteger('trans_order_fetch_time')->setComment('同城配送配送员取货时间')->setNull(false)->setDefault(0))
            ->addColumn(Column::unsignedInteger('trans_order_finish_time')->setComment('同城配送配送员送达时间')->setNull(false)->setDefault(0))
            ->addColumn(Column::makeCreateTime())
            ->addColumn(Column::makeUpdateTime())
            ->addForeignKey('oid', 'xhs_order', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addIndex('order_id')
            ->addIndex('trans_processed')
            ->addIndex('trans_order_status')
            ->addIndex('service_store_id')
            ->addIndex(['service_order_id'], ['unique' => true])
            ->addIndex(['trans_order_id'], ['unique' => true])
            ->addIndex('trans_order_create_time')
            ->create();
    }
}
