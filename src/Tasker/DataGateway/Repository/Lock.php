<?php
namespace Tasker\DataGateway\Repository;

use Propel\Runtime\Propel;
use Tasker\Boundary\Request;
use Tasker\DataGateway\Db\Mapper\Factory as DbMapperFactory;
use Tasker\DataGateway\Definition\LockRepositoryInterface;
use Tasker\DataGateway\Db\Entity\LockQuery;
use Tasker\DataGateway\Repository\Definition\DbAbstract;
use Tasker\DataGateway\Exception\EmptyArray;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;

/**
 * This class is used to represent lock repository data gateway
 *
 * @category DataGateway
 * @package  Tasker\DataGateway
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Lock extends DbAbstract implements LockRepositoryInterface
{
    /**
     * Create a new lock into the data gateway
     *
     * @param Request  $request  Request that will contain attributes of the new entity to be created
     *
     * @throws EmptyArray When an empty array is found
     *
     * @return void
     */
    public function create(Request $request)
    {
        /** @var \Tasker\DataGateway\Db\Mapper\Lock\Entity $mapper */
        $mapper = DbMapperFactory::make('lock|entity');
        $mapper->setArrays($request->getData());

        /** @var \Tasker\DataGateway\Db\Entity\Lock $lock */
        $dbLockCollection = $mapper->getMappedSecondEntities();
        if (!isset($dbLockCollection[0])) {
            throw new EmptyArray('Array must be populated with one db entity', __FILE__, __LINE__);
        }
        /** @var ActiveRecordInterface $dbLock */
        $dbLock = $dbLockCollection[0];
        $dbLock->save();
        $mapper->setArrays($dbLock->toArray());

        $this->setEntitiesFromResponse($mapper->getMappedFirstEntities());
    }

    /**
     * Retrieve lock from db
     *
     * @param Request  $request  Request object used in the search profile api
     *
     * @return array Collection of entity objects or only empty array
     */
    public function retrieve(Request $request)
    {
        /** @var \Tasker\DataGateway\Db\Mapper\Lock\Entity $mapper */
        $mapper = DbMapperFactory::make('lock|entity');
        $requestDataArray = $request->getData();
        $mapper->setArrays($requestDataArray);
        $dbAttributesForQuery = $mapper->getMappedSecondAttributes();

        $requestExtraArray = $request->getExtra();

        // Order by
        $orderBy = isset($requestExtraArray[Request::EXTRA_ORDER_BY]) ?
                       $requestExtraArray[Request::EXTRA_ORDER_BY] :
                       [];
        $lockQuery = new LockQuery();

        /** @var \Propel\Runtime\ActiveQuery\ModelCriteria $modelCriteria */
        $modelCriteria = null;
        foreach ($orderBy as $key => $value) {
            $direction = ($value == 'asc') ?
                             ModelCriteria::ASC :
                             ModelCriteria::DESC;
            if ($modelCriteria != null) {
                $modelCriteria = $modelCriteria->orderBy(
                    $mapper->getMappedSecondAttributeByOneAttributeValue($key),
                    $direction
                );
            }
            else {
                $modelCriteria = $lockQuery->orderBy(
                    $mapper->getMappedSecondAttributeByOneAttributeValue($key),
                    $direction
                );
            }
        }

        // Limit
        if (isset($requestExtraArray[Request::EXTRA_LIMIT])) {
            $modelCriteria->limit($requestExtraArray[Request::EXTRA_LIMIT]);
        }

        // Offset
        if (isset($requestExtraArray[Request::EXTRA_OFFSET])) {
            $modelCriteria->offset($requestExtraArray[Request::EXTRA_OFFSET]);
        }

        if ($modelCriteria != null) {
            /** @var \Propel\Runtime\Collection\ObjectCollection $dbEntities */
            $dbEntities = $modelCriteria->findByArray($dbAttributesForQuery);
        }
        else {
            $dbEntities = $lockQuery->findByArray($dbAttributesForQuery);
        }

        $mapper->setArrays($dbEntities->toArray());
        $this->setEntitiesFromResponse($mapper->getMappedFirstEntities());
    }

    /**
     * Count the number of results based on the passed request
     *
     * @param Request  $request  Request object used in this count method
     *
     * @return int
     */
    public function getTotalResultCount(Request $request)
    {
        /** @var \Tasker\DataGateway\Db\Mapper\Lock\Entity $mapper */
        $mapper = DbMapperFactory::make('lock|entity');
        $requestDataArray = $request->getData();
        $mapper->setArrays($requestDataArray);
        $dbAttributesForQuery = $mapper->getMappedSecondAttributes();

        $lockQuery = new LockQuery();
        return $lockQuery->filterByArray($dbAttributesForQuery)->count();
    }

    /**
     * Update the lock passing request object
     *
     * @param Request $request Request that is used for this update method, $request->getDataObject(0) will contain
     *                         query data to fetch the old object, and $request->getDataObject(1) will contain
     *                         the new values
     *
     * @return void
     */
    public function update(Request $request)
    {
        /** @var \Tasker\DataGateway\Db\Mapper\Lock\Entity $mapper */
        $mapper = DbMapperFactory::make('lock|entity');
        $mapper->setArrays($request->getDataByKey(0));

        $dbAttributesForQuery = $mapper->getMappedSecondAttributes();
        $lockQuery = new LockQuery();
        // Get the old entity
        /** @var \Propel\Runtime\Collection\ObjectCollection $dbEntities */
        $dbEntities = $lockQuery->findByArray($dbAttributesForQuery);
        /** @var \Tasker\DataGateway\Db\Entity\Lock $dbEntity */
        $dbEntity = $dbEntities[0];

        // Edit it
        /** @var \Tasker\DataGateway\Db\Mapper\Lock\Entity $mapper */
        $mapper->setArrays($request->getDataByKey(1));
        $dbEntity->fromArray($mapper->getMappedSecondAttributes());

        // Save it
        $dbEntity->save();

        // Map result to mapper
        $mapper->setArrays($dbEntities->toArray());
        $this->setEntitiesFromResponse($mapper->getMappedFirstEntities());
    }

    /**
     * Delete the lock passing request object
     *
     * @param Request $request Request that is used for this delete method, $request->getDataObject(0) will contain
     *                         query data to fetch the old object
     *
     * @return void
     */
    public function delete(Request $request)
    {
        /** @var \Tasker\DataGateway\Db\Mapper\Lock\Entity $mapper */
        $mapper = DbMapperFactory::make('lock|entity');
        $mapper->setArrays($request->getData());

        $dbAttributesForQuery = $mapper->getMappedSecondAttributes();
        $lockQuery = new LockQuery();

        // Get the old entity
        /** @var \Propel\Runtime\Collection\ObjectCollection $dbEntities */
        $dbEntities = $lockQuery->findByArray($dbAttributesForQuery);
        /** @var \Tasker\DataGateway\Db\Entity\Lock $dbEntity */
        $dbEntities->delete();

        // Return empty array that it will indicate a deleted collection to the caller
        $this->setEntitiesFromResponse([]);
    }
}
