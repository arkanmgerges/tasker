<?php
namespace Tasker\Task\Definition;

use Tasker\Task\Packet\Arrange;

interface ArrangeInterface
{
    public function setPacket(Arrange $packet);
    public function deleteByExternalTypeIdAndExternalId($externalTypeId, $externalId);
    public function deleteByExternalTypeId($externalTypeId);
    public function retrieveByExternalTypeIdAndExternalId($externalTypeId, $externalId);
    public function retrieve(Arrange $packet);
}