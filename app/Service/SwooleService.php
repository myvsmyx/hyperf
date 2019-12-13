<?php
declare (strict_types=1);

namespace App\Service;

/**
 * Swoole收发包类
 * Class SwooleService
 * @package App\Service
 */
class SwooleService extends SwooleServiceAbstract
{
    //swoole客户端
    private $client;

    public function __construct( $config = [] )
    {
        $this->setConfig( $config );
    }

    /**
     * 设置配置
     * @param array $config
     * @return mixed|void
     */
    public function setConfig( $config = [] )
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 发送数据
     * @param $data
     * @return mixed|void
     */
    public function sendData( $data )
    {
        $this->client = $this->getSwooleClient();
        return $this->client->send($data);
    }

    /**
     * 接收数据
     * @return mixed|void
     */
    public function recvData()
    {
        $this->client = $this->getSwooleClient();
        $data = $this->client->recv();
        return $data;
    }
}