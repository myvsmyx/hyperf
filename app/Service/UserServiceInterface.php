<?php
declare (strict_types=1);

namespace App\Service;

interface UserServiceInterface
{
    /**
     * 创建新用户
     * @param array $param
     * @return mixed
     */
    public function createUser( array $param );

    /**
     * 获取玩家的基本信息和游戏信息
     * @param int $uid
     * @return mixed
     */
    public function getUserBaseGameInfo ( int $uid );

    /**
     * 获取用户基本信息
     * @param int $uid
     * @return mixed
     */
    public function getUserBaseInfo( int $uid );

    /**
     * 更新登录玩家信息
     * @param array $param
     * @return mixed
     */
    public function updateUserLoginInfoEvent( array $param );

    /**
     * 用户回归奖励
     * @param int $uid
     * @param array $param
     * @return mixed
     */
    public function userReturnReward( int $uid, string $version );
}