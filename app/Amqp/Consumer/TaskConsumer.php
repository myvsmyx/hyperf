<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;

/**
 * @Consumer(exchange="taskex", routingKey="taskqueue", queue="hyperf", name ="TaskConsumer", nums=1)
 */
class TaskConsumer extends ConsumerMessage
{
    public function consume($data): string
    {
        var_dump($data);
        return Result::ACK;
    }
}
