<?php

declare(strict_types=1);

namespace App\Controller;

use App\Amqp\Consumer\TaskConsumer;
use App\Amqp\Producer\TaskProducer;
use Hyperf\Amqp\Consumer;
use Hyperf\Amqp\Producer;
use Hyperf\Di\Annotation\Inject;

class RabbitmqController extends AbstractController
{

    /**
     * @Inject
     * @var TaskProducer
     */
    private $taskProducer;


    /**
     * @Inject
     * @var Producer
     */
    private $producer;


    /**
     * @Inject
     * @var Consumer;
     */
    private $consumer;


    /**
     * @Inject
     * @var TaskConsumer
     */
    private $taskConsumer;


    public function index()
    {
        $this->taskProducer->setTask('First');
        $result = $this->producer->produce( $this->taskProducer );
        echo "Producer Set Task: \n";
        var_dump($result);
    }


    /**
     * 消费消息
     */
    public function consumerTask()
    {
        echo "Consumer task: \n";
        $this->taskConsumer->consume( $this->consumer );
    }
}
