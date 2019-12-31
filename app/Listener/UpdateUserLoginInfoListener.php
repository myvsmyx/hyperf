<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\UpdateUserLoginInfo;
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
        echo "事件监听器返回的结果: \n";
        var_dump($updateInfo);
    }
}
