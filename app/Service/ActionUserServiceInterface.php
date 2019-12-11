<?php
declare (strict_types=1);

namespace App\Service;

interface ActionUserServiceInterface
{

    /**
     * 更新用户游戏币等财产信息
     * @param array $param
     * @return mixed
     */
    public function updateUserMoney( array $param );

    /**
     * 获取玩家游戏信息
     * @param int $uid
     * @return mixed
     */
    public function getGameInfo( int $uid );
}