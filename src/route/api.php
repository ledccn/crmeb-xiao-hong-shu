<?php

use Ledc\CrmebXiaoHongShu\controller\api\XiaoHongShuController;
use Ledc\CrmebXiaoHongShu\Service;
use think\facade\Route;

/**
 * 小红书前端路由
 */
Route::group(Service::ROUTE_PREFIX, function () {
    // 小红书应用授权回调地址（code换取accessToken）
    Route::any('callback', implode('@', [XiaoHongShuController::class, 'callback']));
    // 小红书消息订阅推送回调地址（消息订阅）
    Route::any('notify', implode('@', [XiaoHongShuController::class, 'notify']));
});
