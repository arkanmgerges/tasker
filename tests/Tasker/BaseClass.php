<?php
namespace Test\Tasker;

use Test\Tasker\DataGateway\Db\Tool\Helper;
use Propel\Runtime\Propel;
use Tasker\Boundary\Request;

class BaseClass extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Set environment variable to test
        // putenv('APP_ENV=development');
        date_default_timezone_set("UTC");
    }

    protected function cleanAndPopulateDatabase()
    {
        $con = $this->setupAndGetPropelConnection();
        $this->dropAndCreateTables($con);
    }

    /**
     * Set up propel connection
     *
     * @return \Propel\Runtime\Connection\ConnectionInterface
     */
    protected function setupAndGetPropelConnection()
    {
        $config = Helper::getConfig();
        Helper::setupPropel();

        $con = Propel::getWriteConnection($config['database']['connectionName']);
        return $con;
    }

    protected function dropAndCreateTables($con)
    {
        $sql = file_get_contents(__DIR__ . '/DataGateway/Db/Tool/Fixture/animator.sql');
        $stmt = $con->prepare($sql);
        $stmt->execute();
    }

    /**
     * Init request object
     *
     * @param array  $params  Array of parameters for request object
     *
     * @return Request
     */
    protected function prepareRequestObject($params)
    {
        return new Request($params);
    }
}
