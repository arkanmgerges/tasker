<?php 
namespace Tasker\Entity\Definition;

use MultiTierArchitecture\Entity\Definition\EntityInterface;

interface ProcessInterface extends EntityInterface
{
    public function getId();
    public function setId($id);
    public function getServer();
    public function setServer($server);
    public function getCreatingDateTime();
    public function setCreatingDateTime($creatingDateTime);
}
