<?php
declare (strict_types=1);

namespace Ledc\CrmebXiaoHongShu\command;

use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\XiaoHongShu\Parameters\Product\SearchItemList;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use Throwable;

/**
 * 小红书商品指令
 */
class ProductCommand extends Command
{
    /**
     * 配置指令
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('xhs:product')
            ->setDescription('小红书商品指令');
    }

    /**
     * 执行指令
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        try {
            $productClient = XiaoHongShuHelper::merchant()->getProductClient();
            $parameter = new SearchItemList();
            $result = $productClient->searchItemList($parameter);
            $output->writeln('<info>' . json_encode($result, JSON_UNESCAPED_UNICODE) . '</info>');
            // 指令输出
            $output->writeln('小红书商品指令执行完毕');
        } catch (Throwable $throwable) {
            $output->writeln('<error>' . $throwable->getMessage() . '</error>');
        }
    }
}
