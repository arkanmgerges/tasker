<?php
namespace Test\Tasker\Misc;

use Tasker\Manager\Definition\ActCallbackInterface;
use Tasker\Task\Packet\Act;
use Tasker\Task\Packet\Information;
use Tasker\Task\Act\Action;

class ActCallback implements ActCallbackInterface
{
    public function callback(Information $info, Act $dataContainer, Action $action)
    {
        $data = $dataContainer->getExternalData();
        if (!is_numeric($data)) {
            $data = 0;
        }
        $data++;
        $dataContainer->setExternalData($data);
        if ($action->updateTask($dataContainer)) {
            $action->endTask();
        }
    }
}