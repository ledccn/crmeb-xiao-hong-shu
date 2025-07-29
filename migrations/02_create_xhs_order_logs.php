<?php

use Ledc\ThinkModelTrait\Contracts\Column;
use think\migration\Migrator;

/**
 * 创建XHS小红书订单日志表
 */
class CreateXhsOrderLogs extends Migrator
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('xhs_order_logs', ['comment' => 'XHS小红书订单日志表', 'signed' => false]);
        $table->addColumn(Column::unsignedInteger('oid')->setComment('内部订单主键')->setNull(false))
            ->addColumn(Column::string('order_id', 50)->setComment('订单号')->setNull(false))
            ->addColumn(Column::string('operator', 50)->setComment('操作人名称')->setNull(false))
            ->addColumn(Column::string('action', 50)->setComment('行为')->setNull(false))
            ->addColumn(Column::text('content')->setComment('内容')->setNull(true))
            ->addColumn(Column::makeCreateTime())
            ->addForeignKey('oid', 'xhs_order', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addIndex('order_id')
            ->addIndex('action')
            ->create();
    }
}
