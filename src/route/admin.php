<?php

use app\adminapi\middleware\AdminAuthTokenMiddleware;
use app\adminapi\middleware\AdminCheckRoleMiddleware;
use app\adminapi\middleware\AdminLogMiddleware;
use app\http\middleware\AllowOriginMiddleware;
use Ledc\CrmebXiaoHongShu\controller\admin\AfterSaleController;
use Ledc\CrmebXiaoHongShu\controller\admin\CategoriesController;
use Ledc\CrmebXiaoHongShu\controller\admin\CommonController;
use Ledc\CrmebXiaoHongShu\controller\admin\DeliveryController;
use Ledc\CrmebXiaoHongShu\controller\admin\InventoryController;
use Ledc\CrmebXiaoHongShu\controller\admin\MaterialController;
use Ledc\CrmebXiaoHongShu\controller\admin\OrderController;
use Ledc\CrmebXiaoHongShu\controller\admin\ProductController;
use think\facade\Route;

/**
 * 小红书 后台管理相关路由
 */
Route::group('xiao_hong_shu', function () {
    /**
     * 公共API
     */
    Route::group('common', function () {
        // 获取商品分类
        Route::get('getCategories', implode('@', [CommonController::class, 'getCategories']));
        // 由末级分类获取规格（新）
        Route::get('getVariations', implode('@', [CommonController::class, 'getVariations']));
        // 由末级分类获取属性
        Route::get('getAttributeLists', implode('@', [CommonController::class, 'getAttributeLists']));
        // 由属性获取属性值
        Route::get('getAttributeValues', implode('@', [CommonController::class, 'getAttributeValues']));
        // 获取快递公司信息
        Route::get('getExpressCompanyList', implode('@', [CommonController::class, 'getExpressCompanyList']));
        // 获取物流方案列表
        Route::get('getLogisticsList', implode('@', [CommonController::class, 'getLogisticsList']));
        // 运费模版列表
        Route::get('getCarriageTemplateList', implode('@', [CommonController::class, 'getCarriageTemplateList']));
        // 运费模版详情
        Route::get('getCarriageTemplate', implode('@', [CommonController::class, 'getCarriageTemplate']));
        // 获取品牌信息
        Route::get('brandSearch', implode('@', [CommonController::class, 'brandSearch']));
        // 获取物流模式列表
        Route::get('logisticsMode', implode('@', [CommonController::class, 'logisticsMode']));
        // 批量获取发货时间规则
        Route::get('getDeliveryRule', implode('@', [CommonController::class, 'getDeliveryRule']));
        // 获取商家地址库
        Route::get('getAddressRecord', implode('@', [CommonController::class, 'getAddressRecord']));
        // 商品标题类目预测
        Route::post('categoryMatch', implode('@', [CommonController::class, 'categoryMatch']));
        // 获取预测类目（新）
        Route::post('categoryMatchV2', implode('@', [CommonController::class, 'categoryMatchV2']));
        // 判断文本中是否含有违禁词
        Route::post('checkForbiddenKeyword', implode('@', [CommonController::class, 'checkForbiddenKeyword']));
    });

    /**
     * 素材中心接口
     */
    Route::group('material', function () {
        // 获取小红书签名（用于管理后台直传场景）
        Route::get('getSignature', implode('@', [MaterialController::class, 'getSignature']));
        // 上传远端素材（解决前端读取云存储跨域的问题）
        Route::post('upload', implode('@', [MaterialController::class, 'upload']));
        // 获取素材列表
        Route::get('queryMaterial', implode('@', [MaterialController::class, 'queryMaterial']));
        // 上传素材
        Route::post('uploadMaterial', implode('@', [MaterialController::class, 'uploadMaterial']));
        // 修改素材
        Route::post('updateMaterial', implode('@', [MaterialController::class, 'updateMaterial']));
        // 删除素材
        Route::delete('deleteMaterial', implode('@', [MaterialController::class, 'deleteMaterial']));
    });

    /**
     * 商品接口
     */
    Route::group('product', function () {
        // 获取商品Sku列表完整版
        Route::get('getDetailSkuList', implode('@', [ProductController::class, 'getDetailSkuList']));
        // 更新物流方案
        Route::post('updateSkuLogisticsPlan', implode('@', [ProductController::class, 'updateSkuLogisticsPlan']));
        // 商品SKU上下架
        Route::post('updateSkuAvailable', implode('@', [ProductController::class, 'updateSkuAvailable']));
        // 更新ITEM V2
        Route::post('updateItemV2', implode('@', [ProductController::class, 'updateItemV2']));
        // 删除ITEM V2
        Route::delete('deleteItemV2', implode('@', [ProductController::class, 'deleteItemV2']));
        // 更新SKU V2
        Route::post('updateSkuV2', implode('@', [ProductController::class, 'updateSkuV2']));
        // 删除SKU V2
        Route::delete('deleteSkuV2', implode('@', [ProductController::class, 'deleteSkuV2']));
        // 查询Item列表
        Route::post('searchItemList', implode('@', [ProductController::class, 'searchItemList']));
        // 获取ITEM详情
        Route::get('getItemInfo', implode('@', [ProductController::class, 'getItemInfo']));
        // 修改SKU价格
        Route::post('updateSkuPrice', implode('@', [ProductController::class, 'updateSkuPrice']));
        // 修改商品主图、主图视频
        Route::post('updateItemImage', implode('@', [ProductController::class, 'updateItemImage']));
        // 创建商品Item+Sku（新）
        Route::post('createItemAndSku', implode('@', [ProductController::class, 'createItemAndSku']));
        // 更新商品Item+Sku（新）
        Route::post('updateItemAndSku', implode('@', [ProductController::class, 'updateItemAndSku']));
    });

    /**
     * 库存接口
     */
    Route::group('inventory', function () {
        // 获取商品SKU库存
        Route::get('getSkuStock', implode('@', [InventoryController::class, 'getSkuStock']));
        // 同步商品SKU库存
        Route::post('syncSkuStock', implode('@', [InventoryController::class, 'syncSkuStock']));
        // 增减商品SKU库存
        Route::post('incSkuStock', implode('@', [InventoryController::class, 'incSkuStock']));
        // 获取商品SKU库存（V2）
        Route::get('getSkuStockV2', implode('@', [InventoryController::class, 'getSkuStockV2']));
        // 同步商品SKU库存（V2）
        Route::post('syncSkuStockV2', implode('@', [InventoryController::class, 'syncSkuStockV2']));
        // 创建仓库
        Route::post('create', implode('@', [InventoryController::class, 'create']));
        // 修改仓库
        Route::post('update', implode('@', [InventoryController::class, 'update']));
        // 仓库列表
        Route::get('list', implode('@', [InventoryController::class, 'list']));
        // 仓库详情
        Route::get('info', implode('@', [InventoryController::class, 'info']));
        // 设置仓库覆盖地区
        Route::post('setCoverage', implode('@', [InventoryController::class, 'setCoverage']));
        // 设置仓库优先级
        Route::post('setPriority', implode('@', [InventoryController::class, 'setPriority']));
    });

    /**
     * 订单接口
     */
    Route::group('order', function () {
        // 获取订单列表
        Route::get('list', implode('@', [OrderController::class, 'list']));
        // 获取订单日志列表
        Route::get('logs_list', implode('@', [OrderController::class, 'logsList']));
        // 打印订单小票
        Route::post('print/:id', implode('@', [OrderController::class, 'print']));
        // 获取订单列表
        Route::get('getOrderList', implode('@', [OrderController::class, 'getOrderList']));
        // 获取订单详情
        Route::get('getOrderDetail', implode('@', [OrderController::class, 'getOrderDetail']));
        // 获取订单收货人信息
        Route::post('getOrderReceiverInfo', implode('@', [OrderController::class, 'getOrderReceiverInfo']));
        // 修改订单备注
        Route::post('modifySellerMarkInfo', implode('@', [OrderController::class, 'modifySellerMarkInfo']));
        // 订单发货
        Route::post('orderDeliver', implode('@', [OrderController::class, 'orderDeliver']));
        // 修改订单快递单号
        Route::post('modifyOrderExpressInfo', implode('@', [OrderController::class, 'modifyOrderExpressInfo']));
        // 订单物流轨迹
        Route::get('getOrderTracking', implode('@', [OrderController::class, 'getOrderTracking']));
        // 海关申报信息
        Route::get('getOrderDeclareInfo', implode('@', [OrderController::class, 'getOrderDeclareInfo']));
        // 批量上传序列号
        Route::post('batchBindSkuIdentifyInfo', implode('@', [OrderController::class, 'batchBindSkuIdentifyInfo']));
        // 跨境清关支持口岸
        Route::get('getSupportedPortList', implode('@', [OrderController::class, 'getSupportedPortList']));
        // 跨境重推支付单
        Route::post('resendBondedPaymentRecord', implode('@', [OrderController::class, 'resendBondedPaymentRecord']));
        // 跨境商品备案信息同步
        Route::post('syncItemCustomsInfo', implode('@', [OrderController::class, 'syncItemCustomsInfo']));
        // 跨境商品备案信息查询
        Route::get('getCustomsInfo', implode('@', [OrderController::class, 'getCustomsInfo']));
        // 小包批次创建
        Route::post('createTransferBatch', implode('@', [OrderController::class, 'createTransferBatch']));
        // 开票列表查询
        Route::get('getInvoiceList', implode('@', [OrderController::class, 'getInvoiceList']));
        // 开票结果回传（正向蓝票开具）
        Route::post('confirmInvoice', implode('@', [OrderController::class, 'confirmInvoice']));
        // 发票冲红（逆向冲红）
        Route::post('reverseInvoice', implode('@', [OrderController::class, 'reverseInvoice']));
        // 批量解密
        Route::post('batchDecrypt', implode('@', [OrderController::class, 'batchDecrypt']));
        // 批量脱敏
        Route::post('batchDesensitise', implode('@', [OrderController::class, 'batchDesensitise']));
        // 批量获取索引串
        Route::post('batchIndex', implode('@', [OrderController::class, 'batchIndex']));
        // 获取KOS员工数据
        Route::get('getKosData', implode('@', [OrderController::class, 'getKosData']));
        // 创建三方商品备案信息
        Route::post('createItemCustomsInfo', implode('@', [OrderController::class, 'createItemCustomsInfo']));
    });

    /**
     * 售后接口
     */
    Route::group('afterSale', function () {
        // 获取售后列表（新）
        Route::post('listAfterSaleInfos', implode('@', [AfterSaleController::class, 'listAfterSaleInfos']));
        // 获取售后详情（新）
        Route::post('getAfterSaleInfo', implode('@', [AfterSaleController::class, 'getAfterSaleInfo']));
        // 售后审核（新）
        Route::post('auditReturns', implode('@', [AfterSaleController::class, 'auditReturns']));
        // 售后确认收货（新）
        Route::post('confirmReceive', implode('@', [AfterSaleController::class, 'confirmReceive']));
        // 售后换货确认收货并发货
        Route::post('receiveAndShip', implode('@', [AfterSaleController::class, 'receiveAndShip']));
        // 获取售后拒绝原因
        Route::get('rejectReasons', implode('@', [AfterSaleController::class, 'rejectReasons']));
    });

    /**
     * 闪送接口
     */
    Route::group('shansong', function () {
        // ★同城配送运力ID枚举
        Route::get('trans', implode('@', [DeliveryController::class, 'trans']));
        // ★同城配送运力单状态枚举（所有运力共用）
        Route::get('status', implode('@', [DeliveryController::class, 'status']));
        // ★创建配送单（呼叫骑手）
        Route::post('create', implode('@', [DeliveryController::class, 'create']));
        // ★订单计费
        Route::post('calculate', implode('@', [DeliveryController::class, 'calculate']));
        // ★查询订单详情
        Route::get('order_info', implode('@', [DeliveryController::class, 'orderInfo']));
        // ★查询闪送员位置信息
        Route::get('courier_info/:trans_order_id', implode('@', [DeliveryController::class, 'courierInfo']));
        // ★订单预取消
        Route::post('cancel_pre/:trans_order_id', implode('@', [DeliveryController::class, 'preAbortOrder']));
        // ★订单取消
        Route::post('cancel', implode('@', [DeliveryController::class, 'abortOrder']));
    });

    /**
     * 分类接口
     */
    Route::group('categories', function () {
        // 获取分类列表
        Route::get('list', implode('@', [CategoriesController::class, 'list']));
        // 获取分类详情
        Route::get('read', implode('@', [CategoriesController::class, 'read']));
        // 同步分类
        Route::put('sync', implode('@', [CategoriesController::class, 'sync']));
    });
})->middleware([
    AllowOriginMiddleware::class,
    AdminAuthTokenMiddleware::class,
    AdminCheckRoleMiddleware::class,
    AdminLogMiddleware::class
]);
