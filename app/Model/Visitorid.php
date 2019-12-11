<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * 游客相关
 */
class Visitorid extends Model
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

    //时间戳
    public $timestamps = false;

    //数据库连接
    protected $connection = 'default';

    /**
     * 设置游客suid和uid关联表
     * @param string $suid
     * @return Model|void
     */
    public function setTable( $suid = '' ): string
    {
        $str = substr($suid, 0, 2);
        $this->table = 'visitorid_'.$str;
        return $this->table;
    }


}