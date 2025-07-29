<?php

namespace Ledc\CrmebXiaoHongShu\controller\admin;

use app\Request;
use Ledc\CrmebXiaoHongShu\XiaoHongShuHelper;
use Ledc\XiaoHongShu\Config;
use Ledc\XiaoHongShu\HttpClient\MaterialClient;
use think\Response;
use Throwable;
use function Ledc\ThinkModelTrait\curl_get_remote_file;

/**
 * 小红书素材中心接口
 */
class MaterialController
{
    /**
     * @var MaterialClient
     */
    protected MaterialClient $client;
    /**
     * 白名单
     */
    protected array $whiteMethodList = [
        'material.uploadMaterial'
    ];

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->client = XiaoHongShuHelper::merchant()->getMaterialClient();
    }

    /**
     * 获取小红书签名（用于管理后台直传场景）
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function getSignature(Request $request): Response
    {
        $method = $request->get('method', 'material.uploadMaterial');
        if (!in_array($method, $this->whiteMethodList, true)) {
            return response_json()->fail('签名方法不在白名单中，请联系开发者');
        }

        $config = $this->client->getClient()->getConfig();
        $timestamp = time();
        $url = Config::API_URL;
        $headers = [
            'Content-Type: application/json; charset=utf-8'
        ];
        $result = [
            'appId' => $config->getAppKey(),
            'timestamp' => time(),
            'version' => $config->getVersion(),
            'method' => $method,
            'sign' => $config->generateSignature($method, $timestamp, $config->getVersion())
        ];
        return response_json()->success('success', compact('url', 'headers', 'result'));
    }

    /**
     * 获取素材列表
     * @method GET
     * @param Request $request
     * @return Response
     */
    public function queryMaterial(Request $request): Response
    {
        $params = $request->get(false);
        $result = $this->client->queryMaterial($params);
        return response_json()->success('success', $result);
    }

    /**
     * 上传远端素材
     * - 解决前端读取云存储跨域的问题
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request): Response
    {
        $url = $request->post('url');
        $type = $request->post('type', 0);
        if (empty($url)) {
            return response_json()->fail('图片url为空');
        }
        try {
            $contents = curl_get_remote_file($url);
            //$contents = file_get_contents(root_path() . 'runtime/logo_1071.png');

            $name = date('YmdHis') . mt_rand(1000, 9999);
            $result = $this->client->uploadMaterial($name, $type, base64_encode($contents));
            return response_json()->success('success', $result);
        } catch (Throwable $throwable) {
            return response_json()->fail($throwable->getMessage());
        }
    }

    /**
     * 上传素材
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function uploadMaterial(Request $request): Response
    {
        $name = $request->post('name/s');
        $type = $request->post('type/d');
        $materialContent = $request->post('materialContent/s');
        $result = $this->client->uploadMaterial($name, $type, $materialContent);
        return response_json()->success('success', $result);
    }

    /**
     * 修改素材
     * @method POST
     * @param Request $request
     * @return Response
     */
    public function updateMaterial(Request $request): Response
    {
        [$materialId, $materialName] = $request->postMore(['materialId', 'materialName'], true);
        $result = $this->client->updateMaterial($materialId, $materialName);
        return response_json()->success('success', $result);
    }

    /**
     * 删除素材
     * @method DELETE
     * @param Request $request
     * @return Response
     */
    public function deleteMaterial(Request $request): Response
    {
        $materialId = $request->post('materialId');
        $result = $this->client->deleteMaterial($materialId);
        return response_json()->success('success', $result);
    }
}
