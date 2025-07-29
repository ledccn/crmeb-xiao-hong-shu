<?php

namespace Ledc\CrmebXiaoHongShu\controller\admin;

use app\Request;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\XiaoHongShu\HttpClient\ProductClient;
use Ledc\XiaoHongShu\Parameters\Product\GetDetailSkuList;
use Ledc\XiaoHongShu\Parameters\Product\SearchItemList;
use think\Response;

/**
 * 小红书商品接口
 */
class ProductController
{
    /**
     * @var ProductClient
     */
    protected ProductClient $client;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->client = XiaoHongShuHelper::merchant()->getProductClient();
    }

    /**
     * 获取商品Sku列表完整版
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getDetailSkuList(Request $request): Response
    {
        $params = $request->get(false);
        $parameter = new GetDetailSkuList($params);
        $result = $this->client->getDetailSkuList($parameter);
        return response_json()->success('success', $result);
    }

    /**
     * 更新物流方案
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function updateSkuLogisticsPlan(Request $request): Response
    {
        $skuId = $request->post('skuId');
        $logisticsPlanId = $request->post('logisticsPlanId');
        $result = $this->client->updateSkuLogisticsPlan($skuId, $logisticsPlanId);
        return response_json()->success('success', $result);
    }

    /**
     * 商品SKU上下架
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function updateSkuAvailable(Request $request): Response
    {
        $skuId = $request->post('skuId');
        $available = $request->post('available');
        $result = $this->client->updateSkuAvailable($skuId, $available);
        return response_json()->success('success', $result);
    }

    /**
     * 更新ITEM V2
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function updateItemV2(Request $request): Response
    {
        $params = $request->post(false);
        $result = $this->client->updateItemV2($params);
        return response_json()->success('success', $result);
    }

    /**
     * 删除ITEM V2
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function deleteItemV2(Request $request): Response
    {
        $itemIds = $request->post('itemIds', []);
        $result = $this->client->deleteItemV2($itemIds);
        return response_json()->success('success', $result);
    }

    /**
     * 更新SKU V2
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function updateSkuV2(Request $request): Response
    {
        $params = $request->post(false);
        $result = $this->client->updateSkuV2($params);
        return response_json()->success('success', $result);
    }

    /**
     * 删除SKU V2
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function deleteSkuV2(Request $request): Response
    {
        $skuIds = $request->post('skuIds', []);
        $result = $this->client->deleteSkuV2($skuIds);
        return response_json()->success('success', $result);
    }

    /**
     * 查询Item列表
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function searchItemList(Request $request): Response
    {
        $pageNo = $request->post('pageNo');
        $pageSize = $request->post('pageSize');
        $searchParam = $request->post('searchParam', []);
        $parameter = new SearchItemList($searchParam);
        $result = $this->client->searchItemList($parameter, $pageNo, $pageSize);
        return response_json()->success('success', $result);
    }

    /**
     * 获取ITEM详情
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getItemInfo(Request $request): Response
    {
        $itemId = $request->get('itemId');
        $pageNo = $request->get('pageNo', 1);
        $pageSize = $request->get('pageSize', 50);
        $result = $this->client->getItemInfo($itemId, $pageNo, $pageSize);
        return response_json()->success('success', $result);
    }

    /**
     * 修改SKU价格
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function updateSkuPrice(Request $request): Response
    {
        $skuId = $request->post('skuId');
        $price = $request->post('price');
        $originalPrice = $request->post('originalPrice');
        $result = $this->client->updateSkuPrice($skuId, $price, $originalPrice);
        return response_json()->success('success', $result);
    }

    /**
     * 修改商品主图、主图视频
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function updateItemImage(Request $request): Response
    {
        $itemId = $request->post('itemId');
        $materialType = $request->post('materialType');
        $materialUrls = $request->post('materialUrls');
        $result = $this->client->updateItemImage($itemId, $materialType, $materialUrls);
        return response_json()->success('success', $result);
    }

    /**
     * 创建商品Item+Sku（新）
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function createItemAndSku(Request $request): Response
    {
        $data = $request->post(false);
        $result = $this->client->createItemAndSku($data);
        return response_json()->success('success', $result);
    }

    /**
     * 更新商品Item+Sku（新）
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function updateItemAndSku(Request $request): Response
    {
        $data = $request->post(false);
        $result = $this->client->updateItemAndSku($data);
        return response_json()->success('success', $result);
    }
}
