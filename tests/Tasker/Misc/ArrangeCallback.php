<?php
namespace Test\Tasker\Misc;

use Tasker\Entity\Task;
use Tasker\Manager\Definition\ArrangeCallbackInterface;
use Tasker\Task\Definition\ArrangeInterface;
use Tasker\Task\Packet\Arrange as ArrangePacket;
use Tasker\Task\Packet\Information;

class ArrangeCallback implements ArrangeCallbackInterface
{
    public function callback(Information $info, ArrangeInterface $caller)
    {
        $packet = new ArrangePacket();
        $packet->setExternalId(1);
        $packet->setExternalTypeId(2);
        $packet->setPriority(1);
        $packet->setStartingDateTime(date('Y-m-d H:i:s'));
        $packet->setExternalData('arrange-stage-test');

        $caller->setPacket($packet);
    }
}