<?php

namespace Ledc\CrmebXiaoHongShu\controller\admin;

use app\Request;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\XiaoHongShu\HttpClient\CommonClient;
use Ledc\XiaoHongShu\Parameters\Common\GetDeliveryRule;
use think\Response;

/**
 * 小红书公共接口
 */
class CommonController
{
    /**
     * @var CommonClient
     */
    protected CommonClient $client;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->client = XiaoHongShuHelper::merchant()->getCommonClient();
    }

    /**
     * 获取商品分类
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getCategories(Request $request): Response
    {
        $categoryId = $request->get('categoryId', '');
        $result = $this->client->getCategories($categoryId);
        return response_json()->success('success', $result);
    }

    /**
     * 由末级分类获取规格（新）
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getVariations(Request $request): Response
    {
        $categoryId = $request->get('categoryId', '');
        $result = $this->client->getVariations($categoryId);
        return response_json()->success('success', $result);
    }

    /**
     * 由末级分类获取属性
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getAttributeLists(Request $request): Response
    {
        $categoryId = $request->get('categoryId', '');
        $result = $this->client->getAttributeLists($categoryId);
        return response_json()->success('success', $result);
    }

    /**
     * 由属性获取属性值
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getAttributeValues(Request $request): Response
    {
        $attributeId = $request->get('attributeId', '');
        $result = $this->client->getAttributeValues($attributeId);
        return response_json()->success('success', $result);
    }

    /**
     * 获取快递公司信息
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getExpressCompanyList(Request $request): Response
    {
        $result = $this->client->getExpressCompanyList();
        return response_json()->success('success', $result);
    }

    /**
     * 获取物流方案列表
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getLogisticsList(Request $request): Response
    {
        $result = $this->client->getLogisticsList();
        return response_json()->success('success', $result);
    }

    /**
     * 运费模版列表
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getCarriageTemplateList(Request $request): Response
    {
        $pageIndex = $request->get('page', 1);
        $pageSize = $request->get('limit', 20);
        $result = $this->client->getCarriageTemplateList($pageIndex, $pageSize);
        return response_json()->success('success', $result);
    }

    /**
     * 运费模版详情
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getCarriageTemplate(Request $request): Response
    {
        $templateId = $request->get('templateId');
        $result = $this->client->getCarriageTemplate($templateId);
        return response_json()->success('success', $result);
    }

    /**
     * 获取品牌信息
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function brandSearch(Request $request): Response
    {
        $categoryId = $request->get('categoryId');
        $keyword = $request->get('keyword');
        $pageNo = $request->get('page', 1);
        $pageSize = $request->get('limit', 10);
        $result = $this->client->brandSearch($categoryId, $keyword, $pageNo, $pageSize);
        return response_json()->success('success', $result);
    }

    /**
     * 获取物流模式列表
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function logisticsMode(Request $request): Response
    {
        $result = $this->client->logisticsMode();
        return response_json()->success('success', $result);
    }

    /**
     * 批量获取发货时间规则
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getDeliveryRule(Request $request): Response
    {
        $params = $request->get(false);
        $parameter = new GetDeliveryRule($params);
        $result = $this->client->getDeliveryRule($parameter);
        return response_json()->success('success', $result);
    }

    /**
     * 获取商家地址库
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getAddressRecord(Request $request): Response
    {
        $pageIndex = $request->get('page', 1);
        $pageSize = $request->get('limit', 20);
        $result = $this->client->getAddressRecord($pageIndex, $pageSize);
        return response_json()->success('success', $result);
    }

    /**
     * 商品标题类目预测
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function categoryMatch(Request $request): Response
    {
        $spuName = $request->post('spuName');
        $topK = $request->post('topK', 1);
        $externalCategoryName = $request->post('externalCategoryName');
        $result = $this->client->categoryMatch($spuName, $topK, $externalCategoryName);
        return response_json()->success('success', $result);
    }

    /**
     * 获取预测类目（新）
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function categoryMatchV2(Request $request): Response
    {
        $name = $request->post('name');
        $imageUrls = $request->post('imageUrls');
        $scene = $request->post('scene', 1);
        $result = $this->client->categoryMatchV2($name, $imageUrls, $scene);
        return response_json()->success('success', $result);
    }

    /**
     * 判断文本中是否含有违禁词
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function checkForbiddenKeyword(Request $request): Response
    {
        $text = $request->post('text/s');
        $result = $this->client->checkForbiddenKeyword($text);
        return response_json()->success('success', $result);
    }
}
