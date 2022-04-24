<?php
namespace Trgino\Http;


class Task implements TaskInterface
{
    /**
     * request method
     * @var string
     */
    protected $method;

    /**
     * @var
     */
    protected $url;

    /**
     * @var null
     */
    protected $proxy_ip = null;

    /**
     * @var null
     */
    protected $proxy_port = null;

    /**
     * @var int
     */
    protected $timeout = 10;

    /**
     * @var int
     */
    protected $transfer_timeout = 600;

    /**
     * request params
     * @var null
     */
    protected $params = null;

    protected $ch = null;

    /**
     *
     */
    const METHOD_POST = "post";

    /**
     *
     */
    const METHOD_GET = "get";

    /**
     * @param $url
     * @param null $params
     * @param int $timeout
     * @param int $transfer_timeout
     * @return Task
     */
    public static function createGet($url, $params = null, $timeout = 10, $transfer_timeout = 600)
    {
        return new Task($url, self::METHOD_GET, $params, $timeout, $transfer_timeout);
    }

    /**
     * @param $url
     * @param null $params
     * @param int $timeout
     * @param int $transfer_timeout
     * @return Task
     */
    public static function createPost($url, $params = null, $timeout = 10, $transfer_timeout = 600)
    {
        return new Task($url, self::METHOD_POST,$params, $timeout, $transfer_timeout);
    }

    /**
     * @param string $method
     * @param $url
     * @param null $params
     * @param int $timeout
     * @param int $transfer_timeout
     */
    protected function __construct($url, $method = Task::METHOD_GET,$params = null, $timeout = 10, $transfer_timeout = 600)
    {
        $this->method = $method;
        $this->url = $url;
        $this->params = $params;
        $this->timeout = $timeout;
        $this->transfer_timeout = $transfer_timeout;
    }

    /**
     * @param $host
     * @param $port
     */
    public function setProxy($host, $port)
    {
        $this->proxy_ip = $host;
        $this->proxy_port = $port;
    }

    /**
     * @param int $timeout
     * @param $transfer_timeout
     */
    public function setTimeout($timeout = 10, $transfer_timeout = 600)
    {
        $this->timeout = $timeout;
        $this->transfer_timeout = $transfer_timeout;
    }

    /**
     * @param int $transfer_timeout
     */
    public function setTransferTimeout($transfer_timeout = 600)
    {
        $this->transfer_timeout = $transfer_timeout;
    }

    /**
     * @param null $params
     */
    public function setParams($params = null)
    {
        $this->params = $params;
    }

    /**
     * get curl resource
     * @return resource curl
     */
    public function getCurl()
    {
        return $this->ch;
    }

    public function createCurl()
    {
        $this->ch = curl_init();

        if ($this->ch === false) {
            throw new \RuntimeException("init curl failed");
        }

        if (!is_null($this->proxy_ip) && !is_null($this->proxy_port)) {
            $proxy = "http://{$this->proxy_ip}:{$this->proxy_port}";
            curl_setopt($this->ch, CURLOPT_PROXY, $proxy);
        }

        $url = $this->url;
        if ($this->method == self::METHOD_GET && (is_array($this->params) || is_object($this->params))) {
            $url .= http_build_query($this->params);
        }
        if ($this->method == self::METHOD_POST && !is_null($this->params)) {
            curl_setopt($this->ch, CURLOPT_POST, 1);
            if (is_array($this->params) || is_object($this->params)) {
                $post_field = http_build_query($this->params);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_field);
            } else {
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->params);
            }
        }

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->transfer_timeout);

        return $this->ch;
    }
}