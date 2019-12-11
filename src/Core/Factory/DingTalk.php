<?php

namespace Myfcomic\Core\Factory;

use  Myfcomic\Core\SendMsg;
use GuzzleHttp;
use Illuminate\Support\Facades\Redis;

class DingTalk implements SendMsg
{
    public $host;

    public $use_cache = true;

    public function __construct($host, $use_cache = true)
    {
        $this->host = $host;

        $this->use_cache = $use_cache;
    }

    public function send(array $body)
    {
        if ($this->use_cache) {
            $cache_num = Redis::get(md5($body['message']));
            if (!empty($cache_num) && ((($cache_num + 1) % 10) != 0)) {
                Redis::setex(md5($body['message']), 10, $cache_num + 1);

                return ['status' => false, 'msg' => '连续发送操作失败'];
            }

            empty($cache_num) && $cache_num = 0;

            Redis::setex(md5($body['message']), 10, $cache_num + 1);
        }

        $client = new \GuzzleHttp\Client();
        $request_data = self::formatRequertData($body['project'], $body['title'], $body['message'] . ' 异常次数：' . ($cache_num + 1) . '次');
        $result = $client->post($this->host, [
            'headers' => [
                'content-type' => 'application/json'
            ],
            'body'    => $request_data,
        ]);

        if (empty(json_decode($result->getBody()->getContents(), true)['errmsg']) || json_decode($result->getBody()->getContents(), true)['errcode'] != 0) {
            return ['status' => false, 'msg' => '发送至钉钉失败'];
        }

        return ['status' => true, 'msg' => '发送成功'];
    }

    /**
     * @param string $title
     * @param string $message
     * @return false|string
     * 组装消息内容
     */
    public static function formatRequertData(string $project, string $title, string $message)
    {
        $textString = json_encode([
            "actionCard" => [
                "title"          => "$project-服务器异常报警",
                "text"           => "### $project-服务器异常报警
#### 异常主题：" . $title . env("APP_ENV") . "
##### 触发时间：" . date("Y-m-d H:i:s", time()) . "
##### 错误信息：" . $message,
                "hideAvatar"     => "0",
                "btnOrientation" => "0",
                "btns"           => [
                ]
            ],
            "msgtype"    => "actionCard"
        ]);

        return $textString;
    }
}