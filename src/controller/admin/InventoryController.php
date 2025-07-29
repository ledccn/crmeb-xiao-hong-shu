<?php

namespace Ledc\CrmebXiaoHongShu\controller\admin;

use app\Request;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\XiaoHongShu\HttpClient\InventoryClient;
use think\Response;

/**
 * 小红书库存接口
 */
class InventoryController
{
    /**
     * @var InventoryClient
     */
    protected InventoryClient $client;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->client = XiaoHongShuHelper::merchant()->getInventoryClient();
    }

    /**
     * 获取商品SKU库存
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getSkuStock(Request $request): Response
    {
        $skuId = $request->get('skuId');
        $result = $this->client->getSkuStock($skuId);
        return response_json()->success('success', $result);
    }

    /**
     * 同步商品SKU库存
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function syncSkuStock(Request $request): Response
    {
        $skuId = $request->post('skuId');
        $qty = $request->post('qty');
        $result = $this->client->syncSkuStock($skuId, $qty);
        return response_json()->success('success', $result);
    }

    /**
     * 增减商品SKU库存
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function incSkuStock(Request $request): Response
    {
        $skuId = $request->post('skuId');
        $qty = $request->post('qty');
        $result = $this->client->incSkuStock($skuId, $qty);
        return response_json()->success('success', $result);
    }

    /**
     * 获取商品SKU库存（V2）
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getSkuStockV2(Request $request): Response
    {
        $skuId = $request->get('skuId');
        $inventoryType = $request->get('inventoryType');
        $result = $this->client->getSkuStockV2($skuId, $inventoryType);
        return response_json()->success('success', $result);
    }

    /**
     * 同步商品SKU库存（V2）
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function syncSkuStockV2(Request $request): Response
    {
        $skuId = $request->post('skuId');
        $qtyWithWhcode = $request->post('qtyWithWhcode');
        $result = $this->client->syncSkuStockV2($skuId, $qtyWithWhcode);
        return response_json()->success('success', $result);
    }

    /**
     * 创建仓库
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $code = $request->post('code');
        $name = $request->post('name');
        $zoneCode = $request->post('zoneCode');
        $address = $request->post('address');
        $contactName = $request->post('contactName', '');
        $contactTel = $request->post('contactTel', '');
        $result = $this->client->create($code, $name, $zoneCode, $address, $contactName, $contactTel);
        return response_json()->success('success', $result);
    }

    /**
     * 修改仓库
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response
    {
        $code = $request->post('code');
        $name = $request->post('name');
        $zoneCode = $request->post('zoneCode');
        $address = $request->post('address');
        $contactName = $request->post('contactName', '');
        $contactTel = $request->post('contactTel', '');
        $result = $this->client->update($code, $name, $zoneCode, $address, $contactName, $contactTel);
        return response_json()->success('success', $result);
    }

    /**
     * 仓库列表
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function list(Request $request): Response
    {
        $pageNo = $request->get('pageNo', 1);
        $pageSize = $request->get('pageSize', 50);
        $code = $request->get('code', '');
        $name = $request->get('name', '');
        $result = $this->client->list($pageNo, $pageSize, $code, $name);
        return response_json()->success('success', $result);
    }

    /**
     * 仓库详情
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function info(Request $request): Response
    {
        $code = $request->get('code');
        $result = $this->client->info($code);
        return response_json()->success('success', $result);
    }

    /**
     * 设置仓库覆盖地区
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function setCoverage(Request $request): Response
    {
        $whCode = $request->post('whCode');
        $zoneCodeList = $request->post('zoneCodeList');
        $result = $this->client->setCoverage($whCode, $zoneCodeList);
        return response_json()->success('success', $result);
    }

    /**
     * 设置仓库优先级
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function setPriority(Request $request): Response
    {
        $zoneCode = $request->post('zoneCode');
        $warehousePriorityList = $request->post('warehousePriorityList');
        $result = $this->client->setPriority($zoneCode, $warehousePriorityList);
        return response_json()->success('success', $result);
    }
}
