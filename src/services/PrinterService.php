<?php

namespace Ledc\CrmebXiaoHongShu\services;

use crmeb\exceptions\AdminException;
use crmeb\services\printer\Printer;
use Ledc\CrmebXiaoHongShu\model\EbXhsOrder;
use Ledc\ThinkModelTrait\RedisUtils;
use think\facade\Log;
use Throwable;

/**
 * 小红书订单打印服务
 */
class PrinterService
{
    /**
     * 订单打印小票的TTL
     */
    public const PRINT_TICKET_CACHE_TTL = 86400 * 10;

    /**
     * 获取打印服务
     * - 代码来源于：\app\services\order\StoreOrderServices::orderPrintTicket
     * @return Printer
     */
    public static function handler(): Printer
    {
        $switch = (bool)sys_config('pay_success_printing_switch');
        if (!$switch) {
            throw new AdminException(400464);
        }
        if (sys_config('print_type', 1) == 1) {
            $name = 'yi_lian_yun';
            $config = [
                'clientId' => sys_config('printing_client_id', ''),
                'apiKey' => sys_config('printing_api_key', ''),
                'partner' => sys_config('develop_id', ''),
                'terminal' => sys_config('terminal_number', '')
            ];
            if (!$config['clientId'] || !$config['apiKey'] || !$config['partner'] || !$config['terminal']) {
                throw new AdminException(400465);
            }
        } else {
            $name = 'fei_e_yun';
            $config = [
                'feyUser' => sys_config('fey_user', ''),
                'feyUkey' => sys_config('fey_ukey', ''),
                'feySn' => sys_config('fey_sn', '')
            ];
            if (!$config['feyUser'] || !$config['feyUkey'] || !$config['feySn']) {
                throw new AdminException(400465);
            }
        }
        return new Printer($name, $config);
    }

    /**
     * 获取打印小票的key
     * @param string $orderId
     * @return string
     */
    public static function getPrintTicketKey(string $orderId): string
    {
        return 'xhs_order_print_ticket_' . $orderId;
    }

    /**
     * 打印小红书订单
     * @param EbXhsOrder $xhsOrder
     * @return bool
     */
    public static function orderPrintTicket(EbXhsOrder $xhsOrder): bool
    {
        try {
            $printer = self::handler();
            $res = $printer->setXhsOrderPrinterContent($xhsOrder)->startPrinter();
            if (!$res) {
                throw new AdminException($printer->getError());
            }
            // 打印成功，累加打印次数
            $cacheKey = PrinterService::getPrintTicketKey($xhsOrder->order_id);
            RedisUtils::incr($cacheKey, self::PRINT_TICKET_CACHE_TTL);
            return true;
        } catch (Throwable $throwable) {
            Log::error('小红书打印订单失败：' . $throwable->getMessage());
            throw new AdminException($throwable->getMessage());
        }
    }

    /**
     * 订单收货信息变更打印小票
     * @param EbXhsOrder $xhsOrder
     * @return bool
     */
    public static function orderReceiverChangePrintTicket(EbXhsOrder $xhsOrder): bool
    {
        try {
            $printer = self::handler();
            $res = $printer->setXhsOrderReceiverChange($xhsOrder)->startPrinter();
            if (!$res) {
                throw new AdminException($printer->getError());
            }
            return true;
        } catch (Throwable $throwable) {
            Log::error('小红书订单收货信息变更打印小票，异常：' . $throwable->getMessage());
            return false;
        }
    }

    /**
     * 售后申请打印小票
     * @param EbXhsOrder $xhsOrder
     * @return bool
     */
    public static function orderAfterSaleCreatePrintTicket(EbXhsOrder $xhsOrder): bool
    {
        try {
            $printer = self::handler();
            $res = $printer->setXhsOrderAfterSaleCreate($xhsOrder)->startPrinter();
            if (!$res) {
                throw new AdminException($printer->getError());
            }
            return true;
        } catch (Throwable $throwable) {
            Log::error('小红书订单售后申请打印小票，异常：' . $throwable->getMessage());
            return false;
        }
    }
}
