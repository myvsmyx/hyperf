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

    const LIBERROR = 4; //lid错误

    const LOGINSIGNERROR = 1; //登录签名错误

    const DBINFONOTFOUND = 10; //数据库配置为找到

    const CREATEUSERPARAMERR = 11; //创建用户参数错误

    const CREATEUIDERROR = 12; //创建用户uid失败

    const RECORDUIDPARAMERROR = 13; //根据lid记录uid参数错误

    const UPDATEMONEYMISSPARAM = 14; //更新用户财产缺少参数

    const SWOOLEIPORPORTMISS = 15; //缺少swoole的ip或者端口配置

    const SWOOLECONNECTFAIL = 16; //swoole连接失败

    static $errorMsg = [
        self::LIBERROR => 'lid error',
        self::LOGINSIGNERROR => 'sig error',
        self::DBINFONOTFOUND => 'db_config not found',
        self::CREATEUSERPARAMERR => 'create user param error',
        self::CREATEUIDERROR => 'create uid fail',
        self::RECORDUIDPARAMERROR => 'create uid By lid fail',
        self::UPDATEMONEYMISSPARAM => 'updatemoney missing param',
        self::SWOOLEIPORPORTMISS => 'missing param for swoole',
        self::SWOOLECONNECTFAIL => 'swoole connected fail',
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
