<?php 
namespace Tasker\Entity\Definition;

use MultiTierArchitecture\Entity\Definition\EntityInterface;

interface LockInterface extends EntityInterface
{
    public function getId();
    public function setId($id);
    public function getCreatingDateTime();
    public function setCreatingDateTime($creatingDateTime);
}
