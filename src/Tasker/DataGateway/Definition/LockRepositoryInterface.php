<?php
namespace Tasker\DataGateway\Definition;

use Tasker\Boundary\Request;
use MultiTierArchitecture\DataGateway\Definition\RepositoryInterface;

/**
 * Lock interface used as a contract to represent the lock data gateway
 *
 * @category DataGateway
 * @package  Tasker\DataGateway
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
interface LockRepositoryInterface extends RepositoryInterface
{
    /**
     * Retrieve the lock passing request object
     *
     * @param Request  $request  Request that is used for this search method
     *
     * @return array
     */
    public function retrieve(Request $request);

    /**
     * Update the lock passing request object
     *
     * @param Request  $request  Request that is used for this update method
     *
     * @return array
     */
    public function update(Request $request);

    /**
     * Create the lock passing request object
     *
     * @param Request  $request  Request that is used for this create method
     *
     * @return array
     */
    public function create(Request $request);
}
