<?php
namespace Tasker\Manager\Definition;

use Tasker\Task\Packet\Act;
use Tasker\Task\Packet\Information;
use Tasker\Task\Act\Action;

interface ActCallbackInterface
{
    public function callback(Information $info, Act $dataContainer, Action $action);
}
