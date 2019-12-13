<?php
declare (strict_types=1);

namespace App\Service;

use App\Constants\CommonCode;
use App\Helper\Socketpacket;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;

class SocketService implements SocketServiceInterface
{

    //超时时间
    protected $timeout = 2.0;

    //玩家的游戏信息字段
    private $gameField = array(
        'money'     => 1,
        'safebox'   => 2,
        'gold'      => 3,
        'exp'       => 4,
        'game'      => 5,
        'point'     => 6,
    );

    /**
     * @Inject
     * @var SwooleService
     */
    private $swooleservice;

    /**
     * @Inject
     * @var Socketpacket
     */
    private $socketpacket;


    // 命令字
    private $cmd = [
        'GAMEINFO'     => 0x1002,//获取用户游戏币经验等信息
        'UPDATEMONEY'  => 0x1004,//更新游戏币
        'UPDATEINFO'   => 0x1005,//更新用户信息
        'GETPERINFO'  => 0x1007,//获取特定用户信息
        'UPDATEGOLD'   => 0x100A ,//更新金币
        'BROADCAST'     => 0x7001,//server多人广播
        'GETCONFIG'    => 0x10002,//获取配置信息
        'SETCONFIG'    => 0x10003,//设置配置
        'UPDATEGAMEBUFF' => 0x100b,//更新用户通用字段
    ];

    /**
     * 更新用户游戏币
     * @param int $uid
     * @param int $money
     * @param int $actid
     * @param int $gid
     * @param string $desc
     * @return mixed|void
     */
    public function updateMoney( $uid = 0, $money = 0, $actid = 0, $gid = 0, $desc = '' )
    {
        if( empty($uid) || empty($actid) || empty($gid) )
        {
            return false;
        }

        //获取配置
        $moneyServerConfig = $this->getMoneyServerConfig( $uid );

        //打包
        $this->socketpacket->init();
        $this->socketpacket->WriteBegin($this->cmd['UPDATEMONEY']);
        $this->socketpacket->WriteInt($uid);
        $this->socketpacket->WriteInt64((int)$money);
        $this->socketpacket->WriteInt($actid);
        $this->socketpacket->WriteInt(0);//gameid
        $this->socketpacket->WriteInt($gid);
        $this->socketpacket->WriteString($desc);
        $this->socketpacket->WriteEnd();
        $packetBuffer = $this->socketpacket->GetPacketBuffer();

        //传递配置参数 __invoke实现
        $this->swooleservice($moneyServerConfig);
        $rs = $this->swooleservice->sendData($packetBuffer);
        var_dump($rs);
    }

    /**
     * 获取玩家游戏信息
     * @param $uid
     */
    public function getGameInfo( $uid )
    {
        $uid = intval($uid);
        //获取配置
        $moneyServerConfig = $this->getMoneyServerConfig( $uid );
        $size = count( $this->gameField );

        //打包
        $this->socketpacket->init();
        $this->socketpacket->WriteBegin($this->cmd['GETPERINFO']);
        $this->socketpacket->WriteInt($uid);
        $this->socketpacket->WriteByte($size);
        foreach ( $this->gameField as $v )
        {
            $this->socketpacket->WriteByte($v);
        }
        $this->socketpacket->WriteEnd();
        $packetBuffer = $this->socketpacket->GetPacketBuffer();

        //传递配置参数 __invoke实现
        $this->swooleservice($moneyServerConfig);

        $config = $this->swooleservice->getConfig();
        var_dump($config);

//        //发送数据
//        $sendRs = $this->swooleservice->sendData($packetBuffer);
//        var_dump($sendRs);
//        //接收数据
//        $recvRs = $this->swooleservice->recvData();
//        var_dump($recvRs);
//
//        echo "OK\n";
//        return $recvRs;
    }

    /**
     * 获取money server配置
     * @param $uid
     */
    protected function getMoneyServerConfig( $uid )
    {
        $id = $uid % 10;
        $gameType = Context::get( CommonCode::GAMELANG );
        $ip = ( $gameType != CommonCode::GAMETHA ) ? config('gameinfo.ip') : config('gameinfo.ip_'.$gameType);
        $port = config('gameinfo.moneyport'.$id);
        $config = [
            'ip' => $ip,
            'port' => $port,
            'timeout' => $this->timeout,
        ];
        $this->moneyServerConfig = $config;
        return $config;
    }
}