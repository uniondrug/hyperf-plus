<?php
/**
 * @author wsfuyibing <websearch@163.com>
 * @date   2019-10-28
 */
namespace Uniondrug\HyperfTools;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;


/**
 * Http请求包装
 * @package Uniondrug\HttpClient
 */
class HttpClient extends ClientFactory
{
    const VERSION = '1.0.0';
    const SLOW_SECONDS = 0.5;

    /**
     * @Inject()
     * @var ClientFactory
     */
    private  $httpClient;

    public function request()
    {
//        echo 'djfafjdaskfasj';
        return 'aa';
    }
}
