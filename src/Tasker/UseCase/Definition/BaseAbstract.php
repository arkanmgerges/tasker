<?php
namespace Tasker\UseCase\Definition;

use Tasker\Boundary\Response;
use Tasker\DataGateway\Factory as DataGatewayFactory;
use MultiTierArchitecture\UseCase\Definition\BaseAbstract as MultiTierArchitectureBase;

/**
 * This is 'base' abstract use case that contains a common code for the use cases in 'profile' namespace
 *
 * @category UseCase
 * @package  Tasker\UseCase
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
abstract class BaseAbstract extends MultiTierArchitectureBase
{
    /**
     * Init response object for the base class
     */
    public function __construct()
    {
        parent::__construct(new Response());
    }

    /**
     * Count the number of results based on the request object. This function will be available for the derived
     * classes to be overwritten
     *
     * @return int
     */
    protected function getTotalResultCount()
    {
        return 0;
    }

    /**
     * Initialize the repository passed by a it's name as a parameter
     *
     * @param string  $repositoryString  Name of the repository that need to be initialize
     *
     * @return void
     */
    protected function initAndSetRepository($repositoryString)
    {
        $repository = DataGatewayFactory::make($repositoryString);
        $this->setRepository($repository);
    }
}
