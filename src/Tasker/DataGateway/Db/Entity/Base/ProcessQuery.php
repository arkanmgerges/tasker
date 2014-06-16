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
use Tasker\DataGateway\Db\Entity\Process as ChildProcess;
use Tasker\DataGateway\Db\Entity\ProcessQuery as ChildProcessQuery;
use Tasker\DataGateway\Db\Entity\Map\ProcessTableMap;

/**
 * Base class that represents a query for the 'tasker_process' table.
 *
 *
 *
 * @method     ChildProcessQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildProcessQuery orderByServer($order = Criteria::ASC) Order by the server column
 * @method     ChildProcessQuery orderByextra($order = Criteria::ASC) Order by the extra column
 * @method     ChildProcessQuery orderByCreatingDateTime($order = Criteria::ASC) Order by the creatingDateTime column
 *
 * @method     ChildProcessQuery groupById() Group by the id column
 * @method     ChildProcessQuery groupByServer() Group by the server column
 * @method     ChildProcessQuery groupByextra() Group by the extra column
 * @method     ChildProcessQuery groupByCreatingDateTime() Group by the creatingDateTime column
 *
 * @method     ChildProcessQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildProcessQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildProcessQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildProcess findOne(ConnectionInterface $con = null) Return the first ChildProcess matching the query
 * @method     ChildProcess findOneOrCreate(ConnectionInterface $con = null) Return the first ChildProcess matching the query, or a new ChildProcess object populated from the query conditions when no match is found
 *
 * @method     ChildProcess findOneById(int $id) Return the first ChildProcess filtered by the id column
 * @method     ChildProcess findOneByServer(string $server) Return the first ChildProcess filtered by the server column
 * @method     ChildProcess findOneByextra(string $extra) Return the first ChildProcess filtered by the extra column
 * @method     ChildProcess findOneByCreatingDateTime(string $creatingDateTime) Return the first ChildProcess filtered by the creatingDateTime column
 *
 * @method     ChildProcess[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildProcess objects based on current ModelCriteria
 * @method     ChildProcess[]|ObjectCollection findById(int $id) Return ChildProcess objects filtered by the id column
 * @method     ChildProcess[]|ObjectCollection findByServer(string $server) Return ChildProcess objects filtered by the server column
 * @method     ChildProcess[]|ObjectCollection findByextra(string $extra) Return ChildProcess objects filtered by the extra column
 * @method     ChildProcess[]|ObjectCollection findByCreatingDateTime(string $creatingDateTime) Return ChildProcess objects filtered by the creatingDateTime column
 * @method     ChildProcess[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class ProcessQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Tasker\DataGateway\Db\Entity\Base\ProcessQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Tasker\\DataGateway\\Db\\Entity\\Process', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildProcessQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildProcessQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildProcessQuery) {
            return $criteria;
        }
        $query = new ChildProcessQuery();
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
     * @return ChildProcess|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProcessTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProcessTableMap::DATABASE_NAME);
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
     * @return ChildProcess A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT `ID`, `SERVER`, `EXTRA`, `CREATINGDATETIME` FROM `tasker_process` WHERE `ID` = :p0';
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
            /** @var ChildProcess $obj */
            $obj = new ChildProcess();
            $obj->hydrate($row);
            ProcessTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildProcess|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildProcessQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProcessTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildProcessQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProcessTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildProcessQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProcessTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProcessTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProcessTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildProcessQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ProcessTableMap::COL_SERVER, $server, $comparison);
    }

    /**
     * Filter the query on the extra column
     *
     * Example usage:
     * <code>
     * $query->filterByextra('fooValue');   // WHERE extra = 'fooValue'
     * $query->filterByextra('%fooValue%'); // WHERE extra LIKE '%fooValue%'
     * </code>
     *
     * @param     string $extra The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildProcessQuery The current query, for fluid interface
     */
    public function filterByextra($extra = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($extra)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $extra)) {
                $extra = str_replace('*', '%', $extra);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ProcessTableMap::COL_EXTRA, $extra, $comparison);
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
     * @return $this|ChildProcessQuery The current query, for fluid interface
     */
    public function filterByCreatingDateTime($creatingDateTime = null, $comparison = null)
    {
        if (is_array($creatingDateTime)) {
            $useMinMax = false;
            if (isset($creatingDateTime['min'])) {
                $this->addUsingAlias(ProcessTableMap::COL_CREATINGDATETIME, $creatingDateTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($creatingDateTime['max'])) {
                $this->addUsingAlias(ProcessTableMap::COL_CREATINGDATETIME, $creatingDateTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProcessTableMap::COL_CREATINGDATETIME, $creatingDateTime, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildProcess $process Object to remove from the list of results
     *
     * @return $this|ChildProcessQuery The current query, for fluid interface
     */
    public function prune($process = null)
    {
        if ($process) {
            $this->addUsingAlias(ProcessTableMap::COL_ID, $process->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the tasker_process table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProcessTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ProcessTableMap::clearInstancePool();
            ProcessTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(ProcessTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ProcessTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            ProcessTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ProcessTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // ProcessQuery
