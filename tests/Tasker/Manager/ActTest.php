<?php
namespace Test\Tasker\Manager;

use Test\Tasker\BaseClass;
use Tasker\Manager\Act as ActManager;
use Test\Tasker\Misc\ActCallback;

class ActTest extends BaseClass
{
    public function tearDown()
    {}

    public function testAssignManager()
    {
        //$this->cleanAndPopulateDatabase();

        $myObject = new ActCallback();

        $manager = new ActManager();
        $manager->setMaxProcesses(2);
        $manager->setConfigPath(__DIR__ . '/../Misc/config.php');
        $manager->setEnvironmentVariable('APP_ENV');
        $manager->registerCallbackObject($myObject);
        $manager->run();
    }
}
