<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\CommonHelper;
use App\Log\Log;
use Hyperf\Di\Annotation\Inject;

use Psr\Container\ContainerInterface;
use Hyperf\Logger\LoggerFactory;

class RabbitmqController extends AbstractController
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @Inject
     * @var Log
     */
    private $log;

    /**
     * @Inject
     * @var LoggerFactory
     */
    protected $loggerfactory;


    public function test()
    {
        $this->logger = $this->loggerfactory->get('log', 'default');
        $this->logger->error("Your log error.", ['name' => 'mike']);
        $this->logger->info("Your log message.");
        $this->logger->warning("Your log warning.");
    }

    public function debuglog()
    {
        $config = CommonHelper::getReturnReward('2.0.3');
        var_dump($config);
        return $config;

//        $this->log->debug('我的测试debug', 'debug2_abc');
//        $this->log->info('我的测试info', 'info2_abc');
//        $this->log->error('我的测试error', 'error2_abc');

//        Log::debug('hahhahh');
//        Log::info(['name' => 'mike', 'age' => 18]);
//        Log::error(['name' => 'mike', 'age' => 18]);
    }
}
