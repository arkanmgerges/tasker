<?php
namespace Tasker\UseCase;

use Tasker\UseCase\Exception\UseCaseNotFound;

/**
 * This class is used to represent the factory of the use case
 *
 * @category UseCase
 * @package  Tasker\UseCase
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Factory
{
    /**
     * Make a new instance of the use case
     *
     * @param string  $useCaseName          Use case name to be created
     *
     * @throws Exception\UseCaseNotFound
     *
     * @return \MultiTierArchitecture\UseCase\Definition\UseCaseInterface  Created use case
     */
    public static function make($useCaseName)
    {
        $namespacesAndClassesArray = explode('|', $useCaseName);
        $namespacesAndClasses = '';
        foreach ($namespacesAndClassesArray as $namespaceOrClass) {
            $namespacesAndClasses .= '\\' . ucfirst($namespaceOrClass);
        }

        $classPath = 'Tasker\\UseCase' . $namespacesAndClasses;
        if (class_exists($classPath)) {
            return new $classPath();
        }

        throw new UseCaseNotFound('Could not find use case class "' . $classPath . '" ', __FILE__, __LINE__);
    }
}
