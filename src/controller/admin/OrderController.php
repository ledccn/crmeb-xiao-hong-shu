<?php

namespace Ledc\CrmebXiaoHongShu\controller\admin;

use app\Request;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\CrmebXiaoHongShu\services\PrinterService;
use Ledc\CrmebXiaoHongShu\services\XhsOrderLogsService;
use Ledc\CrmebXiaoHongShu\services\XhsOrderService;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\XiaoHongShu\HttpClient\OrderClient;
use Ledc\XiaoHongShu\Parameters\Order\GetInvoiceList;
use Ledc\XiaoHongShu\Parameters\Order\GetOrderList;
use Ledc\XiaoHongShu\Parameters\Order\ModifyOrderExpressInfo;
use Ledc\XiaoHongShu\Parameters\Order\OrderDeliver;
use ReflectionException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;

/**
 * 小红书订单接口
 */
class OrderController
{
    /**
     * @var OrderClient
     */
    protected OrderClient $client;
    /**
     * @var XhsOrderService
     */
    protected XhsOrderService $service;

    /**
     * 构造函数
     * @param XhsOrderService $service
     */
    public function __construct(XhsOrderService $service)
    {
        $this->client = XiaoHongShuHelper::merchant()->getOrderClient();
        $this->service = $service;
    }

    /**
     * 获取订单列表
     * @param Request $request
     * @return Response
     * @throws ReflectionException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function list(Request $request): Response
    {
        $where = $request->getMore(['order_id', 'order_type', 'order_status', 'order_after_sales_status', 'cancel_status', 'receiver_phone', 'express_tracking_no', 'payment_type', 'shop_id']);
        $order_field = $request->get('order_field', 'id');
        $order = $request->get('order', 'DESC');
        if (!in_array($order_field, EbXhsOrder::ORDER_FIELD_MAP, true)) {
            return response_json()->fail('排序字段错误，可用值：' . implode(',', EbXhsOrder::ORDER_FIELD_MAP));
        }
        $order = $order === 'ASC' ? 'ASC' : 'DESC';
        return response_json()->success($this->service->getList(filter_where($where), $order_field === 'id' ? "id $order" : "$order_field $order,id DESC"));
    }

    /**
     * 获取订单日志列表
     * @param Request $request
     * @param XhsOrderLogsService $orderLogsService
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws ReflectionException
     */
    public function logsList(Request $request, XhsOrderLogsService $orderLogsService): Response
    {
        $where = $request->getMore(['oid', 'order_id']);
        $where = filter_where($where);
        if (empty($where)) {
            return response_json()->fail('缺少order_id参数');
        }
        return response_json()->success($orderLogsService->getList(filter_where($where)));
    }

    /**
     * 打印订单小票
     * @param int $id 内部订单主键
     * @return Response
     */
    public function print(int $id): Response
    {
        if (empty($id)) {
            return response_json()->fail('缺少id参数');
        }

        $xhsOrder = EbXhsOrder::findOrEmpty($id);
        if ($xhsOrder->isEmpty()) {
            return response_json()->fail('小红书订单不存在');
        }
        $result = PrinterService::orderPrintTicket($xhsOrder);
        if ($result) {
            return response_json()->success(100010);
        }
        return response_json()->fail(100005);
    }

