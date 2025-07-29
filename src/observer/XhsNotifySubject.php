<?php

namespace Ledc\CrmebXiaoHongShu\observer;

use app\Request;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\ThinkModelTrait\RedisLocker;
use Ledc\XiaoHongShu\Notify\XhsNotify;
use think\facade\Event;

/**
 * 小红书消息订阅推送通知回调主题
 */
class XhsNotifySubject extends \Ledc\XiaoHongShu\Notify\XhsNotifySubject
{
    /**
     * 初始化
     * @return void
     */
    protected function initialize(): void
    {
        $this->register = [
            XhsNotifyObserver::class
        ];
    }

    /**
     * 处理小红书消息订阅推送回调
     * @param Request $request
     */
    public static function handle(Request $request): void
    {
        $baseUrl = $request->baseUrl();
        $params = $request->get(false) ?: [];
        $params['timestamp'] = $request->header('timestamp');
        $params['app-key'] = $request->header('app-key');
        $params['sign'] = $request->header('sign');
        $body = $request->post(false);
        foreach ($body as $item) {
            $xhsNotify = new XhsNotify(
                $item['msgTag'] ?? '',
                $item['sellerId'] ?? '',
                (string)($item['data'] ?? ''),
            );
            // 验证小红书消息订阅签名
            XiaoHongShuHelper::merchant()->getConfig()->verifyNotifySignature($xhsNotify, $baseUrl, $params);

            // 并发锁
            $lockKey = implode('_', ['xiaohongshu_notify', $xhsNotify->getMsgTag(), $xhsNotify->getSellerId(), md5((string)($item['data'] ?? ''))]);
            $locker = new RedisLocker($lockKey);
            if ($locker->acquire()) {
                // 触发ThinkPHP原生事件
                Event::trigger(XhsNotify::class, $xhsNotify);
                // 触发自定义事件
                $subject = new XhsNotifySubject($xhsNotify);
                $subject->notify();
                // 释放锁
                $locker->release();
            }
        }
    }
}
