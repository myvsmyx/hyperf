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

return [
    \App\Service\VisitorServiceInterface::class => \App\Service\VisitorService::class,
    \App\Service\UserServiceInterface::class => \App\Service\UserService::class,
    \App\Service\ActionUserServiceInterface::class => \App\Service\ActionUserService::class,
    \App\Service\SocketServiceInterface::class => \App\Service\SocketService::class,
    \App\Service\SwooleServiceAbstract::class => \App\Service\SwooleService::class,
    \App\Service\TuserServiceInterface::class =>  \App\Service\TuserService::class,
];
