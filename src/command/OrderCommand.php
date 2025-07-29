<?php
declare (strict_types=1);

namespace Ledc\CrmebXiaoHongShu\command;

use Ledc\CrmebXiaoHongShu\services\XhsOrderService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use Throwable;

/**
 * 小红书订单指令
 */
class OrderCommand extends Command
{
    /**
     * 配置指令
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('xhs:order')
            ->setDescription('小红书订单指令');
    }

    /**
     * 执行指令
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        try {
            XhsOrderService::scheduler();
            //$xhsOrder = EbXhsOrder::findOrEmpty(1);
            //PrinterService::orderReceiverChangePrintTicket($xhsOrder);
            //PrinterService::orderAfterSaleCreatePrintTicket($xhsOrder);
            // 指令输出
            $output->writeln('小红书订单指令执行完毕');
        } catch (Throwable $throwable) {
            $output->writeln('<error>' . $throwable->getMessage() . '</error>');
        }
    }
}
