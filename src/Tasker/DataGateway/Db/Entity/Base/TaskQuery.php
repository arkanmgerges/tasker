<?php

namespace Tasker\DataGateway\Db\Entity\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Tasker\DataGateway\Db\Entity\Task as ChildTask;
use Tasker\DataGateway\Db\Entity\TaskQuery as ChildTaskQuery;
use Tasker\DataGateway\Db\Entity\Map\TaskTableMap;

/**
 * Base class that represents a query for the 'tasker_task' table.
 *
 *
 *
 * @method     ChildTaskQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildTaskQuery orderByServer($order = Criteria::ASC) Order by the server column
 * @method     ChildTaskQuery orderByStatusId($order = Criteria::ASC) Order by the statusId column
 * @method     ChildTaskQuery orderByTypeId($order = Criteria::ASC) Order by the typeId column
 * @method     ChildTaskQuery orderByCreatingDateTime($order = Criteria::ASC) Order by the creatingDateTime column
 * @method     ChildTaskQuery orderByRepeatingInterval($order = Criteria::ASC) Order by the repeatingInterval column
 * @method     ChildTaskQuery orderByStartingDateTime($order = Criteria::ASC) Order by the startingDateTime column
 * @method     ChildTaskQuery orderByEndingDateTime($order = Criteria::ASC) Order by the endingDateTime column
 * @method     ChildTaskQuery orderByPriority($order = Criteria::ASC) Order by the priority column
 * @method     ChildTaskQuery orderByExternalTypeId($order = Criteria::ASC) Order by the externalTypeId column
 * @method     ChildTaskQuery orderByExternalId($order = Criteria::ASC) Order by the externalId column
 * @method     ChildTaskQuery orderByExternalData($order = Criteria::ASC) Order by the externalData column
 *
 * @method     ChildTaskQuery groupById() Group by the id column
 * @method     ChildTaskQuery groupByServer() Group by the server column
 * @method     ChildTaskQuery groupByStatusId() Group by the statusId column
 * @method     ChildTaskQuery groupByTypeId() Group by the typeId column
 * @method     ChildTaskQuery groupByCreatingDateTime() Group by the creatingDateTime column
 * @method     ChildTaskQuery groupByRepeatingInterval() Group by the repeatingInterval column
 * @method     ChildTaskQuery groupByStartingDateTime() Group by the startingDateTime column
 * @method     ChildTaskQuery groupByEndingDateTime() Group by the endingDateTime column
 * @method     ChildTaskQuery groupByPriority() Group by the priority column
 * @method     ChildTaskQuery groupByExternalTypeId() Group by the externalTypeId column
 * @method     ChildTaskQuery groupByExternalId() Group by the externalId column
 * @method     ChildTaskQuery groupByExternalData() Group by the externalData column
 *
 * @method     ChildTaskQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildTaskQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildTaskQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildTask findOne(ConnectionInterface $con = null) Return the first ChildTask matching the query
 * @method     ChildTask findOneOrCreate(ConnectionInterface $con = null) Return the first ChildTask matching the query, or a new ChildTask object populated from the query conditions when no match is found
 *
 * @method     ChildTask findOneById(int $id) Return the first ChildTask filtered by the id column
 * @method     ChildTask findOneByServer(string $server) Return the first ChildTask filtered by the server column
 * @method     ChildTask findOneByStatusId(int $statusId) Return the first ChildTask filtered by the statusId column
 * @method     ChildTask findOneByTypeId(int $typeId) Return the first ChildTask filtered by the typeId column
 * @method     ChildTask findOneByCreatingDateTime(string $creatingDateTime) Return the first ChildTask filtered by the creatingDateTime column
 * @method     ChildTask findOneByRepeatingInterval(int $repeatingInterval) Return the first ChildTask filtered by the repeatingInterval column
 * @method     ChildTask findOneByStartingDateTime(string $startingDateTime) Return the first ChildTask filtered by the startingDateTime column
 * @method     ChildTask findOneByEndingDateTime(string $endingDateTime) Return the first ChildTask filtered by the endingDateTime column
 * @method     ChildTask findOneByPriority(int $priority) Return the first ChildTask filtered by the priority column
 * @method     ChildTask findOneByExternalTypeId(int $externalTypeId) Return the first ChildTask filtered by the externalTypeId column
 * @method     ChildTask findOneByExternalId(int $externalId) Return the first ChildTask filtered by the externalId column
 * @method     ChildTask findOneByExternalData(string $externalData) Return the first ChildTask filtered by the externalData column
 *
 * @method     ChildTask[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildTask objects based on current ModelCriteria
 * @method     ChildTask[]|ObjectCollection findById(int $id) Return ChildTask objects filtered by the id column
 * @method     ChildTask[]|ObjectCollection findByServer(string $server) Return ChildTask objects filtered by the server column
 * @method     ChildTask[]|ObjectCollection findByStatusId(int $statusId) Return ChildTask objects filtered by the statusId column
 * @method     ChildTask[]|ObjectCollection findByTypeId(int $typeId) Return ChildTask objects filtered by the typeId column
 * @method     ChildTask[]|ObjectCollection findByCreatingDateTime(string $creatingDateTime) Return ChildTask objects filtered by the creatingDateTime column
 * @method     ChildTask[]|ObjectCollection findByRepeatingInterval(int $repeatingInterval) Return ChildTask objects filtered by the repeatingInterval column
 * @method     ChildTask[]|ObjectCollection findByStartingDateTime(string $startingDateTime) Return ChildTask objects filtered by the startingDateTime column
 * @method     ChildTask[]|ObjectCollection findByEndingDateTime(string $endingDateTime) Return ChildTask objects filtered by the endingDateTime column
 * @method     ChildTask[]|ObjectCollection findByPriority(int $priority) Return ChildTask objects filtered by the priority column
 * @method     ChildTask[]|ObjectCollection findByExternalTypeId(int $externalTypeId) Return ChildTask objects filtered by the externalTypeId column
 * @method     ChildTask[]|ObjectCollection findByExternalId(int $externalId) Return ChildTask objects filtered by the externalId column
 * @method     ChildTask[]|ObjectCollection findByExternalData(string $externalData) Return ChildTask objects filtered by the externalData column
 * @method     ChildTask[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class TaskQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Tasker\DataGateway\Db\Entity\Base\TaskQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Tasker\\DataGateway\\Db\\Entity\\Task', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildTaskQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildTaskQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildTaskQuery) {
            return $criteria;
        }
        $query = new ChildTaskQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildTask|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TaskTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TaskTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildTask A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT `ID`, `SERVER`, `STATUSID`, `TYPEID`, `CREATINGDATETIME`, `REPEATINGINTERVAL`, `STARTINGDATETIME`, `ENDINGDATETIME`, `PRIORITY`, `EXTERNALTYPEID`, `EXTERNALID`, `EXTERNALDATA` FROM `tasker_task` WHERE `ID` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildTask $obj */
            $obj = new ChildTask();
            $obj->hydrate($row);
            TaskTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildTask|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TaskTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TaskTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the server column
     *
     * Example usage:
     * <code>
     * $query->filterByServer('fooValue');   // WHERE server = 'fooValue'
     * $query->filterByServer('%fooValue%'); // WHERE server LIKE '%fooValue%'
     * </code>
     *
     * @param     string $server The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByServer($server = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($server)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $server)) {
                $server = str_replace('*', '%', $server);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_SERVER, $server, $comparison);
    }

    /**
     * Filter the query on the statusId column
     *
     * Example usage:
     * <code>
     * $query->filterByStatusId(1234); // WHERE statusId = 1234
     * $query->filterByStatusId(array(12, 34)); // WHERE statusId IN (12, 34)
     * $query->filterByStatusId(array('min' => 12)); // WHERE statusId > 12
     * </code>
     *
     * @param     mixed $statusId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByStatusId($statusId = null, $comparison = null)
    {
        if (is_array($statusId)) {
            $useMinMax = false;
            if (isset($statusId['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_STATUSID, $statusId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($statusId['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_STATUSID, $statusId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_STATUSID, $statusId, $comparison);
    }

    /**
     * Filter the query on the typeId column
     *
     * Example usage:
     * <code>
     * $query->filterByTypeId(1234); // WHERE typeId = 1234
     * $query->filterByTypeId(array(12, 34)); // WHERE typeId IN (12, 34)
     * $query->filterByTypeId(array('min' => 12)); // WHERE typeId > 12
     * </code>
     *
     * @param     mixed $typeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByTypeId($typeId = null, $comparison = null)
    {
        if (is_array($typeId)) {
            $useMinMax = false;
            if (isset($typeId['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_TYPEID, $typeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($typeId['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_TYPEID, $typeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_TYPEID, $typeId, $comparison);
    }

    /**
     * Filter the query on the creatingDateTime column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatingDateTime('2011-03-14'); // WHERE creatingDateTime = '2011-03-14'
     * $query->filterByCreatingDateTime('now'); // WHERE creatingDateTime = '2011-03-14'
     * $query->filterByCreatingDateTime(array('max' => 'yesterday')); // WHERE creatingDateTime > '2011-03-13'
     * </code>
     *
     * @param     mixed $creatingDateTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByCreatingDateTime($creatingDateTime = null, $comparison = null)
    {
        if (is_array($creatingDateTime)) {
            $useMinMax = false;
            if (isset($creatingDateTime['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_CREATINGDATETIME, $creatingDateTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($creatingDateTime['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_CREATINGDATETIME, $creatingDateTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_CREATINGDATETIME, $creatingDateTime, $comparison);
    }

    /**
     * Filter the query on the repeatingInterval column
     *
     * Example usage:
     * <code>
     * $query->filterByRepeatingInterval(1234); // WHERE repeatingInterval = 1234
     * $query->filterByRepeatingInterval(array(12, 34)); // WHERE repeatingInterval IN (12, 34)
     * $query->filterByRepeatingInterval(array('min' => 12)); // WHERE repeatingInterval > 12
     * </code>
     *
     * @param     mixed $repeatingInterval The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByRepeatingInterval($repeatingInterval = null, $comparison = null)
    {
        if (is_array($repeatingInterval)) {
            $useMinMax = false;
            if (isset($repeatingInterval['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_REPEATINGINTERVAL, $repeatingInterval['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($repeatingInterval['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_REPEATINGINTERVAL, $repeatingInterval['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_REPEATINGINTERVAL, $repeatingInterval, $comparison);
    }

    /**
     * Filter the query on the startingDateTime column
     *
     * Example usage:
     * <code>
     * $query->filterByStartingDateTime('2011-03-14'); // WHERE startingDateTime = '2011-03-14'
     * $query->filterByStartingDateTime('now'); // WHERE startingDateTime = '2011-03-14'
     * $query->filterByStartingDateTime(array('max' => 'yesterday')); // WHERE startingDateTime > '2011-03-13'
     * </code>
     *
     * @param     mixed $startingDateTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByStartingDateTime($startingDateTime = null, $comparison = null)
    {
        if (is_array($startingDateTime)) {
            $useMinMax = false;
            if (isset($startingDateTime['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_STARTINGDATETIME, $startingDateTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($startingDateTime['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_STARTINGDATETIME, $startingDateTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_STARTINGDATETIME, $startingDateTime, $comparison);
    }

    /**
     * Filter the query on the endingDateTime column
     *
     * Example usage:
     * <code>
     * $query->filterByEndingDateTime('2011-03-14'); // WHERE endingDateTime = '2011-03-14'
     * $query->filterByEndingDateTime('now'); // WHERE endingDateTime = '2011-03-14'
     * $query->filterByEndingDateTime(array('max' => 'yesterday')); // WHERE endingDateTime > '2011-03-13'
     * </code>
     *
     * @param     mixed $endingDateTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByEndingDateTime($endingDateTime = null, $comparison = null)
    {
        if (is_array($endingDateTime)) {
            $useMinMax = false;
            if (isset($endingDateTime['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_ENDINGDATETIME, $endingDateTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($endingDateTime['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_ENDINGDATETIME, $endingDateTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_ENDINGDATETIME, $endingDateTime, $comparison);
    }

    /**
     * Filter the query on the priority column
     *
     * Example usage:
     * <code>
     * $query->filterByPriority(1234); // WHERE priority = 1234
     * $query->filterByPriority(array(12, 34)); // WHERE priority IN (12, 34)
     * $query->filterByPriority(array('min' => 12)); // WHERE priority > 12
     * </code>
     *
     * @param     mixed $priority The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByPriority($priority = null, $comparison = null)
    {
        if (is_array($priority)) {
            $useMinMax = false;
            if (isset($priority['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_PRIORITY, $priority['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($priority['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_PRIORITY, $priority['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_PRIORITY, $priority, $comparison);
    }

    /**
     * Filter the query on the externalTypeId column
     *
     * Example usage:
     * <code>
     * $query->filterByExternalTypeId(1234); // WHERE externalTypeId = 1234
     * $query->filterByExternalTypeId(array(12, 34)); // WHERE externalTypeId IN (12, 34)
     * $query->filterByExternalTypeId(array('min' => 12)); // WHERE externalTypeId > 12
     * </code>
     *
     * @param     mixed $externalTypeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByExternalTypeId($externalTypeId = null, $comparison = null)
    {
        if (is_array($externalTypeId)) {
            $useMinMax = false;
            if (isset($externalTypeId['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_EXTERNALTYPEID, $externalTypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($externalTypeId['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_EXTERNALTYPEID, $externalTypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_EXTERNALTYPEID, $externalTypeId, $comparison);
    }

    /**
     * Filter the query on the externalId column
     *
     * Example usage:
     * <code>
     * $query->filterByExternalId(1234); // WHERE externalId = 1234
     * $query->filterByExternalId(array(12, 34)); // WHERE externalId IN (12, 34)
     * $query->filterByExternalId(array('min' => 12)); // WHERE externalId > 12
     * </code>
     *
     * @param     mixed $externalId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByExternalId($externalId = null, $comparison = null)
    {
        if (is_array($externalId)) {
            $useMinMax = false;
            if (isset($externalId['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_EXTERNALID, $externalId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($externalId['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_EXTERNALID, $externalId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_EXTERNALID, $externalId, $comparison);
    }

    /**
     * Filter the query on the externalData column
     *
     * Example usage:
     * <code>
     * $query->filterByExternalData('fooValue');   // WHERE externalData = 'fooValue'
     * $query->filterByExternalData('%fooValue%'); // WHERE externalData LIKE '%fooValue%'
     * </code>
     *
     * @param     string $externalData The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByExternalData($externalData = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($externalData)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $externalData)) {
                $externalData = str_replace('*', '%', $externalData);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_EXTERNALDATA, $externalData, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildTask $task Object to remove from the list of results
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function prune($task = null)
    {
        if ($task) {
            $this->addUsingAlias(TaskTableMap::COL_ID, $task->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the tasker_task table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            TaskTableMap::clearInstancePool();
            TaskTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(TaskTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            TaskTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            TaskTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // TaskQuery
