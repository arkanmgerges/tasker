<?php
namespace Tasker\Mapper\Definition;

use MultiTierArchitecture\Mapper\Definition\EntityMapperAbstract;
use Tasker\Entity\Factory;

/**
 * Mapper abstract class used to set arrays, array object of data that later need to be mapped to entities
 *
 * @category Mapper
 * @package  Tasker\DataGateway\Db\Mapper\Definition
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
abstract class CommonEntityMapperAbstract extends EntityMapperAbstract
{
    /**
     * Create other entity by name
     *
     * @param string $otherEntityName Entity name that need to be created
     *
     * @return mixed New entity
     */
    public function getSecondEntityByName($otherEntityName)
    {
        $namespacesAndClassesArray = explode('|', $otherEntityName);
        $namespacesAndClasses = '';
        foreach ($namespacesAndClassesArray as $namespaceOrClass) {
            $namespacesAndClasses .= '\\' . ucfirst($namespaceOrClass);
        }

        $dbClassPathAndName = 'Tasker\\DataGateway\\Db\\Entity' . $namespacesAndClasses;
        if (!class_exists($dbClassPathAndName)) {
            return null;
        }

        return new $dbClassPathAndName();
    }

    /**
     * Create entity by name
     *
     * @param string $entityName Entity name that need to be created
     *
     * @return mixed New entity
     */
    public function getFirstEntityByName($entityName)
    {
        return Factory::make($entityName);
    }
}
