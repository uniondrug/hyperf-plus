<?php
namespace Uniondrug\HyperfTools;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Container;
use Psr\Http\Message\ResponseInterface;

class GuzzleClient extends \GuzzleHttp\Client
{
    const VERSION = 'V1.0.0';
    
    /**
     * @Inject()
     * @var ConfigInterface
     */
    private $config;
    
    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        $options['headers']['X-B3-Traceid']      = \Hyperf\Utils\Context::get('traceId');
        $options['headers']['X-B3-Parentspanid'] = \Hyperf\Utils\Context::get('spandId');
        $options['headers']['X-B3-Spanid']       = md5(uniqid());
        $options['headers']['X-B3-Sampled']      = '';
        $options['headers']['X-B3-Version']      = '';
        $appName = $this->config->get('app_name');
//        $appName = $this->config->get('app_name', '');
        echo '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>'.$appName.PHP_EOL;
//        $appVersion = $this->config->get('app_version', '');
//        $userAgent = "HyperfHttpClient/".self::VERSION." GuzzleHttp/".parent::MAJOR_VERSION;
//        if ($appName !== '' && $appVersion !== '') {
//            $userAgent .= " ".$appName."/".$appVersion;
//        }
//        $options['headers']['XUser-Agent']       = $userAgent;
        echo '>>>>>>>>>>>>>'.\Hyperf\Utils\Context::get('traceId').PHP_EOL;
        return parent::request($method, $uri, $options); // TODO: Change the autogenerated stub
    }
    
    public function get($uri, $option=[]): ResponseInterface
    {
        $options['headers']['X-B3-Traceid']      = \Hyperf\Utils\Context::get('traceId');
        $options['headers']['X-B3-Parentspanid'] = \Hyperf\Utils\Context::get('spandId');
        $options['headers']['X-B3-Spanid']       = md5(uniqid());
        $options['headers']['X-B3-Sampled']      = '';
        $options['headers']['X-B3-Version']      = '';
//        $appName = $this->config->get('app_name', '');

//        $appVersion = $this->config->get('app_version', '');
        echo 'GET===============>222'.PHP_EOL;

//        $userAgent = "HyperfHttpClient/".self::VERSION." GuzzleHttp/".parent::MAJOR_VERSION;
//        if ($appName !== '' && $appVersion !== '') {
//            $userAgent .= " ".$appName."/".$appVersion;
//        }
//        $options['headers']['XUser-Agent']       = $userAgent;
//        echo 'get'.PHP_EOL;
        echo 'GET===============>'.PHP_EOL;

        return $this->request('GET', $uri, $options);
    }
}