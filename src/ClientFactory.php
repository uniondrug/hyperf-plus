<?php
namespace Uniondrug\HyperfTools;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hyperf\Guzzle\CoroutineHandler;
use Hyperf\Utils\Coroutine;
use Psr\Container\ContainerInterface;

class ClientFactory extends \Hyperf\Guzzle\ClientFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create(array $options = []): GuzzleClient
    {
        $stack = null;
        if (Coroutine::inCoroutine()) {
            $stack = HandlerStack::create(new CoroutineHandler());
        }
        $config = array_replace(['handler' => $stack], $options);
        if (method_exists($this->container, 'make')) {
            return $this->container->make(GuzzleClient::class, ['config' => $config]);
        }
        return new GuzzleClient($config);
    }
    
}