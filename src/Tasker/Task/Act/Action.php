<?php
namespace Tasker\Task\Act;

use Tasker\Task\Act;
use Tasker\Task\Packet\Act as ActPacket;

class Action
{
    private $actTask = null;

    public function __construct(Act $actTask)
    {
        $this->actTask = $actTask;
    }

    public function endTask()
    {
        $this->actTask->endTask();
    }

    public function updateTask(ActPacket $actPacket)
    {
        return $this->actTask->updateTask($actPacket);
    }
} 