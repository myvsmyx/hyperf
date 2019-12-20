<?php

declare(strict_types=1);
namespace App\Helper;

use App\Constants\CommonCode;
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
}