    /**
     * 获取订单列表
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getOrderList(Request $request): Response
    {
        $params = $request->get(false);
        $params = filter_where($params);
        $_temp = array_filter($params, function ($k) {
            return !in_array($k, ['pageNo', 'pageSize']);
        }, ARRAY_FILTER_USE_KEY);
        if (empty($_temp)) {
            $parameter = GetOrderList::getDefault();
            $parameter->pageNo = $request->get('pageNo') ?: $parameter->pageNo;
            $parameter->pageSize = $request->get('pageSize') ?: $parameter->pageSize;
        } else {
            $parameter = new GetOrderList($params);
        }

        $result = $this->client->getOrderList($parameter);
        return response_json()->success('success', $result);
    }

    /**
     * 获取订单详情
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getOrderDetail(Request $request): Response
    {
        $orderId = $request->get('orderId');
        $result = $this->client->getOrderDetail($orderId);
        XhsOrderService::syncOrderDetail($result);
        return response_json()->success('success', $result);
    }

    /**
     * 获取收货人信息
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function getOrderReceiverInfo(Request $request): Response
    {
        $receiverQueries = $request->post('receiverQueries');
        $isReturn = $request->post('isReturn');
        $result = $this->client->getOrderReceiverInfo($receiverQueries, $isReturn);
        return response_json()->success('success', $result);
    }

    /**
     * 修改订单备注
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function modifySellerMarkInfo(Request $request): Response
    {
        $orderId = $request->post('orderId');
        $sellerMarkInfo = $request->post('sellerMarkInfo');
        $operator = $request->post('operator');
        $sellerMarkPriority = $request->post('sellerMarkPriority');
        $result = $this->client->modifySellerMarkInfo($orderId, $sellerMarkInfo, $operator, $sellerMarkPriority);
        return response_json()->success('success', $result);
    }

    /**
     * 订单发货
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function orderDeliver(Request $request): Response
    {
        $params = $request->post(false);
        $parameter = new OrderDeliver($params);
        $result = $this->client->orderDeliver($parameter);
        return response_json()->success('success', $result);
    }

    /**
     * 修改快递单号
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function modifyOrderExpressInfo(Request $request): Response
    {
        $params = $request->post(false);
        $parameter = new ModifyOrderExpressInfo($params);
        $result = $this->client->modifyOrderExpressInfo($parameter);
        return response_json()->success('success', $result);
    }

    /**
     * 订单物流轨迹
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getOrderTracking(Request $request): Response
    {
        $orderId = $request->get('orderId');
        $result = $this->client->getOrderTracking($orderId);
        return response_json()->success('success', $result);
    }

    /**
     * 海关申报信息
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getOrderDeclareInfo(Request $request): Response
    {
        $orderId = $request->get('orderId');
        $result = $this->client->getOrderDeclareInfo($orderId);
        return response_json()->success('success', $result);
    }

    /**
     * 批量上传序列号
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function batchBindSkuIdentifyInfo(Request $request): Response
    {
        $list = $request->post('orderSkuIdentifyCodeInfoList');
        $result = $this->client->batchBindSkuIdentifyInfo($list);
        return response_json()->success('success', $result);
    }

    /**
     * 跨境清关支持口岸
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getSupportedPortList(Request $request): Response
    {
        $result = $this->client->getSupportedPortList();
        return response_json()->success('success', $result);
    }

    /**
     * 跨境重推支付单
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function resendBondedPaymentRecord(Request $request): Response
    {
        $orderId = $request->post('orderId');
        $customsType = $request->post('customsType');
        $result = $this->client->resendBondedPaymentRecord($orderId, $customsType);
        return response_json()->success('success', $result);
    }

    /**
     * 跨境商品备案信息同步
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function syncItemCustomsInfo(Request $request): Response
    {
        $params = $request->post(false);
        $result = $this->client->syncItemCustomsInfo($params);
        return response_json()->success('success', $result);
    }

    /**
     * 跨境商品备案信息查询
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getCustomsInfo(Request $request): Response
    {
        $barcode = $request->get('barcode');
        $result = $this->client->getCustomsInfo($barcode);
        return response_json()->success('success', $result);
    }

    /**
     * 小包批次创建
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function createTransferBatch(Request $request): Response
    {
        $orders = $request->post('orders');
        $planInfoId = $request->post('planInfoId');
        $result = $this->client->createTransferBatch($orders, $planInfoId);
        return response_json()->success('success', $result);
    }

    /**
     * 开票列表查询
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getInvoiceList(Request $request): Response
    {
        $params = $request->get(false);
        $parameter = new GetInvoiceList($params);
        $result = $this->client->getInvoiceList($parameter);
        return response_json()->success('success', $result);
    }

    /**
     * 开票结果回传（正向蓝票开具）
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function confirmInvoice(Request $request): Response
    {
        $params = $request->post(false);
        $result = $this->client->confirmInvoice($params);
        return response_json()->success('success', $result);
    }

    /**
     * 发票冲红（逆向冲红）
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function reverseInvoice(Request $request): Response
    {
        $params = $request->post(false);
        $result = $this->client->reverseInvoice($params);
        return response_json()->success('success', $result);
    }

    /**
     * 批量解密
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function batchDecrypt(Request $request): Response
    {
        $baseInfos = $request->post('baseInfos');
        $actionType = $request->post('actionType');
        $appUserId = $request->post('appUserId');
        $result = $this->client->batchDecrypt($baseInfos, $actionType, $appUserId);
        return response_json()->success('success', $result);
    }

    /**
     * 批量脱敏
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function batchDesensitise(Request $request): Response
    {
        $baseInfos = $request->post('baseInfos');
        $actionType = $request->post('actionType');
        $appUserId = $request->post('appUserId');
        $result = $this->client->batchDesensitise($baseInfos, $actionType, $appUserId);
        return response_json()->success('success', $result);
    }

    /**
     * 批量获取索引串
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function batchIndex(Request $request): Response
    {
        $list = $request->post('indexBaseInfoList');
        $result = $this->client->batchIndex($list);
        return response_json()->success('success', $result);
    }

    /**
     * 获取KOS员工数据
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getKosData(Request $request): Response
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $pageNo = $request->get('pageNo', 1);
        $pageSize = $request->get('pageSize', 50);
        $result = $this->client->getKosData($startDate, $endDate, $pageNo, $pageSize);
        return response_json()->success('success', $result);
    }

    /**
     * 创建三方商品备案信息
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function createItemCustomsInfo(Request $request): Response
    {
        $params = $request->post(false);
        $result = $this->client->createItemCustomsInfo($params);
        return response_json()->success('success', $result);
    }
}
