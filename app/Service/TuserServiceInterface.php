<?php
declare (strict_types=1);

namespace App\Service;

interface TuserServiceInterface
{
    /**
     * 获取用户基本信息
     * @param int $uid
     * @return mixed
     */
    public function getUserBaseInfo( int $uid );
}