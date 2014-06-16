<?php
namespace Tasker\DataGateway\Db\Tool;

/**
 * HelperTrait class for the data gateway
 *
 * @category DataGateway
 * @package  Tasker\DataGateway\Db\Tool
 * @author   Arkan M. Gerges <aa@nextdating.com>
 * @version  GIT: $Id:$
 */
class Helper
{
    private static $config = null;
    private static $configPath = null;
    private static $environmentVariable = null;

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
     * Get a general config to be parsed
     *
     * @return array
     */
    public static function getConfig()
    {
        if (self::$config !== null) {
            return self::$config;
        }

        $allConfigEnvs = !empty(self::$configPath) ? include self::$configPath : include __DIR__ . '/../Config/config.php';
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
