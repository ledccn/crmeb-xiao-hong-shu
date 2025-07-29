<?php

namespace Ledc\CrmebXiaoHongShu\services;

use app\model\other\Cache;
use app\services\other\CacheServices;
use InvalidArgumentException;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\XiaoHongShu\Config;
use Ledc\XiaoHongShu\HttpClient\OauthClient;
use Ledc\XiaoHongShu\Parameters\OauthParameters;
use think\exception\ValidateException;
use think\facade\Log;
use Throwable;

/**
 * AccessToken服务
 */
class AccessTokenService
{
    /**
     * @return string
     */
    protected OauthClient $oauthClient;

    /**
     * 构造函数
     * @param OauthClient $oauthClient
     */
    public function __construct(OauthClient $oauthClient)
    {
        $this->oauthClient = $oauthClient;
    }

    /**
     * 定时任务
     */
    public static function scheduler(): void
    {
        if (XiaoHongShuHelper::isEnabled()) {
            XiaoHongShuHelper::clear();
            $service = new AccessTokenService(XiaoHongShuHelper::merchant()->getOauthClient());
            $service->refreshAccessToken();
        }
    }

    /**
     * 从数据库获取AccessToken
     * @param Config $config
     * @return string
     */
    public static function accessToken(Config $config): string
    {
        $key = $config->getCacheKeyAccessToken();
        /** @var Cache $cache */
        $cache = Cache::where(['key' => $key])->where('expire_time', '>', time())->findOrEmpty();
        return $cache->isEmpty() ? '' : json_decode($cache->result, true);
    }

    /**
     * 获取AccessToken
     * @param string $code 授权码
     * @return bool
     */
    public function getAccessToken(string $code): bool
    {
        try {
            $result = $this->oauthClient->getAccessToken($code);
            $this->cache($result);
            return true;
        } catch (Throwable $throwable) {
            throw new ValidateException($throwable->getMessage());
        }
    }

    /**
     * 刷新AccessToken
     * @return void
     */
    public function refreshAccessToken(): void
    {
        try {
            $config = $this->oauthClient->getClient()->getConfig();
            $key = $config->getCacheKeyOauth();
            /** @var Cache $cache */
            $cache = Cache::where(['key' => $key])->findOrEmpty();
            if ($cache->isEmpty() || empty($cache->result)) {
                throw new InvalidArgumentException('请先授权店铺，凭code获取AccessToken');
            }
            if ($cache->expire_time < time()) {
                throw new InvalidArgumentException('RefreshToken已过期，请重新授权店铺');
            }

            $oauth = new OauthParameters(json_decode($cache->result, true));
            // 获取过期时间：毫秒转秒
            $expireTime = $oauth->accessTokenExpiresAt / 1000;
            // 提前20分钟刷新
            if ($expireTime <= time() + 1200) {
                $result = $this->oauthClient->refreshToken($oauth->refreshToken);
                $this->cache($result);
            }
        } catch (Throwable $throwable) {
            Log::error('小红书刷新AccessToken失败：' . $throwable->getMessage());
        }
    }

    /**
     * 缓存
     * @param array $result
     * @return void
     */
    public function cache(array $result): void
    {
        $oauth = new OauthParameters($result);
        /** @var CacheServices $cacheServices */
        $cacheServices = app()->make(CacheServices::class);
        log_develop($result);

        $config = $this->oauthClient->getClient()->getConfig();
        $cacheServices->setDbCache($config->getCacheKeyOauth(), $result, (int)($oauth->refreshTokenExpiresAt / 1000 - time()));
        $cacheServices->setDbCache($config->getCacheKeyAccessToken(), $oauth->accessToken, (int)($oauth->accessTokenExpiresAt / 1000 - time()));
    }
}
