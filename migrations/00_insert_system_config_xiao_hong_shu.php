<?php

use think\migration\Migrator;

/**
 * 创建小红书系统配置
 */
class InsertSystemConfigXiaoHongShu extends Migrator
{
    /**
     * Change Method.
     */
    public function change()
    {
        $systemConfigTab = $this->fetchRow("SELECT * FROM `eb_system_config_tab` WHERE `eng_title` = 'system_config'");
        if (empty($systemConfigTab)) {
            throw new InvalidArgumentException('未找到系统配置');
        }
        // 插入配置分类表
        $this->table('system_config_tab')->insert([
            'pid' => $systemConfigTab['id'],
            'title' => '小红书开放平台',
            'eng_title' => 'xiao_hong_shu',
            'status' => 1,
            'info' => 0,
            'icon' => 's-promotion',
            'type' => 0,
            'sort' => 0,
            'menus_id' => $systemConfigTab['menus_id'],
        ])->saveData();

        $configTab = $this->fetchRow("SELECT * FROM `eb_system_config_tab` WHERE `eng_title` = 'xiao_hong_shu'");
        $config_tab_id = $configTab['id'];

        $systemConfigList = [
            [
                'menu_name' => \Ledc\XiaoHongShu\Config::CONFIG_PREFIX . 'appKey',
                'type' => 'text',
                'input_type' => 'input',
                'config_tab_id' => $config_tab_id,
                'required' => 'required:true',
                'width' => 100,
                'high' => 0,
                'info' => 'AppKey',
                'desc' => '小红书开放平台AppKey',
                'status' => 1,
            ],
            [
                'menu_name' => \Ledc\XiaoHongShu\Config::CONFIG_PREFIX . 'appSecret',
                'type' => 'text',
                'input_type' => 'input',
                'config_tab_id' => $config_tab_id,
                'required' => 'required:true',
                'width' => 100,
                'high' => 0,
                'info' => 'AppSecret',
                'desc' => '小红书开放平台AppSecret',
                'status' => 1,
            ],
            [
                'menu_name' => \Ledc\XiaoHongShu\Config::CONFIG_PREFIX . 'version',
                'type' => 'text',
                'input_type' => 'input',
                'config_tab_id' => $config_tab_id,
                'required' => 'required:true',
                'width' => 100,
                'high' => 0,
                'value' => '"2.0"',
                'info' => '接口版本',
                'desc' => '小红书开放平台接口版本；授权后凭借code获取的AccessToken',
                'status' => 1,
            ],
            [
                'menu_name' => \Ledc\XiaoHongShu\Config::CONFIG_PREFIX . 'storeId',
                'type' => 'text',
                'input_type' => 'input',
                'config_tab_id' => $config_tab_id,
                'required' => 'required:true',
                'width' => 100,
                'high' => 0,
                'info' => '默认店铺ID',
                'desc' => '授权管理-店铺ID；授权后凭借code获取的AccessToken；应用授权回调地址：https://域名/xiaohongshu/callback',
                'status' => 1,
            ],
            [
                'menu_name' => \Ledc\XiaoHongShu\Config::CONFIG_PREFIX . 'timeout',
                'type' => 'text',
                'input_type' => 'number',
                'config_tab_id' => $config_tab_id,
                'required' => '',
                'width' => 100,
                'high' => 0,
                'value' => 10,
                'info' => '请求超时时间',
                'desc' => '请求超时时间（秒）',
                'status' => 1,
            ],
            [
                'menu_name' => \Ledc\XiaoHongShu\Config::CONFIG_PREFIX . 'enabled',
                'type' => 'switch',
                'input_type' => 'input',
                'config_tab_id' => $config_tab_id,
                'required' => '',
                'width' => 0,
                'high' => 0,
                'info' => '启用小红书',
                'desc' => '小红书的全局开关',
                'status' => 1,
            ],
            [
                'menu_name' => \Ledc\XiaoHongShu\Config::CONFIG_PREFIX . 'orderReceiverChangeReprint',
                'type' => 'switch',
                'input_type' => 'input',
                'config_tab_id' => $config_tab_id,
                'required' => '',
                'width' => 0,
                'high' => 0,
                'info' => '变更重打小票',
                'desc' => '启用时，买家收货信息变更时，重新打印订单小票',
                'status' => 1,
            ],
            [
                'menu_name' => \Ledc\XiaoHongShu\Config::CONFIG_PREFIX . 'debug',
                'type' => 'switch',
                'input_type' => 'input',
                'config_tab_id' => $config_tab_id,
                'required' => '',
                'width' => 0,
                'high' => 0,
                'info' => '调试模式',
                'desc' => '调试模式将会记录小红书请求&响应日志',
                'status' => 1,
            ],
        ];
        $this->table('system_config')
            ->insert($systemConfigList)
            ->saveData();
    }
}
