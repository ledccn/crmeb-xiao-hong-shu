<?php

namespace Ledc\CrmebXiaoHongShu\traits;

use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderProduct;
use Ledc\XiaoHongShu\Enums\OrderPaymentType;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 【小红书订单打印】易联云
 */
trait HasXhsOrderYiLianYun
{
    /**
     * 构造订单头部信息区
     * @param EbXhsOrder $xhsOrder
     * @return string
     */
    private function getHeaderContents(EbXhsOrder $xhsOrder): string
    {
        $printTime = date('Y-m-d H:i:s', time());
        $name = $xhsOrder->shop_name ?: '小红书店铺';
        $paid_time = date('Y-m-d H:i:s', $xhsOrder->paid_time);
        $order_seq = get_order_seq($xhsOrder->paid_time, $xhsOrder->order_seq);
        $payment_type = OrderPaymentType::getLabel($xhsOrder->payment_type) ?: '其他支付';
        // 承诺最晚发货时间
        $promise_last_delivery_time = $xhsOrder->promise_last_delivery_time ? date('Y-m-d H:i', $xhsOrder->promise_last_delivery_time) : '';

        return implode("\r", [
            '<FS2>小红书订单</FS2>',
            "<FS><center> ** $name **</center></FS>",
            "<FS2><center># $order_seq</center></FS2>",
            '<FH2><FW2>----------------</FW2></FH2>',
            "承诺最晚发货：$promise_last_delivery_time",
            "订单编号：$xhsOrder->order_id",
            "打印时间：$printTime",
            "付款时间：$paid_time",
            "支付方式：$payment_type",
            "姓    名：$xhsOrder->receiver_name",
            "电    话：$xhsOrder->receiver_phone",
            "地    址：$xhsOrder->receiver_address",
            '<FH2><FW2>----------------</FW2></FH2>',
            "<FS2>备注：$xhsOrder->customer_remark</FS2>" . PHP_EOL,
        ]);
    }

    /**
     * 买家收货信息变更
     * @param EbXhsOrder $xhsOrder
     * @return self
     */
    public function setXhsOrderReceiverChange(EbXhsOrder $xhsOrder): self
    {
        $order_seq = get_order_seq($xhsOrder->paid_time, $xhsOrder->order_seq);
        $contents = $this->getHeaderContents($xhsOrder);

        $this->printerContent = <<<CONTENT
<VI>036</VI><VI>064</VI><VI>075</VI><VI>074</VI>
$contents
<FH2><FW2>----------------</FW2></FH2>
<FS3>买家收货信息变更</FS3>
<FH2><FW2>----------------</FW2></FH2>
<QR>$xhsOrder->order_id</QR>
<FS><center> ** # $order_seq 完 **</center></FS>
CONTENT;
        return $this;
    }

    /**
     * 售后申请
     * @param EbXhsOrder $xhsOrder
     * @return self
     */
    public function setXhsOrderAfterSaleCreate(EbXhsOrder $xhsOrder): self
    {
        $order_seq = get_order_seq($xhsOrder->paid_time, $xhsOrder->order_seq);
        $contents = $this->getHeaderContents($xhsOrder);

        $this->printerContent = <<<CONTENT
<VI>035</VI><VI>047</VI><VI>074</VI><VI>064</VI><VI>047</VI><VI>065</VI>
$contents
<FH2><FW2>----------------</FW2></FH2>
<FS3>用户发起售后申请，请及时处理</FS3>
<FH2><FW2>----------------</FW2></FH2>
<QR>$xhsOrder->order_id</QR>
<FS><center> ** # $order_seq 完 **</center></FS>
CONTENT;
        return $this;
    }

    /**
     * 设置小红书订单打印内容
     * @param EbXhsOrder $xhsOrder
     * @return self
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function setXhsOrderPrinterContent(EbXhsOrder $xhsOrder): self
    {
        $goodsStr = '<table><tr><td>商品名称</td><td>数量</td><td>金额</td></tr>';
        $products = EbXhsOrderProduct::queryByOid($xhsOrder->id)->select();
        /** @var EbXhsOrderProduct $product */
        foreach ($products as $product) {
            $goodsStr .= '<tr>';
            $sku_spec = $product->sku_spec ? PHP_EOL . ' - ' . $product->sku_spec : '';
            $price = bcdiv((string)$product->total_paid_amount, '100', 2);
            $goodsStr .= "<td>{$product->item_name}$sku_spec</td><td>{$product->sku_quantity}</td><td>{$price}</td>";
            $goodsStr .= '</tr>';
            unset($price, $sku_spec);
        }
        $goodsStr .= '</table>';

        $order_seq = get_order_seq($xhsOrder->paid_time, $xhsOrder->order_seq);
        $contents = $this->getHeaderContents($xhsOrder);

        // 金额相关
        $total_shipping_free = $this->formatFloat(bcdiv((string)$xhsOrder->total_shipping_free, '100', 2));
        $total_change_price_amount = $this->formatFloat(bcdiv((string)$xhsOrder->total_change_price_amount, '100', 2));
        $total_merchant_discount = $this->formatFloat(bcdiv((string)$xhsOrder->total_merchant_discount, '100', 2));
        $total_red_discount = $this->formatFloat(bcdiv((string)$xhsOrder->total_red_discount, '100', 2));
        $total_pay_amount = $this->formatFloat(bcdiv((string)$xhsOrder->total_pay_amount, '100', 2));
        $merchant_actual_receive_amount = $this->formatFloat(bcdiv((string)$xhsOrder->merchant_actual_receive_amount, '100', 2));

        $this->printerContent = <<<CONTENT
$contents
*************商品***************\r
{$goodsStr}
********************************\r
<FH>
<LR>配送费:￥{$total_shipping_free},改价金额:￥{$total_change_price_amount}</LR>
<LR>商家优惠:￥{$total_merchant_discount},平台优惠:￥{$total_red_discount}</LR>
<RA>实际支付:￥{$total_pay_amount}</RA>
<RA>商家实收:￥{$merchant_actual_receive_amount}</RA>
</FH>
<QR>$xhsOrder->order_id</QR>
<FS><center> ** # $order_seq 完 **</center></FS>
CONTENT;
        return $this;
    }

    /**
     * 格式化浮点数，移除右侧0和小数点
     */
    private static function formatFloat(string $float): string
    {
        return rtrim(rtrim($float, '0'), '.');
    }
}
