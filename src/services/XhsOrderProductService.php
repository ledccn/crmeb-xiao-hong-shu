<?php

namespace Ledc\CrmebXiaoHongShu\services;

use app\services\BaseServices;
use Ledc\CrmebXiaoHongShu\dao\XhsOrderProductDao;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrderProduct;
use ReflectionException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * XHS小红书订单商品SKU服务层
 */
class XhsOrderProductService extends BaseServices
{
    /**
     * @var XhsOrderProductDao
     */
    protected $dao;

    /**
     * @param XhsOrderProductDao $dao
     */
    public function __construct(XhsOrderProductDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @return XhsOrderProductDao
     */
    public function getDao(): XhsOrderProductDao
    {
        return $this->dao;
    }

    /**
     * 同步订单商品SKU信息
     * @param EbXhsOrder $xhsOrder
     * @param array $skuList
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function syncOrderSkuList(EbXhsOrder $xhsOrder, array $skuList): void
    {
        $oid = $xhsOrder->id;
        $order_id = $xhsOrder->order_id;
        foreach ($skuList as $sku) {
            $skuId = $sku['skuId'];
            $model = EbXhsOrderProduct::querySkuByOid($oid, $skuId);
            if (!$model) {
                $model = new EbXhsOrderProduct();
                $model->oid = $oid;
                $model->order_id = $order_id;
                $model->sku_id = $skuId;
            }

            $model->sku_name = $sku['skuName'];
            if (!empty($sku['erpcode'])) {
                $model->erp_code = $sku['erpcode'];
            }
            $model->sku_spec = $sku['skuSpec'];
            $model->sku_image = $sku['skuImage'];
            $model->sku_quantity = $sku['skuQuantity'];
            $model->sku_detail_list = $sku['skuDetailList'];
            $model->total_paid_amount = $sku['totalPaidAmount'];
            $model->total_merchant_discount = $sku['totalMerchantDiscount'];
            $model->total_red_discount = $sku['totalRedDiscount'];
            $model->total_tax_amount = $sku['totalTaxAmount'];
            $model->total_net_weight = $sku['totalNetWeight'];
            $model->sku_tag = $sku['skuTag'];
            $model->is_channel = $sku['isChannel'];
            $model->delivery_mode = $sku['deliveryMode'];
            $model->kol_id = $sku['kolId'] ?? '';
            $model->kol_name = $sku['kolName'] ?? '';
            $model->sku_after_sale_status = $sku['skuAfterSaleStatus'];
            if (!empty($sku['skuIdentifyCodeInfo'])) {
                $model->sku_identify_code_info = $sku['skuIdentifyCodeInfo'];
            }
            $model->item_id = $sku['itemId'];
            $model->item_name = $sku['itemName'];
            $model->save();
        }
    }

    /**
     * 获取列表
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException|ReflectionException
     */
    public function getList(array $where = []): array
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->selectList($where, '*', $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }
}
