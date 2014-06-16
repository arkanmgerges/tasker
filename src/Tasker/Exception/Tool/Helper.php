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
    protected static $config = null;

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
     * Get a general config to be parsed
     *
     * @return array
     */
    public static function getConfig()
    {
        if (self::$config !== null)
            return self::$config;

        $allConfigEnvs = include __DIR__ . '/../Config/config.php';
        $env = getenv('APP_ENV') ?
                   getenv('APP_ENV') :
                   'production'; // Application environment

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
