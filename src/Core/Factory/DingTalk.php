<?php
namespace Myfcomic\Core\Factory;

use Myfcomic\Core\CheckCache;
use  Myfcomic\Core\SendMsg;
use GuzzleHttp;

/**
 * Class DingTalk
 * @package Myfcomic\Core\Factory
 */
class DingTalk implements SendMsg
{
    /**
     * @var webhook url host
     */
    public $host;

    /**
     * @var use_redis
     */
    public $use_cache = true;

    /**
     * DingTalk constructor.
     * @param $host
     * @param bool $use_cache
     */
    public function __construct($host, $use_cache = true)
    {
        $this->host = $host;

        $this->use_cache = $use_cache;
    }

    /**
     * @param array $body
     * @return array
     */
    public function send(array $body) :array
    {
        if ($this->use_cache) {
            $object = new CheckCache($body);
            $cache = $object->check();
            if (!$cache['status']) {
                return ['status' => false, 'msg' => '不能连续发送'];
            }
        }

        // format request data
        $request_data = self::formatRequertData($body['project'], $body['title'], $body['message'], $cache['send_num']);

        // send ding talk message
        $client = new \GuzzleHttp\Client();
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
     * format request Data
     */
    public static function formatRequertData(string $project, string $title, string $message, int $num = 0)
    {
        $textString = json_encode([
            "actionCard" => [
                "title"          => "$project-异常报警",
                "text"           => "### $project-服务器异常报警
#### 异常主题：" . $title . env("APP_ENV") . "
##### 触发时间：" . date("Y-m-d H:i:s", time()) . "
##### 异常次数：" . $num . " 次" . "
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