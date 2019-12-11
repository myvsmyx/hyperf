<?php
/**
 * 游戏配置信息
 */
declare(strict_types=1);

return [
    //server的ip地址
    'ip_tha' => env('SERVERIP_THA', '172.16.157.43'),
    'ip' => env('SERVERIP', '10.25.166.66'),

    //server的port
    'moneyport0' => env('MONEYPORT0', 6000),
    'moneyport1' => env('MONEYPORT1', 6001),
    'moneyport2' => env('MONEYPORT2', 6002),
    'moneyport3' => env('MONEYPORT3', 6003),
    'moneyport4' => env('MONEYPORT4', 6004),
    'moneyport5' => env('MONEYPORT5', 6005),
    'moneyport6' => env('MONEYPORT6', 6006),
    'moneyport7' => env('MONEYPORT7', 6007),
    'moneyport8' => env('MONEYPORT8', 6008),
    'moneyport9' => env('MONEYPORT9', 6009),

    'configport' => env('CONFIGPORT', 10002),
    'broadcastport' => env('BROADCASTPORT', 10001),
];