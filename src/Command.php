<?php

namespace Ledc\CrmebXiaoHongShu;

use Ledc\ThinkModelTrait\Contracts\HasMigrationCommand;
use think\console\Input;
use think\console\Output;

/**
 * 安装数据库迁移文件
 */
class Command extends \think\console\Command
{
    use HasMigrationCommand;

    /**
     * @return void
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('install:migrate:crmeb-xiao-hong-shu')
            ->setDescription('安装插件的数据库迁移文件');

        // 迁移文件映射
        $this->setFileMaps([
            'InsertSystemConfigXiaoHongShu' => dirname(__DIR__) . '/migrations/00_insert_system_config_xiao_hong_shu.php',
            'CreateXhsOrder' => dirname(__DIR__) . '/migrations/01_create_xhs_order.php',
            'CreateXhsOrderLogs' => dirname(__DIR__) . '/migrations/02_create_xhs_order_logs.php',
            'CreateXhsOrderProduct' => dirname(__DIR__) . '/migrations/03_create_xhs_order_product.php',
            'CreateXhsOrderLogistics' => dirname(__DIR__) . '/migrations/04_create_xhs_order_logistics.php',
            'CreateXhsCategories' => dirname(__DIR__) . '/migrations/05_create_xhs_categories.php',
        ]);
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $this->eachFileMaps($input, $output);
    }
}
