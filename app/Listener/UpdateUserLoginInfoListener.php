<?php

declare(strict_types=1);

namespace App\Listener;

use App\Constants\ErrorCode;
use App\Event\UpdateUserLoginInfo;
use App\Exception\LoginException;
use Hyperf\DbConnection\Db;
use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @Listener
 */
class UpdateUserLoginInfoListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            UpdateUserLoginInfo::class,
        ];
    }

    public function process(object $event)
    {
        $updateInfo = $event->updateInfo;
        $uid = $updateInfo['uid'] ?? null;
        if ( is_null($uid) || !is_array($updateInfo) )
        {
            throw new LoginException( ErrorCode::UPDATEUSERLOGINPARAM );
        }
        unset($updateInfo['uid']);
        $table = 'info'.($uid % 100);
        DB::connection('user')->table($table)->where('uid', $uid)->update($updateInfo);
    }
}
