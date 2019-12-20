<?php

declare(strict_types=1);

/**
 * 项目需要使用的常量
 */

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

class CommonCode extends AbstractConstants
{

    /**
     * @Inject
     * @var RequestInterface
     */
    public static $request;

    /********lid(登录类型)参数定义***********/
    const LIDPHONE = 3; //手机登录
    const LIDGUEST = 2; //游客登录
    const ALLOWLOGINLIDARR = [ self::LIDGUEST, self::LIDPHONE];

    /**********pid(登录类型)参数定义*********/
    const PIDANDROID = 1; // 安卓
    const PIDIOS = 2; // IOS

    /**************目前游戏的语言版本***************/
    const GAMELANG = 'GAMELANG';
    const GAMETH = 'th';
    const GAMEID = 'id';
    const GAMEEN = 'en';
    const GAMETHA = 'tha';

    /*****************账号状态***********************/
    const ACCOUNTAVAILABLE = 0; //账号可用状态
    const ACCOUNTUNAVAILABLE = 1; //账号不可用状态


    /************其他****************/
    const LOGINPRIKEY = '&!@#$iop'; //登录密钥
    const ENCRYPTIONIMEI = '!@#$iop'; // 加密imei
    const FEMALE = 0; // 性别:女
    const MALE = 1; // 性别:男
    const DBDEFAULTVAL = 0; //入库默认值0
    const TRUEVALUE = 1; //条件为真值
    const INFOBESTUID = 5000000; //uid超过此值的用户往th_best.infobest里面查询

    /**
     * 判断是否是允许登录的lid类型
     * @param int $lid
     * @return bool
     */
    public static function allowLoginLid( $lid = 0 )
    {
        return in_array($lid, self::ALLOWLOGINLIDARR);
    }

    /**
     * 获取游戏类型
     */
    public static function getGameType()
    {
        return self::$request->input('gamelang', config('gametype') );//默认游戏语言
    }
}