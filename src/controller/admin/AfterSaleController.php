<?php

namespace Ledc\CrmebXiaoHongShu\controller\admin;

use app\Request;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\XiaoHongShu\HttpClient\AfterSaleClient;
use Ledc\XiaoHongShu\Parameters\AfterSale\AfterSaleList;
use Ledc\XiaoHongShu\Parameters\AfterSale\AuditReturns;
use think\Response;

/**
 * 小红书售后管理
 */
class AfterSaleController
{
    /**
     * @var AfterSaleClient
     */
    protected AfterSaleClient $client;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->client = XiaoHongShuHelper::merchant()->getAfterSaleClient();
    }

    /**
     * 获取售后列表（新）
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function listAfterSaleInfos(Request $request): Response
    {
        $params = $request->post(false);
        $parameter = new AfterSaleList($params);
        $result = $this->client->listAfterSaleInfos($parameter);
        return response_json()->success('success', $result);
    }

    /**
     * 获取售后详情（新）
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function getAfterSaleInfo(Request $request): Response
    {
        $returnsId = $request->post('returnsId');
        $needNegotiateRecord = $request->post('needNegotiateRecord', true);
        $requestHeader = $request->post('requestHeader', []);
        $result = $this->client->getAfterSaleInfo($returnsId, $needNegotiateRecord, $requestHeader);
        return response_json()->success('success', $result);
    }

    /**
     * 售后审核（新）
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function auditReturns(Request $request): Response
    {
        $params = $request->post(false);
        $parameter = new AuditReturns($params);
        $result = $this->client->auditReturns($parameter);
        return response_json()->success('success', $result);
    }

    /**
     * 售后确认收货（新）
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function confirmReceive(Request $request): Response
    {
        $returnsId = $request->post('returnsId');
        $action = $request->post('action');
        $reason = $request->post('reason');
        $description = $request->post('description', '');
        $result = $this->client->confirmReceive($returnsId, $action, $reason, $description);
        return response_json()->success('success', $result);
    }

    /**
     * 售后换货确认收货并发货
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function receiveAndShip(Request $request): Response
    {
        $returnsId = $request->post('returnsId');
        $expressCompanyCode = $request->post('expressCompanyCode');
        $expressNo = $request->post('expressNo');
        $result = $this->client->receiveAndShip($returnsId, $expressCompanyCode, $expressNo);
        return response_json()->success('success', $result);
    }

    /**
     * 获取售后拒绝原因
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function rejectReasons(Request $request): Response
    {
        $returnsId = $request->get('returnsId');
        $rejectReasonType = $request->get('rejectReasonType');
        $result = $this->client->rejectReasons($returnsId, $rejectReasonType);
        return response_json()->success('success', $result);
    }
}
