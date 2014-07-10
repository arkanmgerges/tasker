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

    private $lastOperationSuccess = false;
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
        $this->executeUseCase('task|retrieve', new Request(['id' => $arrangePacket->getId()]));
        $response = $this->getUseCaseResponse();
        return $response->getResult();
    }

    public function retrieveResponseByExternalTypeIdAndExternalId($externalTypeId, $externalId)
    {
        $this->executeUseCase(
            'task|retrieve',
            new Request(
                [
                    'externalTypeId' => $externalTypeId,
                    'externalId'     => $externalId
                ]
            )
        );
        $response = $this->getUseCaseResponse();
        return $response->getResult();
    }

    public function deleteByExternalTypeIdAndExternalId($externalTypeId, $externalId)
    {
        $this->executeUseCase(
            'task|delete',
            new Request(
                [
                    'externalTypeId' => $externalTypeId,
                    'externalId'     => $externalId
                ]
            )
        );
        $this->lastOperationSuccess = $this->getUseCaseResponseStatus() == Response::STATUS_SUCCESS;
    }

    public function deleteByExternalTypeId($externalTypeId)
    {
        $this->executeUseCase(
            'task|delete',
            new Request(['externalTypeId' => $externalTypeId,])
        );
        $this->lastOperationSuccess = $this->getUseCaseResponseStatus() == Response::STATUS_SUCCESS;
    }

    public function delete(ArrangePacket $arrangePacket)
    {
        $this->executeUseCase('task|delete', new Request(['id' => $arrangePacket->getId()]));
        $this->lastOperationSuccess = $this->getUseCaseResponseStatus() == Response::STATUS_SUCCESS;
    }

    public function isLastOperationSucceeded()
    {
        return $this->lastOperationSuccess;
    }

    public function setPacket(ArrangePacket $arrangePacket, $forceOverwrite = false)
    {
        $lockId = ArrangeManager::ID_TYPE . '-' .
            $arrangePacket->getExternalTypeId() . '-' .
            $arrangePacket->getExternalId();

        // 1. Is it a unique mode ?
        if ($this->info->getArrangeMode() == ArrangeManager::ARRANGE_MODE_UNIQUE) {
            // 1.1 Lock the action arrange
            $this->createLock($lockId);
            $response = $this->getUseCaseResponse();
            // 1.2 If it could not lock, then exit
            if (in_array(23000, $response->getCodes()) || $response->getStatus() == Response::STATUS_FAIL) {
                $this->lastOperationSuccess = false;
            }
            // 1.3 Verify if there is another record that has the same type, id
            $response = $this->retrieveTaskResponseByIdAndType($arrangePacket->getExternalId(), $arrangePacket->getExternalTypeId());
            // 1.4 If it does exist, then delete the lock and exit
            if (($response->getStatus() == Response::STATUS_FAIL) ||
                (
                    ($response->getStatus() != Response::STATUS_FAIL) &&
                    ($response->getTotalResultCount() > 0) &&
                    (!$forceOverwrite)
                )
            ) {
                // 1.4.1 Delete the lock and return
                $this->deleteLock($lockId);
                $this->lastOperationSuccess = false;
            }

            // 1.5 check overwrite
            if ($forceOverwrite) {
                // 1.5.1 Retrieve task first
                $tasks = $this->retrieveResponseByExternalTypeIdAndExternalId(
                    $arrangePacket->getExternalTypeId(),
                    $arrangePacket->getExternalId()
                );
                if (isset($tasks[0]) && !empty($tasks[0])) {
                    /** @var Task $task */
                    $task = $tasks[0];
                    $this->updateTask($task->getId(), $arrangePacket);
                }
                else {
                    $this->createTask($arrangePacket);
                }
            }
            else {
                // 1.5.2 Create task
                $this->createTask($arrangePacket);
            }

            // 1.6 Delete the lock record
            $this->deleteLock($lockId);
        }
        else {
            // 2. If the mode is repeatable, then create the task
            $this->createTask($arrangePacket);
        }
    }

    private function createTask(ArrangePacket $arrangePacket)
    {
        $data = [];
        foreach ($arrangePacket->getAttributes() as $key => $value) {
            if ($value != null) {
                $data[$key] = $value;
            }
        }
        $data['creatingDateTime'] = date('Y-m-d H:i:s');
        $request = new Request($data);
        $this->runUseCaseWithNoOfRetriesOnFail(
            'task|createOverwriteByExternalTypeIdAndExternalId',
            $request,
            $this->getMaxRetries()
        );

        if ($this->getUseCaseResponseStatus() == Response::STATUS_FAIL) {
            $this->lastOperationSuccess = false;
        }
        $this->lastOperationSuccess = true;
    }

    private function updateTask($id, ArrangePacket $arrangePacket)
    {
        $data = [];
        foreach ($arrangePacket->getAttributes() as $key => $value) {
            if ($value != null) {
                $data[$key] = $value;
            }
        }
        unset($data['creatingDateTime']);
        $request = new Request(
            [
                ['id' => $id],
                $data
            ]
        );
        $this->runUseCaseWithNoOfRetriesOnFail(
            'task|update',
            $request,
            $this->getMaxRetries()
        );

        if ($this->getUseCaseResponseStatus() == Response::STATUS_FAIL) {
            $this->lastOperationSuccess = false;
        }
        $this->lastOperationSuccess = true;
    }

    private function deleteLock($lockId)
    {
        $this->runUseCaseWithNoOfRetriesOnFail(
            'lock|delete',
            new Request(['id' => $lockId]),
            $this->getMaxRetries()
        );
    }

    private function getMaxRetries()
    {
        return $this->info->getProcessMaxRetryTimeBeforeContinue();
    }


    private function createLock($lockId)
    {
        $this->runUseCaseWithNoOfRetriesOnFail(
            'lock|create',
            new Request(['id' => $lockId, 'creatingDateTime' => date('Y-m-d H:i:s')]),
            $this->getMaxRetries()
        );
    }

    private function retrieveTaskResponseByIdAndType($id, $type)
    {
        $this->executeUseCase(
            'task|retrieveOneUnEnded',
            new Request(
                [
                    'externalId' => $id,
                    'externalTypeId' => $type,
                ]
            )
        );
        return $this->getUseCaseResponse();
    }
}
