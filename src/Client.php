<?php
namespace Myfcomic;

use Elasticsearch\Transport;
use Myfcomic\Core\Factory\DingTalk;

class Client {

    /**
     * @var $host
     */
    private $host;

    /**
     * @var use_cache
     */
    private $use_cache = true;

    /**
     * @var __construct
     */
    public function __construct()
    {
    }

    /**
     * @return create
     */
    public  function create()
    {
        return new static();
    }

    /**
     * @var setHost webhook url
     */
    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @var use_cache
     * Delay sending or not
     */
    public function setCache(bool $use_cache = true)
    {
        $this->use_cache = $use_cache;

        return $this;
    }

    /**
     * @var build
     *  default 1:dingtalk
     */
    public function build(int $type = 1)
    {
        switch (intval($type)) {
            case 1:
                return new DingTalk($this->host, $this->use_redis);
            default:   // to do
                return false;
        }
    }


}
