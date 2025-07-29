<?php

namespace Ledc\CrmebXiaoHongShu;

use crmeb\services\SystemConfigService;
use Ledc\CrmebXiaoHongShu\services\AccessTokenService;
use Ledc\XiaoHongShu\Config;
use Ledc\XiaoHongShu\Merchant;
use think\App;

/**
 * 小红书助手
 */
class XiaoHongShuHelper
{
    /**
     * 小红书是否启用
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return sys_config(Config::CONFIG_PREFIX . 'enabled', false);
    }

    /**
     * 小红书订单买家收货信息变更时是否重打小票
     * @return bool
     */
    public static function isOrderReceiverChangeReprint(): bool
    {
        return sys_config(Config::CONFIG_PREFIX . 'orderReceiverChangeReprint', false);
    }

    /**
     * 清除缓存
     * @return void
     */
    public static function clear(): void
    {
        /** @var App $app */
        $app = app();
        if ($app->exists(Merchant::class)) {
            $app->delete(Merchant::class);
        }
    }

    /**
     * 获取配置
     * @return array
     */
    public static function getConfig(): array
    {
        // 从数据库取配置
        $result = SystemConfigService::more(array_map(fn($key) => Config::CONFIG_PREFIX . $key, Config::REQUIRE_KEYS), false);

        // 移除配置前缀
        $keys = array_map(fn($key) => substr($key, strlen(Config::CONFIG_PREFIX)), array_keys($result));

        return array_combine($keys, array_values($result));
    }

    /**
     * 获取商户对象
     * @return Merchant
     */
    public static function merchant(): Merchant
    {
        /** @var App $app */
        $app = app();
        if ($app->exists(Merchant::class)) {
            return $app->make(Merchant::class);
        }

        // 实例化
        $config = new Config(self::getConfig());
        $config->setAccessToken(fn() => AccessTokenService::accessToken($config));
        $merchant = new Merchant($config);

        // 绑定类实例到容器
        $app->instance(Config::class, $config);
        $app->instance(Merchant::class, $merchant);
        return $merchant;
    }
}
