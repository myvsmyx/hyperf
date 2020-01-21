<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("Server Error！")
     */
    const SERVER_ERROR = 500;

    const LOGINSIGNERROR = 1; //登录签名错误

    const LIBERROR = 4; //lid错误

    const ACCOUNTUNAVAILABLE = 8; //账号禁止登陆

    const DBINFONOTFOUND = 10; //数据库配置未找到

    const CREATEUSERPARAMERR = 11; //创建用户参数错误

    const CREATEUIDERROR = 12; //创建用户uid失败

    const RECORDUIDPARAMERROR = 13; //根据lid记录uid参数错误

    const UPDATEMONEYMISSPARAM = 14; //更新用户财产缺少参数

    const SWOOLEIPORPORTMISS = 15; //缺少swoole的ip或者端口配置

    const SWOOLECONNECTFAIL = 16; //swoole连接失败

    const SWOOLESENDFAIL = 17; //swoole发送失败

    const SWOOLERECVFAIL = 18; //swoole接收失败

    const PACKETHEADERLENERR = 19; //包头长度不对

    const PACKETHEADERERR = 20; //包头错误

    const UNPACKETFAIL = 21; //解包失败

    const UPDATEUSERLOGINPARAM = 22; //更新用户登录信息错误

    const LOGLEVELERROR = 23; //log等级错误


    static $errorMsg = [
        self::SERVER_ERROR => 'parameter error',
        self::LIBERROR => 'lid error',
        self::LOGINSIGNERROR => 'sig error',
        self::DBINFONOTFOUND => 'db_config not found',
        self::CREATEUSERPARAMERR => 'create user param error',
        self::CREATEUIDERROR => 'create uid fail',
        self::RECORDUIDPARAMERROR => 'create uid By lid fail',
        self::UPDATEMONEYMISSPARAM => 'updatemoney missing param',
        self::SWOOLEIPORPORTMISS => 'missing param for swoole',
        self::SWOOLECONNECTFAIL => 'swoole connected fail',
        self::SWOOLESENDFAIL => 'swoole send fail',
        self::SWOOLERECVFAIL => 'swoole recv fail',
        self::PACKETHEADERLENERR => 'packet header length fail',
        self::PACKETHEADERERR => 'packet header fail',
        self::UNPACKETFAIL => 'unpacket fail',
        self::ACCOUNTUNAVAILABLE => 'account unavailable',
        self::UPDATEUSERLOGINPARAM => 'update user login params error',
        self::LOGLEVELERROR => 'log level error',
    ];

    /**
     * 返回错误信息
     * @param $code
     */
    public static function getMessage( $code )
    {
        if( empty($code) || !isset( self::$errorMsg[$code] ) )
        {
            return '';
        }
        return self::$errorMsg[$code];
    }
}
