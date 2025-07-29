<?php

namespace Ledc\CrmebXiaoHongShu\controller\api;

use app\Request;
use Ledc\CrmebXiaoHongShu\observer\XhsNotifySubject;
use Ledc\CrmebXiaoHongShu\services\AccessTokenService;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use think\facade\Log;
use think\Response;
use Throwable;
use function json;

/**
 * 小红书接口
 */
class XiaoHongShuController
{
    /**
     * 应用授权回调地址（code换取accessToken）
     * @param Request $request
     * @return Response
     */
    public function callback(Request $request): Response
    {
        try {
            $code = $request->get('code');
            $service = new AccessTokenService(XiaoHongShuHelper::merchant()->getOauthClient());
            $service->getAccessToken($code);
            return \response('<h1>success</h1>>');
        } catch (Throwable $throwable) {
            return \response($throwable->getMessage());
        }
    }

    /**
     * 小红书消息订阅推送回调地址（消息订阅）
     * @param Request $request
     * @return Response
     */
    public function notify(Request $request): Response
    {
        $secureKey = $request->secureKey();
        $rs = [
            'success' => true,
            'error_code' => 0,
            'error_msg' => ''
        ];
        try {
            log_develop(['secureKey' => $request->secureKey(), 'post' => $request->post(false), 'get' => $request->get(false)]);
            XhsNotifySubject::handle($request);
            return json($rs);
        } catch (Throwable $throwable) {
            Log::error($secureKey . ' 处理小红书推送回调异常：' . $throwable->getMessage());
            $rs['success'] = false;
            $rs['error_code'] = $throwable->getCode() ?: 400;
            $rs['error_msg'] = $throwable->getMessage();
            return json($rs);
        }
    }
}
