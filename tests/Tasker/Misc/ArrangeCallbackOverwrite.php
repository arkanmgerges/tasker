<?php
namespace Test\Tasker\Misc;

use Tasker\Manager\Definition\ArrangeCallbackInterface;
use Tasker\Task\Definition\ArrangeInterface;
use Tasker\Task\Packet\Arrange as ArrangePacket;
use Tasker\Task\Packet\Information;
use Tasker\Entity\Task;

class ArrangeCallbackOverwrite implements ArrangeCallbackInterface
{
    public function callback(Information $info, ArrangeInterface $caller)
    {
        $packet = new ArrangePacket();
        $packet->setExternalId(10);
        $packet->setExternalTypeId(2);
        $packet->setPriority(1);
        $packet->setTypeId(Task::TYPE_ID_RECURRENT);
        $packet->setRepeatingInterval(900); // Every 15 minutes
        $packet->setStartingDateTime(date('Y-m-d H:i:s'));
        $packet->setExternalData('test-' . mt_rand(0, 999999));

        if ($caller->setPacket($packet, true)) {
            // ok
        }
    }
}