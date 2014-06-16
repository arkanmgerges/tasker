<?php 
namespace Tasker\Entity\Definition;

use MultiTierArchitecture\Entity\Definition\EntityInterface;

interface TaskInterface extends EntityInterface
{
    public function getId();
    public function setId($id);
    public function getServer();
    public function setServer($server);
    public function getCreatingDateTime();
    public function setCreatingDateTime($creatingDateTime);
    public function getStartingDateTime();
    public function setStartingDateTime($startingDateTime);
    public function getEndingDateTime();
    public function setEndingDateTime($endingDateTime);
    public function getPriority();
    public function setPriority($priority);
    public function getExternalTypeId();
    public function setExternalTypeId($externalTypeId);
    public function getExternalId();
    public function setExternalId($externalId);
    public function getExternalData();
    public function setExternalData($externalData);
}
