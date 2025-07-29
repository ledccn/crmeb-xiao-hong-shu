<?php

namespace Ledc\CrmebXiaoHongShu\controller\admin;

use app\Request;
use Ledc\CrmebIntraCity\enums\TransOrderStatusEnums;
use Ledc\CrmebIntraCity\locker\OrderLocker;
use Ledc\CrmebIntraCity\parameters\ShanSongParameters;
use Ledc\CrmebIntraCity\ServiceTransEnums;
use Ledc\CrmebIntraCity\ShanSongHelper;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\CrmebXiaoHongShu\services\ShanSongService;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Response;

/**
 * 配送管理
 */
class DeliveryController
{
    /**
     * @var ShanSongService
     */
    protected ShanSongService $service;

    /**
     * 构造函数
     * @param ShanSongService $service
     */
    public function __construct(ShanSongService $service)
    {
        $this->service = $service;
    }

    /**
     * 获取订单数据模型
     * @param int $id
     * @return EbXhsOrder
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    protected function getXhsOrder(int $id): EbXhsOrder
    {
        /** @var EbXhsOrder $order */
        $order = EbXhsOrder::findOrFail($id);
        return $order;
    }

    /**
     * 同城配送运力ID枚举
     * @method GET
     * @return Response
     */
    public function trans(): Response
    {
        $data = [
            [
                'value' => ServiceTransEnums::TRANS_SHANSONG,
                'name' => ServiceTransEnums::cases()[ServiceTransEnums::TRANS_SHANSONG],
                'enabled' => ShanSongHelper::isEnabled(),
            ]
        ];
        return response_json()->success('ok', $data);
    }

    /**
     * 同城配送运力单状态（所有运力共用）
     * @return Response
     */
    public function status(): Response
    {
        return response_json()->success('ok', TransOrderStatusEnums::list());
    }

    /**
     * 创建配送单
     * - 呼叫骑手
     * @method POST
     * @param Request $request
     * @return Response
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function create(Request $request): Response
    {
        [$id, $params] = $request->postMore([
            'id/d',
            ['params/a', []],
        ], true);
        $locker = OrderLocker::create($id);
        if (!$locker->acquire()) {
            return response_json()->fail('未获取到锁，请稍后再试');
        }

        $xhsOrder = $this->getXhsOrder($id);
        $result = $this->service->create(
            $xhsOrder,
            ShanSongParameters::make($params)->setOrderId($xhsOrder->order_id)->cache()
        );
        return response_json()->success('success', $result->jsonSerialize());
    }

    /**
     * 订单计费
     * @method POST
     * @param Request $request
     * @return Response
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function calculate(Request $request): Response
    {
        [$id, $params] = $request->postMore([
            'id/d',
            ['params/a', []],
        ], true);
        if (!$id) {
            return response_json()->fail('订单ID不能为空');
        }

        $xhsOrder = $this->getXhsOrder($id);
        $result = $this->service->calculate(
            $xhsOrder,
            ShanSongParameters::make($params)->setOrderId($xhsOrder->order_id)
        );
        return response_json()->success('ok', $result->jsonSerialize());
    }

    /**
     * 查询订单详情
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function orderInfo(Request $request): Response
    {
        $trans_order_id = $request->get('trans_order_id/s');
        $order_id = $request->get('order_id/s');
        if (empty($trans_order_id)) {
            return response_json()->fail('运力单号必填');
        }
        $result = $this->service->orderInfo($trans_order_id, $order_id);
        return response_json()->success('ok', $result);
    }

    /**
     * 查询闪送员位置信息
     * @method GET
     * @param string $trans_order_id
     * @return Response
     */
    public function courierInfo(string $trans_order_id): Response
    {
        if (empty($trans_order_id)) {
            return response_json()->fail('运力单号必填');
        }
        $result = $this->service->courierInfo($trans_order_id);
        return response_json()->success('ok', $result);
    }

    /**
     * 订单预取消
     * @method POST
     * @param string $trans_order_id
     * @return Response
     */
    public function preAbortOrder(string $trans_order_id): Response
    {
        if (empty($trans_order_id)) {
            return response_json()->fail('运力单号必填');
        }
        $result = $this->service->preAbortOrder($trans_order_id);
        return response_json()->success('ok', $result);
    }

    /**
     * 订单取消
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function abortOrder(Request $request): Response
    {
        $trans_order_id = $request->post('trans_order_id/s');
        $deductFlag = $request->post('deduct_flag/b', false);
        if (empty($trans_order_id)) {
            return response_json()->fail('运力单号必填');
        }
        $result = $this->service->abortOrder($trans_order_id, (bool)$deductFlag);
        return response_json()->success('ok', $result);
    }
}
