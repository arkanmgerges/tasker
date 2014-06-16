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

    /** @var ActPacket $actPacket */
    private $actPacket   = null;

    public function __construct(Information $info, ActCallbackInterface $callback)
    {
        $this->info = $info;
        $this->callback   = $callback;
    }

    public function execute()
    {
        $loop = true;
        $offset = 0;
        while($loop) {
            // 1. Find record
            $request = new Request(
                [
                    'sql' => [
                        'statement'         => 'SELECT * FROM :table1: WHERE ((startingDateTime + repeatingInterval) ' .
                                               '< now()) AND ((statusId != ' . Task::STATUS_ID_PROCESSING . ') AND ' .
                                               '(statusId != ' . Task::STATUS_ID_ENDED . ')) AND ' .
                                               '(server IS NOT NULL) ORDER BY priority DESC, modifyingDateTime '.
                                               'LIMIT '. $offset .',1;',
                        'statementForCount' => 'SELECT * FROM :table1: WHERE ((startingDateTime + repeatingInterval) ' .
                                               '< now()) AND ((statusId != ' . Task::STATUS_ID_PROCESSING . ') AND ' .
                                               '(statusId != ' . Task::STATUS_ID_ENDED . ')) AND (server IS NOT NULL);',
                    ]
                ]
            );
            $response = $this->runUseCaseWithNoOfRetriesOnFailAndReturnResponse(
                'task|retrieve',
                $request,
                $this->getMaxRetries()
            );

            if ($response->getStatus() == Response::STATUS_FAIL) {
                return;
            }
            $result = $response->getResult();

            // 2. If there are no assigned records then, return
            $totalResultCount = $response->getTotalResultCount();
            if ($totalResultCount == 0) {
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
            $response = $this->createLock($lockId);

            // 5. If it could not lock, then exit
            if ($response->getStatus() == Response::STATUS_FAIL) {
                return;
            }
            // 6. If conflict then increase offset
            if (in_array(23000, $response->getCodes())) {
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
            $this->updateTaskStatusAndStartingDateTimeInCaseOfRecurringTypeById($id, Task::STATUS_ID_PROCESSING);

            // 8. Retrieve record
            $response = $this->runUseCaseWithNoOfRetriesOnFailAndReturnResponse(
                'task|retrieve',
                new Request(['id' => $id]),
                $this->getMaxRetries()
            );
            $result = $response->getResult();
            if ($response->getTotalResultCount() > 0) {
                $actPacket = new ActPacket();
                $actPacket->setExternalId($result[0]->getExternalId());
                $actPacket->setExternalTypeId($result[0]->getExternalTypeId());
                $actPacket->setExternalData($result[0]->getExternalData());
                $actPacket->setPriority($result[0]->getPriority());
                $actPacket->setTypeId($result[0]->getTypeId());
                $actPacket->setStartingDateTime($result[0]->getStartingDateTime());
                // Save the old values before calling callback
                $this->actPacket = $actPacket;
                $this->callback->callback($this->info, $actPacket, new Action($this));
            }
            // 9. Update the current task to change its status
            if ($this->currentTask->getStatusId() != Task::STATUS_ID_ENDED) {
                $this->updateTaskStatusAndStartingDateTimeInCaseOfRecurringTypeById($id, Task::STATUS_ID_SLEEPING);
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
        $response = $this->runUseCaseWithNoOfRetriesOnFailAndReturnStatus(
            'task|update',
            $request,
            $this->getMaxRetries()
        );
        if ($response->getStatus() == Response::STATUS_FAIL) {
            return;
        }
        $this->currentTask->setStatusId(Task::STATUS_ID_ENDED);
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
        $status = $this->runUseCaseWithNoOfRetriesOnFailAndReturnStatus(
            'task|update',
            $request,
            $this->getMaxRetries()
        );
        if ($status == Response::STATUS_FAIL) {
            return;
        }
        // Update the current task
        $this->currentTask->setTypeId($actPacket->getTypeId());
    }

    private function updateTaskStatusAndStartingDateTimeInCaseOfRecurringTypeById($id, $statusId)
    {
        // If it's a recurring type task, and the old value of starting date time is not change, then updated it
        $request = null;
        if ($this->currentTask->getTypeId() == Task::TYPE_ID_RECURRENT) {
            $request = new Request([['id' => $id],['statusId' => $statusId, 'startingDateTime' => date('Y-m-d H:i:s')]]);
        }
        else {
            $request = new Request([['id' => $id],['statusId' => $statusId]]);
        }
        $this->runUseCaseWithNoOfRetriesOnFailAndReturnStatus('task|update', $request, $this->getMaxRetries());
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

    private function deleteLock($id)
    {
        $params['useCaseString'] = 'lock|delete';
        $params['request'] = new Request(['id' => $id]);
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
}
