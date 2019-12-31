<?php

declare(strict_types=1);

namespace App\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

/**
 * @Producer(exchange="taskex", routingKey="taskqueue")
 */
class TaskProducer extends ProducerMessage
{
    public function __construct()
    {

    }

    public function setTask($name = '')
    {
        $this->payload = $name;
        echo 'TaskName:'.$name."\n";
    }
}
