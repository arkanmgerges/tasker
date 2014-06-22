<?php
namespace Test\Tasker\Manager;

use Test\Tasker\BaseClass;
use Tasker\Manager\Act as ActManager;
use Test\Tasker\Misc\ActCallback;

class ActTest extends BaseClass
{
    public function tearDown()
    {}

    public function testActManager()
    {
        //$this->cleanAndPopulateDatabase();

        $myObject = new ActCallback();

        $manager = new ActManager();
        $manager->setMaxProcesses(30);
        $manager->setConfigPath(__DIR__ . '/../Misc/config.php');
        $manager->setEnvironmentVariable('APP_ENV');
        $externalTypeId = 2;
        $manager->registerCallbackObject($myObject, $externalTypeId);
        $manager->run();
    }
}
