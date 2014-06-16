<?php

namespace Tasker\DataGateway\Db\Entity\Base;

use \DateTime;
use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;
use Tasker\DataGateway\Db\Entity\TaskQuery as ChildTaskQuery;
use Tasker\DataGateway\Db\Entity\Map\TaskTableMap;

abstract class Task implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Tasker\\DataGateway\\Db\\Entity\\Map\\TaskTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the server field.
     * @var        string
     */
    protected $server;

    /**
     * The value for the statusid field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $statusid;

    /**
     * The value for the typeid field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $typeid;

    /**
     * The value for the creatingdatetime field.
     * @var        \DateTime
     */
    protected $creatingdatetime;

    /**
     * The value for the repeatinginterval field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $repeatinginterval;

    /**
     * The value for the startingdatetime field.
     * @var        \DateTime
     */
    protected $startingdatetime;

    /**
     * The value for the endingdatetime field.
     * @var        \DateTime
     */
    protected $endingdatetime;

    /**
     * The value for the priority field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $priority;

    /**
     * The value for the externaltypeid field.
     * @var        int
     */
    protected $externaltypeid;

    /**
     * The value for the externalid field.
     * @var        int
     */
    protected $externalid;

    /**
     * The value for the externaldata field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $externaldata;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->statusid = 0;
        $this->typeid = 1;
        $this->repeatinginterval = 0;
        $this->priority = 0;
        $this->externaldata = '';
    }

    /**
     * Initializes internal state of Tasker\DataGateway\Db\Entity\Base\Task object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Task</code> instance.  If
     * <code>obj</code> is an instance of <code>Task</code>, delegates to
     * <code>equals(Task)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|Task The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [server] column value.
     *
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Get the [statusid] column value.
     *
     * @return int
     */
    public function getStatusId()
    {
        return $this->statusid;
    }

    /**
     * Get the [typeid] column value.
     *
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeid;
    }

    /**
     * Get the [optionally formatted] temporal [creatingdatetime] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return string|\DateTime Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatingDateTime($format = NULL)
    {
        if ($format === null) {
            return $this->creatingdatetime;
        } else {
            return $this->creatingdatetime instanceof \DateTime ? $this->creatingdatetime->format($format) : null;
        }
    }

    /**
     * Get the [repeatinginterval] column value.
     *
     * @return int
     */
    public function getRepeatingInterval()
    {
        return $this->repeatinginterval;
    }

    /**
     * Get the [optionally formatted] temporal [startingdatetime] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return string|\DateTime Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getStartingDateTime($format = NULL)
    {
        if ($format === null) {
            return $this->startingdatetime;
        } else {
            return $this->startingdatetime instanceof \DateTime ? $this->startingdatetime->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [endingdatetime] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return string|\DateTime Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getEndingDateTime($format = NULL)
    {
        if ($format === null) {
            return $this->endingdatetime;
        } else {
            return $this->endingdatetime instanceof \DateTime ? $this->endingdatetime->format($format) : null;
        }
    }

    /**
     * Get the [priority] column value.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get the [externaltypeid] column value.
     *
     * @return int
     */
    public function getExternalTypeId()
    {
        return $this->externaltypeid;
    }

    /**
     * Get the [externalid] column value.
     *
     * @return int
     */
    public function getExternalId()
    {
        return $this->externalid;
    }

    /**
     * Get the [externaldata] column value.
     *
     * @return string
     */
    public function getExternalData()
    {
        return $this->externaldata;
    }

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->statusid !== 0) {
                return false;
            }

            if ($this->typeid !== 1) {
                return false;
            }

            if ($this->repeatinginterval !== 0) {
                return false;
            }

            if ($this->priority !== 0) {
                return false;
            }

            if ($this->externaldata !== '') {
                return false;
            }

        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : TaskTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : TaskTableMap::translateFieldName('Server', TableMap::TYPE_PHPNAME, $indexType)];
            $this->server = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : TaskTableMap::translateFieldName('StatusId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->statusid = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : TaskTableMap::translateFieldName('TypeId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->typeid = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : TaskTableMap::translateFieldName('CreatingDateTime', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->creatingdatetime = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : TaskTableMap::translateFieldName('RepeatingInterval', TableMap::TYPE_PHPNAME, $indexType)];
            $this->repeatinginterval = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : TaskTableMap::translateFieldName('StartingDateTime', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->startingdatetime = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : TaskTableMap::translateFieldName('EndingDateTime', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->endingdatetime = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : TaskTableMap::translateFieldName('Priority', TableMap::TYPE_PHPNAME, $indexType)];
            $this->priority = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : TaskTableMap::translateFieldName('ExternalTypeId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->externaltypeid = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : TaskTableMap::translateFieldName('ExternalId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->externalid = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : TaskTableMap::translateFieldName('ExternalData', TableMap::TYPE_PHPNAME, $indexType)];
            $this->externaldata = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 12; // 12 = TaskTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Tasker\\DataGateway\\Db\\Entity\\Task'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
    } // ensureConsistency

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[TaskTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [server] column.
     *
     * @param  string $v new value
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setServer($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->server !== $v) {
            $this->server = $v;
            $this->modifiedColumns[TaskTableMap::COL_SERVER] = true;
        }

        return $this;
    } // setServer()

    /**
     * Set the value of [statusid] column.
     *
     * @param  int $v new value
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setStatusId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->statusid !== $v) {
            $this->statusid = $v;
            $this->modifiedColumns[TaskTableMap::COL_STATUSID] = true;
        }

        return $this;
    } // setStatusId()

    /**
     * Set the value of [typeid] column.
     *
     * @param  int $v new value
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setTypeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->typeid !== $v) {
            $this->typeid = $v;
            $this->modifiedColumns[TaskTableMap::COL_TYPEID] = true;
        }

        return $this;
    } // setTypeId()

    /**
     * Sets the value of [creatingdatetime] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setCreatingDateTime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->creatingdatetime !== null || $dt !== null) {
            if ($dt !== $this->creatingdatetime) {
                $this->creatingdatetime = $dt;
                $this->modifiedColumns[TaskTableMap::COL_CREATINGDATETIME] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatingDateTime()

    /**
     * Set the value of [repeatinginterval] column.
     *
     * @param  int $v new value
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setRepeatingInterval($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->repeatinginterval !== $v) {
            $this->repeatinginterval = $v;
            $this->modifiedColumns[TaskTableMap::COL_REPEATINGINTERVAL] = true;
        }

        return $this;
    } // setRepeatingInterval()

    /**
     * Sets the value of [startingdatetime] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setStartingDateTime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->startingdatetime !== null || $dt !== null) {
            if ($dt !== $this->startingdatetime) {
                $this->startingdatetime = $dt;
                $this->modifiedColumns[TaskTableMap::COL_STARTINGDATETIME] = true;
            }
        } // if either are not null

        return $this;
    } // setStartingDateTime()

    /**
     * Sets the value of [endingdatetime] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setEndingDateTime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->endingdatetime !== null || $dt !== null) {
            if ($dt !== $this->endingdatetime) {
                $this->endingdatetime = $dt;
                $this->modifiedColumns[TaskTableMap::COL_ENDINGDATETIME] = true;
            }
        } // if either are not null

        return $this;
    } // setEndingDateTime()

    /**
     * Set the value of [priority] column.
     *
     * @param  int $v new value
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setPriority($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->priority !== $v) {
            $this->priority = $v;
            $this->modifiedColumns[TaskTableMap::COL_PRIORITY] = true;
        }

        return $this;
    } // setPriority()

    /**
     * Set the value of [externaltypeid] column.
     *
     * @param  int $v new value
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setExternalTypeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->externaltypeid !== $v) {
            $this->externaltypeid = $v;
            $this->modifiedColumns[TaskTableMap::COL_EXTERNALTYPEID] = true;
        }

        return $this;
    } // setExternalTypeId()

    /**
     * Set the value of [externalid] column.
     *
     * @param  int $v new value
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setExternalId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->externalid !== $v) {
            $this->externalid = $v;
            $this->modifiedColumns[TaskTableMap::COL_EXTERNALID] = true;
        }

        return $this;
    } // setExternalId()

    /**
     * Set the value of [externaldata] column.
     *
     * @param  string $v new value
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object (for fluent API support)
     */
    public function setExternalData($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->externaldata !== $v) {
            $this->externaldata = $v;
            $this->modifiedColumns[TaskTableMap::COL_EXTERNALDATA] = true;
        }

        return $this;
    } // setExternalData()

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TaskTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildTaskQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Task::setDeleted()
     * @see Task::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildTaskQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $isInsert = $this->isNew();
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                TaskTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[TaskTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . TaskTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(TaskTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = '`ID`';
        }
        if ($this->isColumnModified(TaskTableMap::COL_SERVER)) {
            $modifiedColumns[':p' . $index++]  = '`SERVER`';
        }
        if ($this->isColumnModified(TaskTableMap::COL_STATUSID)) {
            $modifiedColumns[':p' . $index++]  = '`STATUSID`';
        }
        if ($this->isColumnModified(TaskTableMap::COL_TYPEID)) {
            $modifiedColumns[':p' . $index++]  = '`TYPEID`';
        }
        if ($this->isColumnModified(TaskTableMap::COL_CREATINGDATETIME)) {
            $modifiedColumns[':p' . $index++]  = '`CREATINGDATETIME`';
        }
        if ($this->isColumnModified(TaskTableMap::COL_REPEATINGINTERVAL)) {
            $modifiedColumns[':p' . $index++]  = '`REPEATINGINTERVAL`';
        }
        if ($this->isColumnModified(TaskTableMap::COL_STARTINGDATETIME)) {
            $modifiedColumns[':p' . $index++]  = '`STARTINGDATETIME`';
        }
        if ($this->isColumnModified(TaskTableMap::COL_ENDINGDATETIME)) {
            $modifiedColumns[':p' . $index++]  = '`ENDINGDATETIME`';
        }
        if ($this->isColumnModified(TaskTableMap::COL_PRIORITY)) {
            $modifiedColumns[':p' . $index++]  = '`PRIORITY`';
        }
        if ($this->isColumnModified(TaskTableMap::COL_EXTERNALTYPEID)) {
            $modifiedColumns[':p' . $index++]  = '`EXTERNALTYPEID`';
        }
        if ($this->isColumnModified(TaskTableMap::COL_EXTERNALID)) {
            $modifiedColumns[':p' . $index++]  = '`EXTERNALID`';
        }
        if ($this->isColumnModified(TaskTableMap::COL_EXTERNALDATA)) {
            $modifiedColumns[':p' . $index++]  = '`EXTERNALDATA`';
        }

        $sql = sprintf(
            'INSERT INTO `tasker_task` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`ID`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`SERVER`':
                        $stmt->bindValue($identifier, $this->server, PDO::PARAM_STR);
                        break;
                    case '`STATUSID`':
                        $stmt->bindValue($identifier, $this->statusid, PDO::PARAM_INT);
                        break;
                    case '`TYPEID`':
                        $stmt->bindValue($identifier, $this->typeid, PDO::PARAM_INT);
                        break;
                    case '`CREATINGDATETIME`':
                        $stmt->bindValue($identifier, $this->creatingdatetime ? $this->creatingdatetime->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case '`REPEATINGINTERVAL`':
                        $stmt->bindValue($identifier, $this->repeatinginterval, PDO::PARAM_INT);
                        break;
                    case '`STARTINGDATETIME`':
                        $stmt->bindValue($identifier, $this->startingdatetime ? $this->startingdatetime->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case '`ENDINGDATETIME`':
                        $stmt->bindValue($identifier, $this->endingdatetime ? $this->endingdatetime->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case '`PRIORITY`':
                        $stmt->bindValue($identifier, $this->priority, PDO::PARAM_INT);
                        break;
                    case '`EXTERNALTYPEID`':
                        $stmt->bindValue($identifier, $this->externaltypeid, PDO::PARAM_INT);
                        break;
                    case '`EXTERNALID`':
                        $stmt->bindValue($identifier, $this->externalid, PDO::PARAM_INT);
                        break;
                    case '`EXTERNALDATA`':
                        $stmt->bindValue($identifier, $this->externaldata, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = TaskTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getServer();
                break;
            case 2:
                return $this->getStatusId();
                break;
            case 3:
                return $this->getTypeId();
                break;
            case 4:
                return $this->getCreatingDateTime();
                break;
            case 5:
                return $this->getRepeatingInterval();
                break;
            case 6:
                return $this->getStartingDateTime();
                break;
            case 7:
                return $this->getEndingDateTime();
                break;
            case 8:
                return $this->getPriority();
                break;
            case 9:
                return $this->getExternalTypeId();
                break;
            case 10:
                return $this->getExternalId();
                break;
            case 11:
                return $this->getExternalData();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array())
    {
        if (isset($alreadyDumpedObjects['Task'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Task'][$this->getPrimaryKey()] = true;
        $keys = TaskTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getServer(),
            $keys[2] => $this->getStatusId(),
            $keys[3] => $this->getTypeId(),
            $keys[4] => $this->getCreatingDateTime(),
            $keys[5] => $this->getRepeatingInterval(),
            $keys[6] => $this->getStartingDateTime(),
            $keys[7] => $this->getEndingDateTime(),
            $keys[8] => $this->getPriority(),
            $keys[9] => $this->getExternalTypeId(),
            $keys[10] => $this->getExternalId(),
            $keys[11] => $this->getExternalData(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }


        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\Tasker\DataGateway\Db\Entity\Task
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = TaskTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Tasker\DataGateway\Db\Entity\Task
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setServer($value);
                break;
            case 2:
                $this->setStatusId($value);
                break;
            case 3:
                $this->setTypeId($value);
                break;
            case 4:
                $this->setCreatingDateTime($value);
                break;
            case 5:
                $this->setRepeatingInterval($value);
                break;
            case 6:
                $this->setStartingDateTime($value);
                break;
            case 7:
                $this->setEndingDateTime($value);
                break;
            case 8:
                $this->setPriority($value);
                break;
            case 9:
                $this->setExternalTypeId($value);
                break;
            case 10:
                $this->setExternalId($value);
                break;
            case 11:
                $this->setExternalData($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = TaskTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setServer($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setStatusId($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setTypeId($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setCreatingDateTime($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setRepeatingInterval($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setStartingDateTime($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setEndingDateTime($arr[$keys[7]]);
        }
        if (array_key_exists($keys[8], $arr)) {
            $this->setPriority($arr[$keys[8]]);
        }
        if (array_key_exists($keys[9], $arr)) {
            $this->setExternalTypeId($arr[$keys[9]]);
        }
        if (array_key_exists($keys[10], $arr)) {
            $this->setExternalId($arr[$keys[10]]);
        }
        if (array_key_exists($keys[11], $arr)) {
            $this->setExternalData($arr[$keys[11]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return $this|\Tasker\DataGateway\Db\Entity\Task The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(TaskTableMap::DATABASE_NAME);

        if ($this->isColumnModified(TaskTableMap::COL_ID)) {
            $criteria->add(TaskTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(TaskTableMap::COL_SERVER)) {
            $criteria->add(TaskTableMap::COL_SERVER, $this->server);
        }
        if ($this->isColumnModified(TaskTableMap::COL_STATUSID)) {
            $criteria->add(TaskTableMap::COL_STATUSID, $this->statusid);
        }
        if ($this->isColumnModified(TaskTableMap::COL_TYPEID)) {
            $criteria->add(TaskTableMap::COL_TYPEID, $this->typeid);
        }
        if ($this->isColumnModified(TaskTableMap::COL_CREATINGDATETIME)) {
            $criteria->add(TaskTableMap::COL_CREATINGDATETIME, $this->creatingdatetime);
        }
        if ($this->isColumnModified(TaskTableMap::COL_REPEATINGINTERVAL)) {
            $criteria->add(TaskTableMap::COL_REPEATINGINTERVAL, $this->repeatinginterval);
        }
        if ($this->isColumnModified(TaskTableMap::COL_STARTINGDATETIME)) {
            $criteria->add(TaskTableMap::COL_STARTINGDATETIME, $this->startingdatetime);
        }
        if ($this->isColumnModified(TaskTableMap::COL_ENDINGDATETIME)) {
            $criteria->add(TaskTableMap::COL_ENDINGDATETIME, $this->endingdatetime);
        }
        if ($this->isColumnModified(TaskTableMap::COL_PRIORITY)) {
            $criteria->add(TaskTableMap::COL_PRIORITY, $this->priority);
        }
        if ($this->isColumnModified(TaskTableMap::COL_EXTERNALTYPEID)) {
            $criteria->add(TaskTableMap::COL_EXTERNALTYPEID, $this->externaltypeid);
        }
        if ($this->isColumnModified(TaskTableMap::COL_EXTERNALID)) {
            $criteria->add(TaskTableMap::COL_EXTERNALID, $this->externalid);
        }
        if ($this->isColumnModified(TaskTableMap::COL_EXTERNALDATA)) {
            $criteria->add(TaskTableMap::COL_EXTERNALDATA, $this->externaldata);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(TaskTableMap::DATABASE_NAME);
        $criteria->add(TaskTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Tasker\DataGateway\Db\Entity\Task (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setServer($this->getServer());
        $copyObj->setStatusId($this->getStatusId());
        $copyObj->setTypeId($this->getTypeId());
        $copyObj->setCreatingDateTime($this->getCreatingDateTime());
        $copyObj->setRepeatingInterval($this->getRepeatingInterval());
        $copyObj->setStartingDateTime($this->getStartingDateTime());
        $copyObj->setEndingDateTime($this->getEndingDateTime());
        $copyObj->setPriority($this->getPriority());
        $copyObj->setExternalTypeId($this->getExternalTypeId());
        $copyObj->setExternalId($this->getExternalId());
        $copyObj->setExternalData($this->getExternalData());
        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \Tasker\DataGateway\Db\Entity\Task Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        $this->id = null;
        $this->server = null;
        $this->statusid = null;
        $this->typeid = null;
        $this->creatingdatetime = null;
        $this->repeatinginterval = null;
        $this->startingdatetime = null;
        $this->endingdatetime = null;
        $this->priority = null;
        $this->externaltypeid = null;
        $this->externalid = null;
        $this->externaldata = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
        } // if ($deep)

    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(TaskTableMap::DEFAULT_STRING_FORMAT);
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
