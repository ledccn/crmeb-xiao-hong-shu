<?php

use think\migration\db\Column;
use think\migration\Migrator;

/**
 * 创建XHS小红书分类表
 */
class CreateXhsCategories extends Migrator
{
    /**
     * Change Method.
     */
    public function change()
    {
        $table = $this->table('xhs_categories', ['comment' => 'XHS小红书分类表', 'signed' => false]);
        $table->addColumn(Column::unsignedInteger('pid')->setComment('父级ID')->setDefault(0)->setNull(false))
            ->addColumn(Column::string('category_id', 40)->setComment('分类ID')->setNull(false))
            ->addColumn(Column::string('zh_name', 200)->setComment('分类中文名')->setNull(false)->setDefault(''))
            ->addColumn(Column::string('en_name', 200)->setComment('分类英文名')->setNull(false)->setDefault(''))
            ->addColumn(Column::boolean('is_leaf')->setComment('是否是叶子类目')->setNull(false)->setDefault( 0))
            ->addColumn(Column::boolean('support_size_table')->setComment('是否支持基础尺码图')->setNull(false)->setDefault( 0))
            ->addColumn(Column::boolean('support_recommend_size_table')->setComment('是否支持尺码推荐图')->setNull(false)->setDefault( 0))
            ->addColumn(Column::boolean('support_model_try_on_size_table')->setComment('是否支持模特试穿图')->setNull(false)->setDefault( 0))
            ->addColumn(Column::boolean('support_main_spec_image')->setComment('是否支持规格大图')->setNull(false)->setDefault( 0))
            ->addColumn(Column::string('main_spec_id', 40)->setComment('主规格id')->setNull(false))
            ->addColumn(Column::tinyInteger('level')->setComment('层级')->setDefault( 0)->setNull(false)->setSigned(false))
            ->addColumn(Column::string('path_info', 300)->setComment('路径信息')->setDefault('')->setNull(false))
            ->addColumn(Column::unsignedInteger('sync_time')->setComment('同步时间')->setDefault(0)->setNull(false)->setSigned(false))
            ->addColumn(\Ledc\ThinkModelTrait\Contracts\Column::makeCreateTime())
            ->addColumn(\Ledc\ThinkModelTrait\Contracts\Column::makeUpdateTime())
            ->addIndex(['category_id'], ['unique' => true])
            ->addIndex(['pid'])
            ->addIndex(['level'])
            ->create();
    }
}
