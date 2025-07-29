<?php
declare (strict_types=1);

namespace Ledc\CrmebXiaoHongShu\command;

use Ledc\CrmebXiaoHongShu\services\XhsCategoriesService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use Throwable;

/**
 * 同步小红书分类
 */
class CategoriesCommand extends Command
{
    /**
     * 配置指令
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('xhs:categories')
            ->setDescription('同步小红书分类');
    }

    /**
     * 执行指令
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        try {
            XhsCategoriesService::scheduler();
            // 指令输出
            $output->writeln('同步小红书分类指令执行完毕');
        } catch (Throwable $throwable) {
            $output->writeln('<error>' . $throwable->getMessage() . '</error>');
        }
    }
}
