<?php
namespace Tasker\DataGateway\Db\Mapper\Task\Pdo;

use Tasker\DataGateway\Db\Mapper\Definition\CommonEntityMapperAbstract;

/**
 * Mapper class for the action entity
 *
 * @category Mapper
 * @package  Tasker\Mapper
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Entity extends CommonEntityMapperAbstract
{
    /**
     * @var string  $dbEntityName  Entity object that represents the current db entity in the orm
     */
    private $dbEntityName = 'Task';
    /**
     * @var string  $entityName  Entity object that does not have any dependency
     */
    private $entityName   = 'Task';


    /**
     * @var array  $mappingEntityToDbEntityAttributes  Mappings to entity's db attributes' names
     */
    private $mappingEntityToDbEntityAttributes = [
        'id'                => 'id',
        'server'            => 'server',
        'statusId'          => 'statusId',
        'typeId'            => 'typeId',
        'repeatingInterval' => 'repeatingInterval',
        'creatingDateTime'  => 'creatingDateTime',
        'startingDateTime'  => 'startingDateTime',
        'endingDateTime'    => 'endingDateTime',
        'priority'          => 'priority',
        'externalTypeId'    => 'externalTypeId',
        'externalId'        => 'externalId',
        'externalData'      => 'externalData',
    ];

    /**
     * Set the default mapping data
     */
    public function __construct()
    {
        parent::__construct(
            $this->mappingEntityToDbEntityAttributes,
            array_flip($this->mappingEntityToDbEntityAttributes)
        );
    }

    /**
     * Get mapped db entities array
     *
     * @return \Propel\Runtime\Collection\Collection Array of entities
     */
    public function getMappedSecondEntities()
    {
        return $this->getMappedSecondEntitiesByEntityType($this->dbEntityName);
    }

    /**
     * Get mapped entities array
     *
     * @return array Array of entities
     */
    public function getMappedFirstEntities()
    {
        return $this->getMappedFirstEntitiesByEntityType($this->entityName);
    }
}

