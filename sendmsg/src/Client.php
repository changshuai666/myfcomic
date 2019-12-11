<?
namespace Myfcomic\Sendmsg;

use Myfcomic\Sendmsg\Core\Factory\DingTalk;

class Client {
    private $host;

    private $use_redis = true;

    public function __construct()
    {
    }

    /**
     * @return static
     */
    public  function create()
    {
        return new static();
    }

    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    public function setRedis(bool $use_redis = true)
    {
        $this->use_redis = $use_redis;

        return $this;
    }

    public function build(int $type = 1)
    {
        switch (intval($type)) {
            case 1:
                return new DingTalk($this->host, $this->use_redis);
            default:
                return false;
        }
    }


}
