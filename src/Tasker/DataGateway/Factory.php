<?php
namespace Tasker\DataGateway;

use Propel\Runtime\ServiceContainer\StandardServiceContainer;
use Tasker\DataGateway\Db\Tool\Helper;
use Tasker\DataGateway\Exception\ClassNotFound;
use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionManagerSingle;

/**
 * This class is used to represents the factory of the employee data gateway
 *
 * @category DataGateway
 * @package  Tasker\DataGateway
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Factory
{
    /**
     * Make a new instance of the passed gateway name
     *
     * @param string  $gatewayName  This is the gateway name that need to be instantiated (e.g. 'Employee' gateway)
     *
     * @throws \Tasker\DataGateway\Exception\ClassNotFound When the class is not found
     *
     * @return \MultiTierArchitecture\DataGateway\Definition\RepositoryInterface
     */
    public static function make($gatewayName)
    {
        self::setupPropel();
        $namespacesAndClassesArray = explode('|', $gatewayName);
        $namespacesAndClasses = '';
        foreach ($namespacesAndClassesArray as $namespaceOrClass) {
            $namespacesAndClasses .= '\\' . ucfirst($namespaceOrClass);
        }

        $classPath = 'Tasker\\DataGateway' . $namespacesAndClasses;
        if (class_exists($classPath)) {
            return new $classPath();
        }

        throw new ClassNotFound('Could not find class "' . $classPath . '" ', __FILE__, __LINE__);
    }

    /**
     * Set propel to be used later
     *
     * @return void
     */
    public static function setupPropel()
    {
        $serviceContainer = new StandardServiceContainer();
        Propel::setServiceContainer($serviceContainer);
        $config = Helper::getConfig();
        $dbConfig = $config['database'];
        $manager = self::setPropelManager($dbConfig);
        self::setPropelServiceContainer($serviceContainer, $dbConfig, $manager);
    }

    /**
     * Set up the manager of propel by specifying array config
     *
     * @param array  $config  Config that will contain details about data source name (dsn) for the database
     *
     * @return ConnectionManagerSingle
     */
    protected static function setPropelManager($config)
    {
        $manager = new ConnectionManagerSingle();
        $manager->setConfiguration(
            [
                'dsn'      => $config['dsn'],
                'user'     => $config['username'],
                'password' => $config['password'],
            ]
        );
        return $manager;
    }

    /**
     * Set up service container of propel
     *
     * @param \Propel\Runtime\ServiceContainer\ServiceContainerInterface   $serviceContainer  Propel service container
     * @param array                                                        $config            Config array
     * @param \Propel\Runtime\Connection\ConnectionManagerInterface        $manager           Propel manager
     *
     * @return void
     */
    protected static function setPropelServiceContainer($serviceContainer, $config, $manager)
    {
        $serviceContainer->setAdapterClass($config['connectionName'], $config['adapter']);
        $serviceContainer->setConnectionManager($config['connectionName'], $manager);
    }
}
