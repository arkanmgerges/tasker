<?php
namespace Tasker\DataGateway\Definition;

use Tasker\Boundary\Request;
use MultiTierArchitecture\DataGateway\Definition\RepositoryInterface;

/**
 * Task interface used as a contract to represent the task data gateway
 *
 * @category DataGateway
 * @package  Tasker\DataGateway
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
interface TaskRepositoryInterface extends RepositoryInterface
{
    /**
     * Retrieve the task passing request object
     *
     * @param Request  $request  Request that is used for this search method
     *
     * @return array
     */
    public function retrieve(Request $request);

    /**
     * Update the task passing request object
     *
     * @param Request  $request  Request that is used for this update method
     *
     * @return array
     */
    public function update(Request $request);

    /**
     * Create the task passing request object
     *
     * @param Request  $request  Request that is used for this create method
     *
     * @return array
     */
    public function create(Request $request);
}
