<?php
declare (strict_types=1);

namespace App\Service;

use App\Constants\CommonCode;
use App\Constants\ErrorCode;
use App\Exception\GameException;
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

    //server返回的字段
    private $field = array(
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
        $uid = intval($uid);

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

        //获取配置
        $moneyServerConfig = $this->getMoneyServerConfig( $uid );

        //设置配置
        $this->swooleservice->setConfig( $moneyServerConfig );

        //发送数据
        $this->swooleservice->sendData($packetBuffer);

        //接收数据
        $recvRs = $this->swooleservice->recvData();

        //解包
        $rs = $this->parsePacket( $recvRs );
        if( !$rs )
        {
            throw new GameException( ErrorCode::UNPACKETFAIL );
        }

        $this->socketpacket->ReadInt();
        $ret = $this->socketpacket->ReadByte();
        if ( $ret != CommonCode::DBDEFAULTVAL )
        {
            return CommonCode::DBDEFAULTVAL;
        }
        return $this->socketpacket->ReadInt64();
    }

    /**
     * 获取玩家游戏信息
     * @param $uid
     */
    public function getGameInfo( $uid )
    {
        $uid = intval($uid);

        //打包
        $size = count( $this->gameField );
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

        //获取配置
        $moneyServerConfig = $this->getMoneyServerConfig( $uid );

        //设置配置
        $this->swooleservice->setConfig( $moneyServerConfig );

        //发送数据
        $this->swooleservice->sendData($packetBuffer);

        //接收数据
        $recvRs = $this->swooleservice->recvData();

        //解包
        $rs = $this->parsePacket( $recvRs );
        if( !$rs )
        {
            throw new GameException( ErrorCode::UNPACKETFAIL );
        }

        $this->socketpacket->ReadInt();
        $ret = $this->socketpacket->ReadByte();
        if ( $ret != CommonCode::DBDEFAULTVAL )
        {
            return [];
        }

        $size = $this->socketpacket->ReadByte();
        $returnData = [];
        $server_value = array_flip( $this->field );
        for ($i=0; $i <$size ; $i++) {
            $type = $this->socketpacket->ReadByte();
            if( !isset( $server_value[$type] ) )
            {
                continue;
            }
            $fieldName = strtolower( $server_value[$type] );
            switch ( $fieldName ) {
                case 'game':
                    $buff = $this->socketpacket->ReadString();
                    $returnData[ $fieldName ] = empty($buff) ? array() : json_decode($buff, true);
                    break;
                case 'money':
                case 'safebox':
                case 'gold':
                case 'point':
                    $returnData[ $fieldName ] = $this->socketpacket->ReadInt64();
                    break;
                case 'exp':
                    $returnData[ $fieldName ] = $this->socketpacket->ReadInt();
                    break;
            }
        }
        return $returnData;
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
            'port' => intval( $port ),
            'timeout' => $this->timeout,
        ];
        $this->moneyServerConfig = $config;
        return $config;
    }

    /**
     * 解包数据
     * @param string $packetBuffer
     */
    protected function parsePacket( $packetBuffer = '' )
    {
        $rs = $this->socketpacket->parsePacket( $packetBuffer );
        return $rs === CommonCode::DBDEFAULTVAL ? true : false;
    }
}