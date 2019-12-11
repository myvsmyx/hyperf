<?php
declare (strict_types=1);

namespace App\Service;

Interface SocketServiceInterface
{
    /**
     * 更新用户游戏币
     * @param int $uid
     * @param int $money
     * @param int $actid
     * @param int $gid
     * @param string $desc
     * @return mixed
     */
    public function updateMoney( int $uid, int $money, int $actid, int $gid, string $desc);

    /**
     * 获取用户游戏信息
     * @param int $uid
     * @return mixed
     */
    public function getGameInfo( int $uid );
}
