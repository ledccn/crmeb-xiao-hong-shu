<?php

namespace Ledc\CrmebXiaoHongShu\model;

/**
 * eb_xhs_categories XHS小红书分类表
 * @property integer $id (主键)
 * @property integer $pid 父级ID
 * @property string $category_id 分类ID
 * @property string $zh_name 分类中文名
 * @property string $en_name 分类英文名
 * @property boolean $is_leaf 是否是叶子类目
 * @property boolean $support_size_table 是否支持基础尺码图
 * @property boolean $support_recommend_size_table 是否支持尺码推荐图
 * @property boolean $support_model_try_on_size_table 是否支持模特试穿图
 * @property boolean $support_main_spec_image 是否支持规格大图
 * @property string $main_spec_id 主规格id
 * @property integer $level 层级
 * @property string $path_info 路径信息
 * @property integer $sync_time 同步时间
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
trait HasEbXhsCategories
{
}
