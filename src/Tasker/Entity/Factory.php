<?php
namespace Tasker\Entity;

use MultiTierArchitecture\Entity\Exception\EntityNotFound;
use MultiTierArchitecture\Entity\Definition\EntityInterface;

/**
 * This class is used to represent the factory of the entity
 *
 * @category Entity
 * @package  Tasker\Entity
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Factory
{
    /**
     * Make a new instance of the passed gateway name
     *
     * @param string  $entityName  This is the entity name that need to be instantiated (e.g. 'Employee' entity)
     *
     * @throws EntityNotFound When the entity class is not found
     *
     * @return EntityInterface
     */
    public static function make($entityName)
    {
        $namespacesAndClassesArray = explode('|', $entityName);
        $namespacesAndClasses = '';
        foreach ($namespacesAndClassesArray as $namespaceOrClass) {
            $namespacesAndClasses .= '\\' . ucfirst($namespaceOrClass);
        }

        $classPathAndName = 'Tasker\\Entity' . $namespacesAndClasses;
        if (!class_exists($classPathAndName)) {
            throw new EntityNotFound('Could not find entity class "' . $classPathAndName . '" ', __FILE__, __LINE__);
        }

        return new $classPathAndName();
    }
}
