<?php
namespace Tasker\Entity;

use Tasker\Entity\Definition\ProcessInterface;


class Process implements ProcessInterface
{
    private $id;
    private $server;
    private $extra;
    private $creatingDateTime;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getServer()
    {
        return $this->server;
    }
    public function setServer($server)
    {
        $this->server = $server;
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
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
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
