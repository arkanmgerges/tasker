<?php
namespace Tasker\UseCase\Process;

use Tasker\UseCase\Definition\BaseAbstract;
use Tasker\Exception\UnExpectedException;

/**
 * This is 'delete' use case of the process
 *
 * @category UseCase
 * @package  Tasker\UseCase\Process
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Delete extends BaseAbstract
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
            $this->initAndSetRepository('repository|process');
            /** @var \Tasker\DataGateway\Repository\Process $repository */
            $repository = $this->getRepository();
            $repository->delete($this->getRequest());
        }
        catch (\Exception $e) {
            throw new UnExpectedException($e->getMessage(), __FILE__, __LINE__);
        }
    }
}
