<?php
namespace Tasker\Exception\Tool;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

/**
 * HelperTrait class for exceptions' logging
 *
 * @category Exception
 * @package  Tasker\Exception\Tool
 * @author   Arkan M. Gerges <aa@nextdating.com>
 * @version  GIT: $Id:$
 */
class Helper
{
    private static $config              = null;
    private static $configPath          = null;
    private static $environmentVariable = null;

    /**
     * Here it's using Monolog for writing logs
     *
     * @param int  $level  Log level
     *
     * @return \Monolog\Logger
     */
    public static function getLogger($level = Logger::DEBUG)
    {
        $config = self::getConfig();
        $logger = new Logger($config['log']['channelName']);

        if (!is_dir($config['log']['baseDirectory'])) {
            mkdir($config['log']['baseDirectory'], 0777);
        }
        $handler = new RotatingFileHandler(
            $config['log']['baseDirectory'] . 'messages.log',
            $config['log']['maxFilesRotation'],
            $level
        );
        $handler->setFormatter(
            new LineFormatter($config['log']['format'])
        );
        $logger->pushHandler($handler);
        return $logger;
    }

    /**
     * Set the environment variable that will be use to choose the configuration key
     *
     * @param string  $environmentVariable  Configuration key
     *
     * @return void
     */
    public static function setEnvironmentVariable($environmentVariable)
    {
        self::$environmentVariable = $environmentVariable;
    }

    /**
     * Set the path to the configuration file
     *
     * @param string  $configPath  This is the configuration path to the file that contains application configuration
     *
     * @return void
     */
    public static function setConfigPath($configPath)
    {
        self::$configPath = $configPath;
    }

    /**
     * Get a general config to be parsed
     *
     * @return array
     */
    public static function getConfig()
    {
        if (self::$config !== null) {
            return self::$config;
        }

        $allConfigEnvs = !empty(self::$configPath) ?
            include self::$configPath : include __DIR__ . '/../Config/config.php';
        $env = !empty(self::$environmentVariable)  ? getenv(self::$environmentVariable) : 'base';
        $env = !empty($env) ? $env : 'base';

        self::$config = self::getConfigByEnv($env, $allConfigEnvs);

        return self::$config;
    }

    /**
     * Get config array for the specified environment, this include also inheritance like dev:prod config keys
     *
     * @param string  $env     Environment key in the config array
     * @param array   $config  Config array for all the environments
     *
     * @return array Merged array for the required environment, or an empty array
     */
    protected static function getConfigByEnv($env = '', $config = [])
    {
        return isset($config[$env]) ?
                   $config[$env] :
                   self::getMergedArrayForConfigEnvironment($env, $config);
    }

    /**
     * Get configuration merged array in case of the inheritance (e.g. dev:prod), or return an empty array
     *
     * @param string  $env     Environment the need its array to be fetched
     * @param array   $config  Config array for all environments
     *
     * @return array Merged array of the config env (in case of inheritance) or simply return empty array
     */
    protected static function getMergedArrayForConfigEnvironment($env, $config)
    {
        $mergedConfigArray = [];
        foreach ($config as $configKey => $envConfigArray) {
            $isMatchFound = stripos($configKey, $env . ':') !== false;
            if ($isMatchFound) {
                $separatedKeys = explode(':', $configKey);
                if (isset($separatedKeys[1])) {
                    $mergedConfigArray = array_replace_recursive(
                        (array)self::getConfigByEnv($separatedKeys[1], $config),
                        (array)$envConfigArray
                    );
                }
                break;
            }
        }
        return $mergedConfigArray;
    }
}
