<?php
namespace Uniondrug\HyperfTools;

use Hyperf\Config\Annotation\Value;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Request;
use Hyperf\Redis\Redis;
use Hyperf\Utils\Context;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\Coroutine;
use Psr\Log\LoggerInterface;

class Logger
{
    /**
     * @Inject()
     * @var Redis
     */
    private  $redis;

    /**
     * @Inject()
     * @var Request
     */
    private $request;

    public $traceId;

    /**
     * @Value("config.app_name")
     */
    private $appName;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        // 第一个参数对应日志的 name, 第二个参数对应 config/autoload/logger.php 内的 key
        $this->logger = $loggerFactory->get('sys');
    }

    public function info($data, $action, $method = 'GET')
    {
        $keyRandom = 't'.date('dHis').mt_rand(1001, 9999).mt_rand(1001, 9999);
        // 2. parser logger
        $list = [];
        $list[$keyRandom.sprintf("%04d", 0)] = [
            'time'          => date('Y-m-t H:i:s.sss',time()),
            'level'         => 'INFO',
            'action'        => $action,
            'module'        => $this->appName,
            'duration'      => 0,
            'pid'           => Coroutine::parentId(),
            'requestId'     => Context::get('traceId'),
            'requestMethod' => $method,
            'requestUrl'    => $this->request->url(),
            'traceId'       => Context::get('traceId'),
            'spanId'        => Context::get('spanId'),
            'parentSpandId' => '',
            'serverAddr'    => '',
            'taskId'        => Coroutine::id(),
            'taskName'      => '',
            'version'       => '',
            'content'       => $data
        ];
        $keys = [];
        foreach ($list as $key=>$val){
            $this->redis->set('logger:'.$key, json_encode($val, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 86400);
            $keys[] = 'logger:'.$key;
        }
        $this->redis->rPush('logger:list', ... $keys);
        $res = is_array($data) ? json_encode($data, true) : $data;
        $this->logger->info(trim($res));
    }


}