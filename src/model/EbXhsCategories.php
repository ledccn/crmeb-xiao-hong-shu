<?php

namespace Ledc\CrmebXiaoHongShu\model;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * eb_xhs_categories XHS小红书分类表
 */
class EbXhsCategories extends Model
{
    use HasEbXhsCategories;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'eb_xhs_categories';

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
        'is_leaf' => 'boolean',
        'support_size_table' => 'boolean',
        'support_recommend_size_table' => 'boolean',
        'support_model_try_on_size_table' => 'boolean',
        'support_main_spec_image' => 'boolean',
    ];

    /**
     * 获取表名
     * @return string
     */
    public function getTableName(): string
    {
        return $this->table;
    }

    /**
     * 唯一查询
     * @param string $categoryId
     * @return self|null
     */
    public static function uniqueQuery(string $categoryId): ?self
    {
        $model = self::where('category_id', $categoryId)->findOrEmpty();
        return $model->isEmpty() ? null : $model;
    }

    /**
     * 分类树
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getCategoriesAttr(): array
    {
        return EbXhsCategories::whereIn('id', explode(',', $this->path_info))
            ->hidden(['sync_time', 'create_time', 'update_time'])
            ->select()
            ->toArray();
    }
}
