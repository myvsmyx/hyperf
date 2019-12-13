<?php
declare (strict_types=1);

namespace App\Service;

use App\Constants\CommonCode;
use App\Constants\ErrorCode;
use App\Exception\GameException;
use Hyperf\Di\Annotation\Inject;

class ActionUserService implements ActionUserServiceInterface
{

    /**
     * @Inject
     * @var SocketServiceInterface
     */
    private $socketService;

    /**
     * 更新玩家游戏币
     * @param array $param
     */
    public function updateUserMoney( $param = [] )
    {
        $uid = $param['uid'] ?? 0;
        $money = $param['money'] ?? 0;
        $actid = $param['actid'] ?? 0; // 操作类型
        $desc = $param['desc'] ?? ''; // 描述

        if( empty($uid) || empty($money) || empty($actid) )
        {
            throw new GameException( ErrorCode::UPDATEMONEYMISSPARAM );
        }
        //扣除判断是否有足够的游戏币
        if ( $money > CommonCode::DBDEFAULTVAL )
        {
            echo "=======================";
            $gameInfo = $this->getGameInfo( $uid );
            if ($gameInfo['money'] == 0)
            {
                return false;
            }
            if ($gameInfo['money'] < ($money*-1)) {
                return false;
            }
        }

    }

    /**
     * 获取玩家游戏信息
     * @param $uid
     */
    public function getGameInfo( $uid )
    {
        $uid = intval($uid);
        echo $uid."===================\n";
        $this->socketService->getGameInfo( $uid );
    }
}