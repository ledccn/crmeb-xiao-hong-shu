<?php

namespace Ledc\CrmebXiaoHongShu\controller\admin;

use app\Request;
use Ledc\CrmebXiaoHongShu\jobs\SyncCategoriesJobs;
use Ledc\CrmebXiaoHongShu\model\EbXhsCategories;
use Ledc\CrmebXiaoHongShu\services\XhsCategoriesService;
use ReflectionException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;

/**
 * 小红书分类
 */
class CategoriesController
{
    /**
     * 服务
     * @var XhsCategoriesService
     */
    protected XhsCategoriesService $service;

    /**
     * 构造函数
     * @param XhsCategoriesService $service
     */
    public function __construct(XhsCategoriesService $service)
    {
        $this->service = $service;
    }

    /**
     * 分类列表
     * @param Request $request
     * @return Response
     * @throws ReflectionException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function list(Request $request): Response
    {
        $where = $request->getMore(['id', 'pid', 'category_id', 'level']);
        $where = filter_where($where);
        return response_json()->success('success', $this->service->getList($where));
    }

    /**
     * 获取分类详情
     * @param Request $request
     * @return Response
     */
    public function read(Request $request): Response
    {
        $category_id = $request->get('category_id/s');
        if (empty($category_id)) {
            return response_json()->fail('请选择分类');
        }
        $model = EbXhsCategories::uniqueQuery($category_id);
        if (!$model) {
            return response_json()->fail('分类不存在，请联系开发者');
        }
        return response_json()->success('success', $model->append(['categories'])->toArray());
    }

    /**
     * 同步分类
     * @return Response
     */
    public function sync(): Response
    {
        $locker = XhsCategoriesService::getLocker();
        if ($locker->acquire()) {
            $locker->release();
            SyncCategoriesJobs::dispatch(time());
            return response_json()->success('已发送同步指令，请稍后查看');
        }
        return response_json()->fail('正在同步中，请勿重复操作');
    }
}
