<?php
namespace Tasker\Task;

use Tasker\Boundary\Request;
use Tasker\Boundary\Response;
use Tasker\Definition\CommandInterface;
use Tasker\Entity\Task;
use Tasker\Task\Act\Action;
use Tasker\Task\Packet\Act as ActPacket;
use Tasker\Manager\Definition\ActCallbackInterface;
use Tasker\Task\Packet\Information;
use Tasker\UseCase\Definition\HelperTrait;
use Tasker\Manager\Act as ActManager;

class Act implements CommandInterface
{
    use HelperTrait;

    /** @var Information $info */
    private $info = null;
    private $callback = null;
    /** @var Task $currentTask */
    private $currentTask = null;
    private $externalTypeId = null;

    /** @var ActPacket $actPacket */
    private $actPacket   = null;

    private $lastOperationSuccess = false;

    public function __construct(Information $info, ActCallbackInterface $callback, $externalTypeId)
    {
        $this->info           = $info;
        $this->callback       = $callback;
        $this->externalTypeId = $externalTypeId;
    }

    public function execute()
    {
        $loop = true;
        $offset = 0;
        while($loop) {
            // 1. Find record
            $request = new Request([Request::EXTRA_OFFSET => $offset, 'externalTypeId' => $this->externalTypeId]);
            $this->runUseCaseWithNoOfRetriesOnFail(
                'task|retrieveOneToProcess',
                $request,
                $this->getMaxRetries()
            );
            $response = $this->getUseCaseResponse();
            if ($response->getStatus() == Response::STATUS_FAIL) {
                $this->lastOperationSuccess = false;
                return;
            }

            $result = $response->getResult();

            // 2. If there are no assigned records then, return
            $totalResultCount = $response->getTotalResultCount();
            if ((empty($totalResultCount)) || (!isset($result[0]))) {
                $loop = false;
                continue;
            }

            if ($offset >= $totalResultCount) {
                $offset = 0;
            }

            // 3. Get the id
            $id = $result[0]->getId();
            // 4. Lock by id
            $lockId = ActManager::ID_TYPE . '-' . $id;
            $this->createLock($lockId);
            $response = $this->getUseCaseResponse();

            // 5. If it could not lock, then exit
            if ($response->getStatus() == Response::STATUS_FAIL) {
                $this->lastOperationSuccess = false;
                return;
            }
            // 6. If conflict then increase offset
            if (in_array(23000, $response->getCodes())) {
                $this->lastOperationSuccess = false;
                $offset++;
                continue;
            }

            /*
             * Save the packet so it can be accessed by methods that can be called by the callback like endTask()
             * through Action class
             */
            if ($result[0] instanceof Task) {
                $this->currentTask = $result[0];
            }

            // 7. Update the current task to change its status
            $this->updateTaskStatusByIdAndStatusId($id, Task::STATUS_ID_PROCESSING);

            // 8. Retrieve record
            $this->runUseCaseWithNoOfRetriesOnFail(
                'task|retrieve',
                new Request(['id' => $id]),
                $this->getMaxRetries()
            );
            $response = $this->getUseCaseResponse();
            $result = $response->getResult();
            if (($response->getTotalResultCount() > 0) &&
                (isset($result[0])) &&
                ($result[0] instanceof Task)
            ) {
                $actPacket = new ActPacket();
                /** @var \Tasker\Entity\Task $task */
                $task      = $result[0];
                $actPacket->setExternalId($task->getExternalId());
                $actPacket->setExternalTypeId($task->getExternalTypeId());
                $actPacket->setExternalData($task->getExternalData());
                $actPacket->setRepeatingInterval($task->getRepeatingInterval());
                $actPacket->setPriority($task->getPriority());
                $actPacket->setTypeId($task->getTypeId());
                $actPacket->setStartingDateTime($task->getStartingDateTime());
                // Save the old values before calling callback
                $this->actPacket = $actPacket;
                $this->callback->callback($this->info, $actPacket, new Action($this));
            }
            // 9. Update the current task to change its status
            if ($this->currentTask->getStatusId() != Task::STATUS_ID_ENDED) {
                $this->updateTaskStatusByIdAndStatusId($id, Task::STATUS_ID_SLEEPING);
            }
            // 10. Delete the lock record
            $this->deleteLock($lockId);
            return;
        }
    }

    public function endTask()
    {
        $id = $this->currentTask->getId();
        $request = new Request(
            [
                ['id' => $id],
                [
                    'statusId'       => Task::STATUS_ID_ENDED,
                    'endingDateTime' => date('Y-m-d H:i:s')
                ]
            ]
        );
        $this->runUseCaseWithNoOfRetriesOnFail(
            'task|update',
            $request,
            $this->getMaxRetries()
        );
        if ($this->getUseCaseResponseStatus() == Response::STATUS_FAIL) {
            $this->lastOperationSuccess = false;
            return;
        }
        $this->lastOperationSuccess = true;
        $this->currentTask->setStatusId(Task::STATUS_ID_ENDED);
    }

    public function isLastOperationSucceeded()
    {
        return $this->lastOperationSuccess;
    }

    public function updateTask(ActPacket $actPacket)
    {
        // If it's a recurring type task, and the old value of starting date time is not change, then updated it
        if (($actPacket->getTypeId() == Task::TYPE_ID_RECURRENT) &&
            ($actPacket->getStartingDateTime() == $this->actPacket->getStartingDateTime())) {
            $actPacket->setStartingDateTime(date('Y-m-d H:i:s'));
        }

        $id = $this->currentTask->getId();
        $request = new Request(
            [
                ['id' => $id],
                [
                    'typeId'            => $actPacket->getTypeId(),
                    'repeatingInterval' => $actPacket->getRepeatingInterval(),
                    'startingDateTime'  => $actPacket->getStartingDateTime(),
                    'priority'          => $actPacket->getPriority(),
                    'externalTypeId'    => $actPacket->getExternalTypeId(),
                    'externalId'        => $actPacket->getExternalId(),
                    'externalData'      => $actPacket->getExternalData()
                ]
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
        // Update the current task
        $this->currentTask->setTypeId($actPacket->getTypeId());
        $this->lastOperationSuccess = true;
    }

    private function updateTaskStatusByIdAndStatusId($id, $statusId)
    {
        // If it's a recurring type task, and the old value of starting date time is not change, then updated it
        $request = null;
        if ($this->currentTask->getTypeId() == Task::TYPE_ID_RECURRENT) {
            $request = new Request([['id' => $id],['statusId' => $statusId, 'startingDateTime' => date('Y-m-d H:i:s')]]);
        }
        else {
            $request = new Request([['id' => $id],['statusId' => $statusId]]);
        }
        $this->runUseCaseWithNoOfRetriesOnFail('task|update', $request, $this->getMaxRetries());
    }

    private function createLock($lockId)
    {
        $request = new Request(['id' => $lockId, 'creatingDateTime' => date('Y-m-d H:i:s')]);
        $this->runUseCaseWithNoOfRetriesOnFail(
            'lock|create',
            $request,
            $this->getMaxRetries()
        );
        $this->lastOperationSuccess = $this->getUseCaseResponseStatus() == Response::STATUS_SUCCESS;
    }

    private function deleteLock($id)
    {
        $this->runUseCaseWithNoOfRetriesOnFail(
            'lock|delete',
            new Request(['id' => $id]),
            $this->getMaxRetries()
        );
        $this->lastOperationSuccess = $this->getUseCaseResponseStatus() == Response::STATUS_SUCCESS;
    }

    private function getMaxRetries()
    {
        return $this->info->getProcessMaxRetryTimeBeforeContinue();
    }
}
