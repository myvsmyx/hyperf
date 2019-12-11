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

use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LogLevel;

return [
    'app_name' => env('APP_NAME', 'skeleton'),
    StdoutLoggerInterface::class => [
        'log_level' => [
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::DEBUG,
            LogLevel::EMERGENCY,
            LogLevel::ERROR,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
        ],
    ],
    'gametype' => env('GAMETYPE', 'th'),//默认游戏类型
    'initMoney' => [//初始用户的游戏包, 语言 => [ lid => xxx ]
        'th' => [ 1 => 10000, 2 => 8000 ],
        'id' => [ 1 => 10000, 2 => 8000 ],
        'en' => [ 1 => 10000, 2 => 8000 ],
        'tha' => [ 1 => 120000, 2 => 80000 ],
    ],
    'defaultMoney' => 10000,//出现无法找到initMoney对应的数据时候，使用
];
