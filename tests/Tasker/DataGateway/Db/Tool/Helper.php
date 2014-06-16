<?php
namespace Test\Tasker\DataGateway\Db\Tool;

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionManagerSingle;

class Helper
{
    protected static $isPropelSetUp = false;

    public static function getConfig()
    {
        $allConfigEnvs = include __DIR__ .
            '/../Config/config.php';
        $env = getenv('APP_ENV') ? getenv('APP_ENV') : 'production'; // Application environment
        return self::getConfigByEnv($env, $allConfigEnvs);
    }

    public static function setupPropel()
    {
        if (!self::$isPropelSetUp) {
            $dbConfig = self::getDbConfig();
            $manager = self::setPropelManager($dbConfig);
            $serviceContainer = Propel::getServiceContainer();
            self::setPropelServiceContainer($serviceContainer, $dbConfig, $manager);
            self::$isPropelSetUp = true;
        }
    }

    protected static function setPropelManager($config)
    {
        $manager = new ConnectionManagerSingle();
        $configuration =             [
            'dsn'      => $config['dsn'],
            'user'     => $config['username'],
            'password' => $config['password'],
        ];

        $manager->setConfiguration($configuration);
        return $manager;
    }

    protected static function setPropelServiceContainer($serviceContainer, $config, $manager)
    {
        $serviceContainer->setAdapterClass($config['connectionName'], $config['adapter']);
        $serviceContainer->setConnectionManager($config['connectionName'], $manager);
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
        if (isset($config[$env]))
            return $config[$env];

        return self::getMergedArrayForConfigEnvironment($env, $config);
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

    protected static function getDbConfig()
    {
        $config = self::getConfig();
        $dbConfig = $config['database'];
        return $dbConfig;
    }
}
