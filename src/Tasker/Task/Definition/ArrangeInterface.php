<?php
namespace Tasker\Task\Definition;

use Tasker\Task\Packet\Arrange;

interface ArrangeInterface
{
    public function setPacket(Arrange $packet, $forceOverwrite = false);
    public function deleteByExternalTypeIdAndExternalId($externalTypeId, $externalId);
    public function deleteByExternalTypeId($externalTypeId);
    public function retrieveResponseByExternalTypeIdAndExternalId($externalTypeId, $externalId);
    public function retrieve(Arrange $packet);
}