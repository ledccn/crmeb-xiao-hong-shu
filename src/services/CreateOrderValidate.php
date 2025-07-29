<?php

namespace Ledc\CrmebXiaoHongShu\services;

use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderLogistics;
use Ledc\XiaoHongShu\Enums\OrderAfterSalesStatusEnums;
use Ledc\XiaoHongShu\Enums\OrderStatusEnums;
use think\exception\ValidateException;

/**
 * 创建订单验证服务
 */
class CreateOrderValidate
{
    /**
     * 订单同城配送发单前做前置检查
     * @param EbXhsOrder $xhsOrder
     * @return void
     */
    public static function beforeValidate(EbXhsOrder $xhsOrder): void
    {
        if ($xhsOrder->isEmpty()) {
            throw new ValidateException('订单不存在，暂时不支持同城配送');
        }
        if (!OrderStatusEnums::canDelivery($xhsOrder->order_status)) {
            throw new ValidateException('订单状态错误，暂时不支持同城配送');
        }
        if (OrderAfterSalesStatusEnums::existsAfterSales($xhsOrder->order_after_sales_status)) {
            throw new ValidateException('订单售后状态错误，暂时不支持同城配送');
        }
        if ($xhsOrder->cancel_status) {
            throw new ValidateException('订单已取消，暂时不支持同城配送');
        }
        if (!$xhsOrder->paid_time) {
            throw new ValidateException('订单未支付，暂时不支持同城配送');
        }
        if ($xhsOrder->unpack) {
            throw new ValidateException('订单已拆包，暂时不支持同城配送');
        }
        // 验证物流模式
        if ($xhsOrder->logistics !== 'red_auto') {
            throw new ValidateException('订单非三方自主发货，暂时不支持同城配送');
        }
        if ($xhsOrder->logistics_mode !== 1) {
            throw new ValidateException('订单非普通内贸，暂时不支持同城配送');
        }

        /** @var EbXhsOrderLogistics $orderLogistics */
        $orderLogistics = EbXhsOrderLogistics::findByOid($xhsOrder->id);
        if ($orderLogistics) {
            if ($orderLogistics->trans_processed) {
                throw new ValidateException('该订单已呼叫骑手，请勿重复操作');
            }
        }
    }
}
