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
            $response = $this->runUseCaseWithNoOfRetriesOnFailAndReturnResponse(
                'task|retrieve',
                $request,
                $this->getMaxRetries()
            );

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
            $response = $this->createLock($lockId);

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
        $this->runUseCaseWithNoOfRetriesOnFailAndReturnStatus(
            'task|update',
            $request,
            $this->getMaxRetries()
        );
    }

    private function deleteLock($lockId)
    {
        $params['useCaseString'] = 'lock|delete';
        $params['request'] = new Request(['id' => $lockId]);
        $params['processMaxRetryTimeBeforeContinue'] = $this->getMaxRetries();
        $this->executeLockUseCaseAndReturnResponse($params);
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
