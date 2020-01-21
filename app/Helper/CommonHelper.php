<?php

declare(strict_types=1);
namespace App\Helper;

use App\Constants\CommonCode;
use App\Log\Log;
use Hyperf\Utils\Context;

class CommonHelper
{


    /**
     * 根据游戏语言设置不同的时区
     * @param string $str
     */
    public static function localStrToTime( $str = '' )
    {
        $lang = Context::get( CommonCode::GAMELANG, CommonCode::GAMETH );//默认游戏语言
        $timezone = config($lang);
        date_default_timezone_set($timezone);
        $strtotime = strtotime($str);
        date_default_timezone_set(config('cn'));
        return $strtotime;
    }

    /**
     * 获取老用户回归奖励配置
     * @param string $lang
     * @param string $version
     */
    public static function getReturnReward( $version = '' )
    {
        //获取对应的语言
        $lang = Context::get( CommonCode::GAMELANG, CommonCode::GAMETH );
        //获取回归奖励配置
        $config = config('return_reward.'.$lang );
        if( empty($config) )
        {
            Log::debug('can not find return_reward config');
            return false;
        }
        ksort($config);
        foreach ($config as $k => $v)
        {
            if( version_compare($version, $k, '>=') )
            {
                return $v;
            }
        }
        return false;
    }
}