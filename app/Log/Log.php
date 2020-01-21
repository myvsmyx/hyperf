<?php

declare(strict_types=1);

namespace App\Log;

use App\Constants\CommonCode;
use App\Constants\ErrorCode;
use App\Exception\GameException;
use Hyperf\Config\Annotation\Value;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;

class Log
{

    /**
     * 获取monolog的channel
     * @param string $name
     * @return \Psr\Log\LoggerInterface
     */
    public static function get( $name = 'app', $group = 'default')
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name, $group);
    }

    /**
     * 默认静态调用方法
     * @param $name
     * @param $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        if( !in_array($name, CommonCode::LOGLEVEL) )
        {
            throw new GameException( ErrorCode::LOGLEVELERROR );
        }
        $message = json_encode($arguments);
        $group = 'level-'.$name;
        $logger = self::get($group, $group);
        $logger->$name($message);
    }

}