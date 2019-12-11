<?php

declare (strict_types=1);
namespace App\Model;

use App\Constants\CommonCode;
use Hyperf\DbConnection\Model\Model;
/**
 */
class InfoBese extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = '';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    protected $connection = 'user';

    public $timestamps = false;

    /**
     * 设置表
     * @param int $uid
     * @return Model|void
     */
    public function setTable( $uid = 0 )
    {
        if ($uid > CommonCode::INFOBESTUID) {
            $this->table = 'infobest' . ($uid % 100);
            $this->connection = 'best';
        } else {
            $this->connection = 'user';
            $this->table = 'infobest' . ($uid % 10);
        }
    }

    /**
     * 创建用户
     * @param int $timezone
     */
    public function createUser( $uid = 0, $timezone = 0 )
    {
        $this->uid = $uid;
        $this->maxmoney = CommonCode::DBDEFAULTVAL;
        $this->invitemoney = CommonCode::DBDEFAULTVAL;
        $this->invitenum = CommonCode::DBDEFAULTVAL;
        $this->timezone = intval( $timezone );
        $this->buygold = CommonCode::DBDEFAULTVAL;
        $this->vip = CommonCode::DBDEFAULTVAL;
        $this->maxvip = CommonCode::DBDEFAULTVAL;
        $this->paytotal = CommonCode::DBDEFAULTVAL;
        $this->paytime = CommonCode::DBDEFAULTVAL;

        $this->setTable($uid);
        $this->save();
    }
}