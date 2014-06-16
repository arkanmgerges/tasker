<?php
namespace Tasker\Task\Definition;

use Tasker\Task\Packet\Arrange;

interface ArrangeInterface
{
    public function setPacket(Arrange $packet);
} 