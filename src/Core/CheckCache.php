<?php
namespace Myfcomic\Core;
use Illuminate\Support\Facades\Cache;

/**
 * Class CheckCache
 * @package Myfcomic\Core
 */
class CheckCache {

    /**
     * @var cache_info array
     */
    private $cache_info = [];

    /**
     * @var cache key string
     */
    private $key;

    /**
     * @var int
     *  cache time
     */
    private $cache_time = 0.1; // min

    /**
     * @var set_cache_time
     */
    private $set_cache_time = 86400;  // s

    /**
     * CheckCache constructor.
     * @param array $body
     */
    public function __construct(array $body)
    {
        $this->cache_info = Cache::get(md5($body['message']));

        $this->key = md5($body['message']);

        !empty($this->cache_info) && $this->cache_info = json_decode($this->cache_info, true);
    }

    /**
     * @return array
     * Verify that messages can be sent
     */
    public function check()
    {
        if (empty($this->cache_info)) {
            Cache::put($this->key, json_encode(['time' => time(), 'send_num' => 1]), $this->set_cache_time);

            return ['status' => true, 'send_num' => 1];
        }

        if (((time() - $this->cache_info['time']) / 60) > $this->cache_time) {

            Cache::setex($this->key, json_encode(['time' => time(), 'send_num' => $this->cache_info['send_num'] + 1]), $this->set_cache_time);

            return ['status' => true, 'send_num' => $this->cache_info['send_num'] + 1];
        }

        Cache::setex($this->key, json_encode(['time' => $this->cache_info['time'], 'send_num' => $this->cache_info['send_num'] + 1]), $this->set_cache_time);

        return ['status' => false, 'send_num' => $this->cache_info['send_num'] + 1];
    }
}