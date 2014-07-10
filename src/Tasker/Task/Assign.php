<?php
namespace Tasker\Task;

use Tasker\Boundary\Request;
use Tasker\Boundary\Response;
use Tasker\Definition\CommandInterface;
use Tasker\Entity\Task;
use Tasker\Task\Packet\Information;
use Tasker\UseCase\Definition\HelperTrait;
use Tasker\Manager\Assign as AssignManager;

class Assign implements CommandInterface
{
    use HelperTrait;

    private $lastOperationSuccess = false;
    /** @var Information $info */
    private $info = null;

    public function __construct(Information $info)
    {
        $this->info = $info;
    }

    public function execute()
    {
        $loop = true;
        while($loop) {
            // 1. Find record with server = null
            $request = new Request(['server' => ''], [Request::EXTRA_LIMIT => 1]);
            $this->runUseCaseWithNoOfRetriesOnFail(
                'task|retrieve',
                $request,
                $this->getMaxRetries()
            );

            $response = $this->getUseCaseResponse();
            $result = $response->getResult();

            // 2. If there are no unassigned servers then, return
            $resultCount = $response->getTotalResultCount();
            if ((empty($resultCount)) || (!isset($result[0]))) {
                $loop = false;
                continue;
            }

            // 3. Get the id
            $id = $result[0]->getId();
            // 4. Lock by id
            $lockId = AssignManager::ID_TYPE . '-' . $id;
            $this->createLock($lockId);

            $response = $this->getUseCaseResponse();
            // 5. If it could not lock, then exit
            if (in_array(23000, $response->getCodes()) || $response->getStatus() == Response::STATUS_FAIL) {
                continue;
            }
            // 6. If there is no other record, then create a new task record
            $this->updateTaskById($id, $this->info->getHostname(), Task::STATUS_ID_ASSIGNED);
            // 7. Delete the lock record
            $this->deleteLock($lockId);
        }
    }

    public function isLastOperationSucceeded()
    {
        return $this->lastOperationSuccess;
    }

    private function updateTaskById($id, $server, $statusId)
    {
        $request = new Request(
            [
                ['id' => $id],
                [
                    'server'           => $server,
                    'statusId'         => $statusId,
                ]
            ]
        );
        $this->runUseCaseWithNoOfRetriesOnFail(
            'task|update',
            $request,
            $this->getMaxRetries()
        );

        $this->lastOperationSuccess = $this->getUseCaseResponseStatus() == Response::STATUS_SUCCESS;
    }

    private function deleteLock($lockId)
    {
        $this->runUseCaseWithNoOfRetriesOnFail(
            'lock|delete',
            new Request(['id' => $lockId]),
            $this->getMaxRetries()
        );
        $this->lastOperationSuccess = $this->getUseCaseResponseStatus() == Response::STATUS_SUCCESS;
    }

    private function createLock($lockId)
    {
        $this->runUseCaseWithNoOfRetriesOnFail(
            'lock|create',
            new Request(['id' => $lockId, 'creatingDateTime' => date('Y-m-d H:i:s')]),
            $this->getMaxRetries()
        );
        $this->lastOperationSuccess = $this->getUseCaseResponseStatus() == Response::STATUS_SUCCESS;
    }

    private function getMaxRetries()
    {
        return $this->info->getProcessMaxRetryTimeBeforeContinue();
    }
}
