<?php
namespace Tasker\Manager\Definition;

use Tasker\Task\Definition\ArrangeInterface;
use Tasker\Task\Packet\Information;

interface ArrangeCallbackInterface
{
    public function callback(Information $info, ArrangeInterface $caller);
}
