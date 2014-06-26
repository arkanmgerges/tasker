<?php
namespace Tasker\Task;

use Tasker\Boundary\Request;
use Tasker\Boundary\Response;
use Tasker\Entity\Task;
use Tasker\Task\Definition\ArrangeInterface;
use Tasker\Task\Packet\Information;
use Tasker\UseCase\Definition\HelperTrait;
use Tasker\Manager\Arrange as ArrangeManager;
use Tasker\Task\Packet\Arrange as ArrangePacket;

class Arrange implements ArrangeInterface
{
    use HelperTrait;

    private $typeId = Task::TYPE_ID_STANDARD;

    /** @var Information $info */
    private $info = null;

    public function __construct(Information $info)
    {
        $this->info = $info;
    }

    public function setTypeId($typeId)
    {
        if ($typeId == Task::TYPE_ID_STANDARD || $typeId == Task::TYPE_ID_RECURRENT) {
            $this->typeId = $typeId;
        }
    }

    public function getTypeId()
    {
        return $this->typeId;
    }

    public function retrieve(ArrangePacket $arrangePacket)
    {
        $response = $this->runUseCaseAndReturnResponse('task|retrieve', new Request(['id' => $arrangePacket->getId()]));
        return $response->getResult();
    }

    public function retrieveByExternalTypeIdAndExternalId($externalTypeId, $externalId)
    {
        $response = $this->runUseCaseAndReturnResponse(
            'task|retrieve',
            new Request(
                [
                    'externalTypeId' => $externalTypeId,
                    'externalId'     => $externalId
                ]
            )
        );
        return $response->getResult();
    }

    public function deleteByExternalTypeIdAndExternalId($externalTypeId, $externalId)
    {
        $response = $this->runUseCaseAndReturnResponse(
            'task|delete',
            new Request(
                [
                    'externalTypeId' => $externalTypeId,
                    'externalId'     => $externalId
                ]
            )
        );
        return ($response->getStatus() == Response::STATUS_SUCCESS);
    }

    public function deleteByExternalTypeId($externalTypeId)
    {
        $response = $this->runUseCaseAndReturnResponse(
            'task|delete',
            new Request(['externalTypeId' => $externalTypeId,])
        );
        return ($response->getStatus() == Response::STATUS_SUCCESS);
    }

    public function delete(ArrangePacket $arrangePacket)
    {
        $response = $this->runUseCaseAndReturnResponse('task|delete', new Request(['id' => $arrangePacket->getId()]));
        return ($response->getStatus() == Response::STATUS_SUCCESS);
    }

    public function setPacket(ArrangePacket $arrangePacket)
    {
        $lockId = ArrangeManager::ID_TYPE . '-' .
            $arrangePacket->getExternalTypeId() . '-' .
            $arrangePacket->getExternalId();

        // 1. Is it a unique mode ?
        if ($this->info->getArrangeMode() == \Tasker\Manager\Arrange::ARRANGE_MODE_UNIQUE) {
            // 1.1 Lock the action arrange
            $response = $this->createLock($lockId);
            // 1.2 If it could not lock, then exit
            if (in_array(23000, $response->getCodes()) || $response->getStatus() == Response::STATUS_FAIL) {
                return false;
            }
            // 1.3 Verify if there is another record that has the same type, id
            $response = $this->retrieveTask($arrangePacket->getExternalId(), $arrangePacket->getExternalTypeId());
            // 1.4 If it does exist, then delete the lock and exit
            if (($response->getStatus() == Response::STATUS_FAIL) ||
                (($response->getStatus() != Response::STATUS_FAIL) &&
                ($response->getTotalResultCount() > 0))
            ) {
                // 1.4.1 Delete the lock and return
                $this->deleteLock($lockId);
                return false;
            }
            // 1.5 If there is no other record, then create a new task record
            $isSucceed = $this->createTask($arrangePacket);
            // 1.6 Delete the lock record
            $this->deleteLock($lockId);
            return $isSucceed;
        }
        else {
            // 2. If the mode is repeatable, then create the task
            return $this->createTask($arrangePacket);
        }
    }

    private function createTask(ArrangePacket $arrangePacket)
    {
        $request = new Request(
            [
                'externalId'       => $arrangePacket->getExternalId(),
                'externalTypeId'   => $arrangePacket->getExternalTypeId(),
                'externalData'     => $arrangePacket->getExternalData(),
                'priority'         => $arrangePacket->getPriority(),
                'startingDateTime' => $arrangePacket->getStartingDateTime(),
                'creatingDateTime' => date('Y-m-d H:i:s')
            ]
        );
        $status = $this->runUseCaseWithNoOfRetriesOnFailAndReturnStatus(
            'task|create',
            $request,
            $this->getMaxRetries()
        );

        if ($status == Response::STATUS_FAIL) {
            return false;
        }
        return true;
    }

    private function deleteLock($lockId)
    {
        $params['useCaseString'] = 'lock|delete';
        $params['request'] = new Request(['id' => $lockId]);
        $params['processMaxRetryTimeBeforeContinue'] = $this->getMaxRetries();
        $this->executeLockUseCaseAndReturnResponse($params);
    }

    private function executeLockUseCaseAndReturnResponse($params)
    {
        $response = $this->runUseCaseWithNoOfRetriesOnFailAndReturnResponse(
            $params['useCaseString'],
            $params['request'],
            $params['processMaxRetryTimeBeforeContinue']
        );
        return $response;
    }

    private function getMaxRetries()
    {
        return $this->info->getProcessMaxRetryTimeBeforeContinue();
    }


    private function createLock($lockId)
    {
        $request = new Request(['id' => $lockId, 'creatingDateTime' => date('Y-m-d H:i:s')]);
        $params = [
            'useCaseString' => 'lock|create',
            'request' => $request,
            'processMaxRetryTimeBeforeContinue' => $this->getMaxRetries()
        ];
        $response = $this->executeLockUseCaseAndReturnResponse($params);
        return $response;
    }

    private function retrieveTask($id, $type)
    {
        $response = $this->runUseCaseAndReturnResponse(
            'task|retrieveOneUnEnded',
            new Request(
                [
                    'externalId' => $id,
                    'externalTypeId' => $type,
                ]
            )
        );
        return $response;
    }
}
