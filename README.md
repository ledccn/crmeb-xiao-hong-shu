# 说明

Crmeb单商户系统-小红书商家自研系统，包含公共API、商品、素材、库存、订单、售后等

## 安装

`composer require ledc/crmeb-xiao-hong-shu`

## 使用说明

1. 安装完之后，请执行以下命令，安装插件的数据库迁移文件 `php think install:migrate:crmeb-xiao-hong-shu`

2. 执行数据库迁移 `php think migrate:run`

## 小红书开放平台填写的回调地址

1. 【应用授权】小红书应用授权回调地址（code换取accessToken） `https://您的域名/xiaohongshu/callback`
2. 【消息订阅】小红书消息订阅推送回调地址 `https://您的域名/xiaohongshu/notify`

## 修改过的文件

### 文件 `app/services/system/crontab/SystemCrontabServices.php`

`\app\services\system\crontab\SystemCrontabServices::crontabCommandRun` 方法

```php
// 每2分钟执行一次
new Crontab('10 */2 * * * *', function () {
    // 小红书定时任务
    \Ledc\CrmebXiaoHongShu\services\AccessTokenService::scheduler();
});

// 每12小时执行一次
new Crontab('0 */12 * * *', function () {
    \Ledc\CrmebXiaoHongShu\jobs\SyncOrderJobs::dispatch(time());
    \Ledc\CrmebXiaoHongShu\jobs\SyncCategoriesJobs::dispatch(time());
});
```

### 文件 `crmeb/services/printer/storage/YiLianYun.php`

类内 `\crmeb\services\printer\storage\YiLianYun` 引入特性

```php
use \Ledc\CrmebXiaoHongShu\traits\HasXhsOrderYiLianYun;
```

## 捐赠

![reward](reward.png)