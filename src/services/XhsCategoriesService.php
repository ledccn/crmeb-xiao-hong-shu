<?php

namespace Ledc\CrmebXiaoHongShu\services;

use app\services\BaseServices;
use Ledc\CrmebXiaoHongShu\dao\XhsCategoriesDao;
use Ledc\CrmebXiaoHongShu\model\EbXhsCategories;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\ThinkModelTrait\RedisLocker;
use Ledc\XiaoHongShu\HttpClient\CommonClient;
use ReflectionException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Log;
use Throwable;

/**
 * XHS小红书分类服务层
 */
class XhsCategoriesService extends BaseServices
{
    /**
     * @var XhsCategoriesDao
     */
    protected $dao;

    /**
     * @param XhsCategoriesDao $dao
     */
    public function __construct(XhsCategoriesDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @return XhsCategoriesDao
     */
    public function getDao(): XhsCategoriesDao
    {
        return $this->dao;
    }

    /**
     * 获取列表
     * @param array $where
     * @param string $order
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws ReflectionException
     */
    public function getList(array $where = [], string $order = 'id DESC'): array
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->selectList($where, '*', $page, $limit, $order);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 获取分类同步锁
     * @return RedisLocker
     */
    public static function getLocker(): RedisLocker
    {
        return new RedisLocker(static::class, 60);
    }

    /**
     * 定时同步小红书商品分类
     * - 建议每12小时执行一次
     * @return void
     */
    public static function scheduler(): void
    {
        if (!XiaoHongShuHelper::isEnabled()) {
            return;
        }

        $locker = self::getLocker();
        try {
            XiaoHongShuHelper::clear();
            if ($locker->acquire()) {
                log_develop('定时同步小红书分类，执行开始');
                // 先清空数据表
                $model = new EbXhsCategories;
                $table = $model->getTableName();
                $model->db()->getConnection()->query("TRUNCATE `$table`");

                // 获取顶级分类
                $client = XiaoHongShuHelper::merchant()->getCommonClient();
                $topLevel = $client->getCategories();
                $topCategories = $topLevel['categoryV3s'];

                // 保存顶级分类
                array_map(function ($category) use ($client) {
                    $model = self::syncCategory($category, 0, 0, '');
                    self::getCategoryTree($client, $model);
                }, $topCategories);
                log_develop('定时同步小红书分类，执行结束');
            }
        } catch (Throwable $throwable) {
            Log::error('同步小红书分类失败：' . $throwable->getMessage());
        } finally {
            $locker->release();
        }
    }

    /**
     * 获取分类树
     * @param CommonClient $client 小红书公共接口
     * @param EbXhsCategories $xhsCategories 分类模型
     */
    private static function getCategoryTree(CommonClient $client, EbXhsCategories $xhsCategories): void
    {
        try {
            $subCategories = $client->getCategories($xhsCategories->category_id);
            $children = $subCategories['categoryV3s'];
            foreach ($children as $child) {
                $model = self::syncCategory($child, $xhsCategories->id, $xhsCategories->level + 1, $xhsCategories->path_info);
                self::getCategoryTree($client, $model);
            }
        } catch (Throwable $throwable) {
        }
    }

    /**
     * 同步分类
     * @param array $category
     * @param int $pid
     * @param int $level
     * @param string $pathInfo
     * @return EbXhsCategories
     */
    private static function syncCategory(array $category, int $pid, int $level, string $pathInfo): EbXhsCategories
    {
        $categoryId = $category['id'];
        $model = EbXhsCategories::uniqueQuery($categoryId);
        if (!$model) {
            $model = new EbXhsCategories();
            $model->pid = $pid;
            $model->category_id = $categoryId;
        }

        $model->zh_name = $category['name'];
        $model->en_name = $category['enName'] ?: '';
        $model->is_leaf = $category['isLeaf'];
        $model->support_size_table = $category['supportSizeTable'];
        $model->support_recommend_size_table = $category['supportRecommendSizeTable'];
        $model->support_model_try_on_size_table = $category['supportModelTryOnSizeTable'];
        $model->support_main_spec_image = $category['supportMainSpecImage'];
        $model->level = $level;
        $model->sync_time = time();
        $model->save();

        if (empty($pathInfo)) {
            $model->path_info = $model->id;
        } else {
            $model->path_info = $pathInfo . ',' . $model->id;
        }
        $model->save();

        return $model;
    }
}
