<?php
namespace Tasker\Entity;

use Tasker\Entity\Definition\TaskInterface;

class Task implements TaskInterface
{
    const TYPE_ID_STANDARD  = 1;
    const TYPE_ID_RECURRENT = 2;

    const STATUS_ID_ASSIGNED   = 1;
    const STATUS_ID_PROCESSING = 2;
    const STATUS_ID_ENDED      = 3;
    const STATUS_ID_SLEEPING   = 4;

    private $id;
    private $server;
    private $statusId;
    private $typeId;
    private $repeatingInterval;
    private $creatingDateTime;
    private $startingDateTime;
    private $endingDateTime;
    private $priority;
    private $externalTypeId;
    private $externalId;
    private $externalData;

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

    public function getStartingDateTime()
    {
        return $this->startingDateTime;
    }
    public function setStartingDateTime($startingDateTime)
    {
        $this->startingDateTime = $startingDateTime;
    }

    public function getEndingDateTime()
    {
        return $this->endingDateTime;
    }
    public function setEndingDateTime($endingDateTime)
    {
        $this->endingDateTime = $endingDateTime;
    }

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

    public function getStatusId()
    {
        return $this->statusId;
    }

    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
    }

    public function setRepeatingInterval($repeatingInterval)
    {
        $this->repeatingInterval = $repeatingInterval;
    }

    public function getRepeatingInterval()
    {
        return $this->repeatingInterval;
    }

    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    public function getTypeId()
    {
        return $this->typeId;
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
