<?php
declare (strict_types=1);

namespace App\Service;

use App\Constants\ErrorCode;
use App\Exception\GameException;

abstract class SwooleServiceAbstract
{
    //单例对象
    private $client;
    //配置信息
    protected $config = '';
    //默认超时时间
    private $timeOut = 2.0;

    /*
     * 连接swoole
     */
    protected function connection()
    {
        if( is_object($this->client) )
        {
            return true;
        }

        if( empty($this->config) )
        {
            throw new GameException( ErrorCode::SWOOLEIPORPORTMISS );
        }

        $this->client = new \Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
        $this->client->set(array(
            'open_length_check'     => 1,
            'package_length_type'   => 'N',
            'package_length_offset' => 0,       //第N个字节是包长度的值
            'package_body_offset'   => 0,       //第几个字节开始计算长度
            'package_max_length'    => 65535,  //协议最大长度
        ));

        $ip     = $this->config['ip'] ?? '';
        $port   = $this->config['port'] ?? 0;
        $timeout    = $this->config['timeout'] ?? $this->timeOut;
        if( empty($ip) || empty($port) )
        {
            throw new GameException( ErrorCode::SWOOLEIPORPORTMISS );
        }

        $connectRs = $this->client->connect( $ip, $port, $timeout );
        if( $connectRs === false )
        {
            throw new GameException( ErrorCode::SWOOLECONNECTFAIL );
        }
        return true;
    }

    /**
     * 获取swoole客户端
     */
    protected function getSwooleClient()
    {
        if( !is_object( $this->client ) )
        {
            $this->connection();
        }
        return $this->client;
    }

    /**
     * 设置配置
     * @param array $config
     * @return mixed
     */
    abstract protected function setConfig( array $config );

    /**
     * 发送数据
     * @param $data
     * @return mixed
     */
    abstract protected function sendData( $data );

    /**
     * 接收数据
     * @return mixed
     */
    abstract protected function recvData();

}
