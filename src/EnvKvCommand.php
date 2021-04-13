<?php
declare(strict_types=1);
namespace Uniondrug\HyperfTools;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @Command
 */
class EnvKvCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var ConfigInterface
     */
    protected $config;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('kv:init');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('从不同环境的consul合并配置到env');
    }

    protected function getArguments()
    {
        return [
            ['env', InputArgument::OPTIONAL, '环境变量']
        ];
    }

    public function handle()
    {
        //当前环境
        $env = $this->input->getArgument('env') ?? 'testing';
        $appName = $this->config->get('app_name');
        switch ($env){
            case 'release':
                $host = 'uniondrug.net';
                break;
            case 'production':
                $host = 'uniondrug.cn';
                break;
            case 'testing':
            default:
                $host = 'turboradio.cn';
                break;
        }
        $baseUrl = 'http://udsdk.'.$host.'/v1/kv/';
        $url  = $baseUrl.'apps/'.$appName.'/config';
        $json = $this->getConfig($url);

        $envLocal = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $envData = [];
        foreach ($envLocal as $local){
            $temp = explode('=', $local);
            $envData[$temp[0]] = $temp[1];
        }
        //数据库配置
        if (isset($json['database']['value']) && strstr($json['database']['value'], 'kv://')){
            $url = $baseUrl.str_replace('kv://', '', $json['database']['value']);
            $dbJson = $this->getConfig($url);
            if (isset($dbJson['connection']) && strstr($dbJson['connection'], 'kv://')){
                $url = $baseUrl.str_replace('kv://', '', $dbJson['connection']);
                $connectionArr = $this->getConfig($url);
                foreach ($connectionArr as $key=>$value){
                    $url  = $baseUrl.str_replace('kv://', '', $value);
                    $text = file_get_contents($url);
                    $data = json_decode($text, true);
                    $this->formatDbData($key, trim(base64_decode($data[0]['Value'])), $envData);
                }
            }
        }
        //redis配置
        if (isset($json['redis']['value']) && strstr($json['redis']['value'], 'kv://')){
            $url = $baseUrl.str_replace('kv://', '', $json['redis']['value']);
            $redisArr = $this->getConfig($url);
            foreach ($redisArr as $key=>$value){
                if (in_array($key, ['host', 'port', 'auth'])){
                    $url  = $baseUrl.str_replace('kv://', '', $value);
                    $text = file_get_contents($url);
                    $data = json_decode($text, true);
                    $envData['REDIS_'.strtoupper($key)] = trim(base64_decode($data[0]['Value']));
                }
            }
        }
        file_put_contents('.env','');
        foreach ($envData as $k=>$v){
            file_put_contents('.env', $k.'='.$v.PHP_EOL,FILE_APPEND);
        }
    }

    private function getConfig($url)
    {
        $text = file_get_contents($url);
        if (!$text){
            $this->line('not find config from '.$url, 'info');
            return;
        }
        $data = json_decode($text, true);
        if (!isset($data[0]['Value'])){
            return;
        }
        return json_decode(trim(base64_decode($data[0]['Value'])), true);
    }

    private function formatDbData($key, $val, &$envData)
    {
        switch ($key){
            case 'host':
                $envData['DB_HOST'] = $val;break;
            case 'port':
                $envData['DB_PORT'] = $val;break;
            case 'username':
                $envData['DB_USERNAME'] = $val;break;
            case 'password':
                $envData['DB_PASSWORD'] = $val;break;
            case 'charset':
                $envData['DB_CHARSET'] = $val;
                $envData['DB_COLLATION'] = $val.'_unicode_ci';break;
        }
    }

    private function formatRedisData($key, $val, &$envData)
    {
        switch ($key){
            case 'host':
                $envData['REDIS_HOST'] = $val;break;
            case 'port':
                $envData['REDIS_PORT'] = $val;break;
            case 'auth':
                $envData['REDIS_AUTH'] = $val;break;
        }
    }
}