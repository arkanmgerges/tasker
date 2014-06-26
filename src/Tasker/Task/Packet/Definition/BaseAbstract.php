<?php
namespace Tasker\Task\Packet\Definition;

abstract class BaseAbstract
{
    private $id;
    private $typeId;
    private $repeatingInterval;
    private $priority;
    private $startingDateTime;
    private $externalTypeId;
    private $externalId;
    private $externalData;

    public function getPriority()
    {
        return $this->priority;
    }
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getExternalTypeId()
    {
        return $this->externalTypeId;
    }
    public function setExternalTypeId($externalTypeId)
    {
        $this->externalTypeId = $externalTypeId;
    }

    public function getExternalId()
    {
        return $this->externalId;
    }
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    public function getExternalData()
    {
        return $this->externalData;
    }
    public function setExternalData($externalData)
    {
        $this->externalData = $externalData;
    }

    public function setRepeatingInterval($repeatingInterval)
    {
        $this->repeatingInterval = $repeatingInterval;
    }

    public function getRepeatingInterval()
    {
        return $this->repeatingInterval;
    }

    public function setStartingDateTime($startingDateTime)
    {
        $this->startingDateTime = $startingDateTime;
    }

    public function getStartingDateTime()
    {
        return $this->startingDateTime;
    }

    public function getTypeId()
    {
        return $this->typeId;
    }

    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * Get all the attributes of this class
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
