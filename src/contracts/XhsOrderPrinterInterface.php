<?php

namespace Ledc\CrmebXiaoHongShu\contracts;

use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;

/**
 * 小红书订单打印接口
 */
interface XhsOrderPrinterInterface
{
    /**
     * 设置小红书订单打印内容
     * @param EbXhsOrder $xhsOrder
     * @return self
     */
    public function setXhsOrderPrinterContent(EbXhsOrder $xhsOrder): self;
}
