<?php
namespace Tasker\UseCase\Lock;

use Tasker\UseCase\Definition\BaseAbstract;
use Tasker\Exception\UnExpectedException;

/**
 * This is 'update' use case of the lock
 *
 * @category UseCase
 * @package  Tasker\UseCase\Lock
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Update extends BaseAbstract
{
    /**
     * Template pattern, this will be run by execute(), in this method, data gateway call will be executed
     *
     * @throws UnExpectedException  Unexpected behaviour from the api
     *
     * @return void
     */
    public function executeDataGateway()
    {
        try {
            $this->initAndSetRepository('repository|lock');
            /** @var \Tasker\DataGateway\Repository\Lock $repository */
            $repository = $this->getRepository();
            $repository->update($this->getRequest());
        }
        catch (\Exception $e) {
            throw new UnExpectedException($e->getMessage(), __FILE__, __LINE__);
        }
    }
}
