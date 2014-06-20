<?php
namespace Test\Tasker\Manager;

use Test\Tasker\BaseClass;
use Tasker\Manager\Assign as AssignManager;

class AssignTest extends BaseClass
{
    public function tearDown()
    {}

    public function testAssignManager()
    {
        //$this->cleanAndPopulateDatabase();

        $manager = new AssignManager();
        $manager->setMaxProcesses(30);
        $manager->setConfigPath(__DIR__ . '/../Misc/config.php');
        $manager->setEnvironmentVariable('APP_ENV');
        $manager->run();
    }
}
