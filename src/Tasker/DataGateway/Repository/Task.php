<?php
namespace Tasker\DataGateway\Repository;

use Propel\Runtime\Propel;
use Tasker\Boundary\Request;
use Tasker\DataGateway\Db\Mapper\Factory as DbMapperFactory;
use Tasker\DataGateway\Db\Tool\Helper;
use Tasker\DataGateway\Definition\TaskRepositoryInterface;
use Tasker\DataGateway\Db\Entity\TaskQuery;
use Tasker\DataGateway\Repository\Definition\DbAbstract;
use Tasker\DataGateway\Exception\EmptyArray;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;

/**
 * This class is used to represent task repository data gateway
 *
 * @category DataGateway
 * @package  Tasker\DataGateway
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Task extends DbAbstract implements TaskRepositoryInterface
{
    /**
     * Create a new task into the data gateway
     *
     * @param Request  $request  Request that will contain attributes of the new entity to be created
     *
     * @throws EmptyArray When an empty array is found
     *
     * @return void
     */
    public function create(Request $request)
    {
        /** @var \Tasker\DataGateway\Db\Mapper\Task\Entity $mapper */
        $mapper = DbMapperFactory::make('task|entity');
        $mapper->setArrays($request->getData());

        /** @var \Tasker\DataGateway\Db\Entity\Task $task */
        $dbTaskCollection = $mapper->getMappedSecondEntities();
        if (!isset($dbTaskCollection[0])) {
            throw new EmptyArray('Array must be populated with one db entity', __FILE__, __LINE__);
        }

        /** @var ActiveRecordInterface $dbTask */
        $dbTask = $dbTaskCollection[0];
        $dbTask->save();

        $mapper->setArrays($dbTask->toArray());

        $this->setEntitiesFromResponse($mapper->getMappedFirstEntities());
    }

    /**
     * Retrieve task from db
     *
     * @param Request  $request  Request object used in the search profile api
     *
     * @return void
     */
    public function retrieve(Request $request)
    {
        $requestDataArray = $request->getData();
        if (isset($requestDataArray['sql'])) {
            $this->processQueryAndSetEntities($requestDataArray);
            return;
        }
        /** @var \Tasker\DataGateway\Db\Mapper\Task\Entity $mapper */
        $mapper = DbMapperFactory::make('task|entity');
        $mapper->setArrays($requestDataArray);
        $dbAttributesForQuery = $mapper->getMappedSecondAttributes();

        $requestExtraArray = $request->getExtra();

        // Order by
        $orderBy = isset($requestExtraArray[Request::EXTRA_ORDER_BY]) ?
                       $requestExtraArray[Request::EXTRA_ORDER_BY] :
                       [];
        $taskQuery = new TaskQuery();

        foreach ($orderBy as $key => $value) {
            $direction = ($value == 'asc') ?
                             ModelCriteria::ASC :
                             ModelCriteria::DESC;
            $taskQuery->orderBy(
                $mapper->getMappedSecondAttributeByOneAttributeValue($key),
                $direction
            );
        }

        // Limit
        if (isset($requestExtraArray[Request::EXTRA_LIMIT])) {
            $taskQuery->limit($requestExtraArray[Request::EXTRA_LIMIT]);
        }

        // Offset
        if (isset($requestExtraArray[Request::EXTRA_OFFSET])) {
            $taskQuery->offset($requestExtraArray[Request::EXTRA_OFFSET]);
        }

        $dbEntities = $taskQuery->findByArray($dbAttributesForQuery);

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
        /** @var \Tasker\DataGateway\Db\Mapper\Task\Entity $mapper */
        $mapper = DbMapperFactory::make('task|entity');
        $requestDataArray = $request->getData();
        if (isset($requestDataArray['sql'])) {
            $sql = $requestDataArray['sql']['statementForCount'];
            return $this->getTotalResultCountFromSql($sql);
        }
        $mapper->setArrays($requestDataArray);
        $dbAttributesForQuery = $mapper->getMappedSecondAttributes();

        $taskQuery = new TaskQuery();
        return $taskQuery->filterByArray($dbAttributesForQuery)->count();
    }

    /**
     * Update the task passing request object
     *
     * @param Request $request Request that is used for this update method, $request->getDataObject(0) will contain
     *                         query data to fetch the old object, and $request->getDataObject(1) will contain
     *                         the new values
     *
     * @return void
     */
    public function update(Request $request)
    {
        /** @var \Tasker\DataGateway\Db\Mapper\Task\Entity $mapper */
        $mapper = DbMapperFactory::make('task|entity');
        $mapper->setArrays($request->getDataByKey(0));

        $dbAttributesForQuery = $mapper->getMappedSecondAttributes();
        $taskQuery = new TaskQuery();
        // Get the old entity
        /** @var \Propel\Runtime\Collection\ObjectCollection $dbEntities */
        $dbEntities = $taskQuery->findByArray($dbAttributesForQuery);
        /** @var \Tasker\DataGateway\Db\Entity\Task $dbEntity */
        $dbEntity = $dbEntities[0];

        // Edit it
        /** @var \Tasker\DataGateway\Db\Mapper\Task\Entity $mapper */
        $mapper->setArrays($request->getDataByKey(1));
        $dbEntity->fromArray($mapper->getMappedSecondAttributes());

        // Save it
        $dbEntity->save();

        // Map result to mapper
        $mapper->setArrays($dbEntities->toArray());
        $this->setEntitiesFromResponse($mapper->getMappedFirstEntities());
    }

    /**
     * Delete the task passing request object
     *
     * @param Request $request Request that is used for this delete method, $request->getDataObject(0) will contain
     *                         query data to fetch the old object
     *
     * @return void
     */
    public function delete(Request $request)
    {
        /** @var \Tasker\DataGateway\Db\Mapper\Task\Entity $mapper */
        $mapper = DbMapperFactory::make('task|entity');
        $mapper->setArrays($request->getData());

        $dbAttributesForQuery = $mapper->getMappedSecondAttributes();
        $taskQuery = new TaskQuery();

        // Get the old entity
        /** @var \Propel\Runtime\Collection\ObjectCollection $dbEntities */
        $dbEntities = $taskQuery->findByArray($dbAttributesForQuery);
        /** @var \Tasker\DataGateway\Db\Entity\Task $dbEntity */
        $dbEntities->delete();

        // Return empty array that it will indicate a deleted collection to the caller
        $this->setEntitiesFromResponse([]);
    }

    private function getTotalResultCountFromSql($sql)
    {
        $config = Helper::getConfig();
        $tableReference = $config['repository']['tableReference'];
        $search  = array_keys($tableReference);
        $replace = array_values($tableReference);
        $sql = str_replace($search, $replace, $sql);
        $con = Propel::getReadConnection($config['database']['connectionName']);
        $stmt = $con->query($sql);
        return $stmt->count();
    }

    private function processQueryAndSetEntities($requestDataArray)
    {
        if (isset($requestDataArray['sql'])) {
            $sql = $requestDataArray['sql']['statement'];
            $config = Helper::getConfig();
            $tableReference = $config['repository']['tableReference'];
            $search  = array_keys($tableReference);
            $replace = array_values($tableReference);
            $sql = str_replace($search, $replace, $sql);

            $pdo = new \PDO(
                $config['database']['dsn'],
                $config['database']['username'],
                $config['database']['password']
            );

            $stmt = $pdo->prepare($sql);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $mapper = DbMapperFactory::make('task|pdo|entity');
            $mapper->setArrays($result);
            $this->setEntitiesFromResponse($mapper->getMappedFirstEntities());
        }
    }
}
