<?php
declare (strict_types=1);

namespace App\Service;

use Hyperf\DbConnection\Db;

class TuserService implements TuserServiceInterface
{
    /**
     * 获取用户信息
     * @param int $uid
     * @return mixed|void
     */
    public function getUserBaseInfo( $uid = 0 )
    {
        $table = 'info'.$uid%100;
        return DB::connection('user')->table($table)->where('uid', '=', $uid)->first();
    }
}
