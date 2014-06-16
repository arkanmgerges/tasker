<?php
namespace Tasker\Entity;

use Tasker\Entity\Definition\LockInterface;

class Lock implements LockInterface
{
    private $id;
    private $creatingDateTime;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCreatingDateTime()
    {
        return $this->creatingDateTime;
    }
    public function setCreatingDateTime($creatingDateTime)
    {
        $this->creatingDateTime = $creatingDateTime;
    }

    /**
     * Get all the attributes of the entity
     *
     * @return array Associated array, which key is the name of the attribute and value is its value
     */
    public function getAttributes()
    {
        $attributes = [];
        foreach ($this as $attributeKey => $attributeValue) {
            $attributes[$attributeKey] = $attributeValue;
        }
        return $attributes;
    }
}
