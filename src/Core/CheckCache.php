<?php
namespace Myfcomic\Core;
use Illuminate\Support\Facades\Redis;

/**
 * Class CheckCache
 * @package Myfcomic\Core
 */
class CheckCache {

    /**
     * @var cache_info array
     */
    private $cache_info;

    /**
     * @var cache key string
     */
    private $key;

    /**
     * @var int
     *  cache time
     */
    private $cache_time = 5; //min

    /**
     * CheckCache constructor.
     * @param array $body
     */
    public function __construct(array $body)
    {
        $this->cache_info = Redis::get(md5($body['message']));

        $this->key = md5($body['message']);

        !empty($this->cache_info) ? json_decode($this->cache_info, true) : [];
    }

    /**
     * @return array
     * Verify that messages can be sent
     */
    public function check()
    {
        if (empty($this->cache_info)) {
            Redis::setex($this->key, 86400, json_encode(['time' => time(), 'send_num' => 1]));

            return ['status' => true, 'send_num' => 1];
        }

        if (((time() - $this->cache_info['time']) / 60) > $this->cache_time) {

            Redis::setex($this->key, 86400, json_encode(['time' => time(), 'send_num' => $this->cache_info['send_num'] + 1]));

            return ['status' => true, 'send_num' => $this->cache_info['send_num'] + 1];
        }

        Redis::setex($this->key, 86400, json_encode(['time' => $this->cache_info['time'], 'send_num' => $this->cache_info['send_num'] + 1]));

        return ['status' => false, 'send_num' => $this->cache_info['send_num'] + 1];
    }
}