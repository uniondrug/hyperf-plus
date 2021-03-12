<?php
/**
 * @author wsfuyibing <websearch@163.com>
 * @date   2019-10-28
 */
namespace Uniondrug\HttpClient;

use Phalcon\Di;
use Phalcon\Logger\Adapter;
use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Server;
use Uniondrug\Framework\Container;
use Uniondrug\Phar\Server\XHttp;

/**
 * Http请求包装
 * @package Uniondrug\HttpClient
 */
class Client
{
    const VERSION = '1.0.0';
    const SLOW_SECONDS = 0.5;

    public function request()
    {
        return 'aa';
    }
}
