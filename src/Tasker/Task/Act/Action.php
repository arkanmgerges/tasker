<?php
namespace Tasker\Task\Act;

use Tasker\Task\Act;
use Tasker\Task\Packet\Act as ActPacket;

class Action
{
    private $actTask = null;
    private $updateTaskSuccess = false;

    public function __construct(Act $actTask)
    {
        $this->actTask = $actTask;
    }

    public function endTask()
    {
        $this->actTask->endTask();
    }

    public function isUpdateTaskSucceeded()
    {
        return $this->updateTaskSuccess;
    }

    public function updateTask(ActPacket $actPacket)
    {
        $this->actTask->updateTask($actPacket);
        $this->updateTaskSuccess =  $this->actTask->isLastOperationSucceeded();
    }
} 