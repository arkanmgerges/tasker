<?php
namespace Tasker\DataGateway\Db\Mapper;

use Tasker\DataGateway\Db\Exception\DbMapperNotFound;

/**
 * Factory class used to create mapper instances of the mapper classes
 *
 * @category Mapper
 * @package  Tasker\Db\Mapper
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Factory
{
    /**
     * Create new instances of mappers
     *
     * @param string  $mapperClassName  Mapper name
     *
     * @throws DbMapperNotFound If db class can not be found
     *
     * @return \Tasker\DataGateway\Db\Mapper\Definition\CommonEntityMapperAbstract Created mapper
     */
    public static function make($mapperClassName)
    {
        $namespacesAndClassesArray = explode('|', $mapperClassName);
        $namespacesAndClasses = '';
        foreach ($namespacesAndClassesArray as $namespaceOrClass) {
            $namespacesAndClasses .= '\\' . ucfirst($namespaceOrClass);
        }

        $classPath = 'Tasker\\DataGateway\\Db\\Mapper' . $namespacesAndClasses;
        if (class_exists($classPath)) {
            return new $classPath();
        }

        throw new DbMapperNotFound('Could not find database mapper "' . $classPath . '" ', __FILE__, __LINE__);
    }
}
