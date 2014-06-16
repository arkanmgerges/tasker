<?php
namespace Tasker\UseCase\Lock;

use Tasker\UseCase\Definition\BaseAbstract;
use Tasker\Exception\UnExpectedException;

/**
 * This is 'create' use case of the lock
 *
 * @category UseCase
 * @package  Tasker\UseCase\Lock
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Create extends BaseAbstract
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
            $repository->create($this->getRequest());
        }
        catch (\Propel\Runtime\Exception\PropelException $e) {
            /* Code 23000 used for duplicating key, it's ok to ignore it because we don't want other processes to
             * have the same primary key as an id, this is related to this use case ('lock')
             */
            if ($e->getPrevious()->getCode() != 23000) {
                throw new UnExpectedException($e->getMessage(), __FILE__, __LINE__);
            }

            $response = $this->getResponse();
            $codes = $response->getCodes();
            array_push($codes, 23000);
            $response->setCodes($codes);
        }
        catch (\Exception $e) {
            throw new UnExpectedException($e->getMessage(), __FILE__, __LINE__);
        }
    }
}
