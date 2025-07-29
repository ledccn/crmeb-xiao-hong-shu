<?php

namespace Ledc\CrmebXiaoHongShu\locker;

use Ledc\ThinkModelTrait\RedisLocker;

/**
 * 小红书订单操作锁
 * @method static RedisLocker create(string $orderId) 创建锁
 * @method static RedisLocker syncOrderReceiverInfo(string $orderId) 同步&解密订单收货人信息
 */
class OrderLocker extends RedisLocker
{
}
