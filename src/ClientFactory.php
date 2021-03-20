<?php


namespace Uniondrug\HyperfTools;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hyperf\Guzzle\CoroutineHandler;
use Hyperf\Utils\Coroutine;
use Psr\Container\ContainerInterface;

class ClientFactory
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
            // Create by DI for AOP.
            echo '1111111'.PHP_EOL;
            return $this->container->make(GuzzleClient::class, ['config' => $config]);
        }
        echo 'aaaaa'.PHP_EOL;
//        return new Client($config);
        return new GuzzleClient($config);
    }
    
    public function aa()
    {
        return 'cccccc';
    }
}