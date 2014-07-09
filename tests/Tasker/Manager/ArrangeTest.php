<?php
namespace Test\Tasker\Manager;

use Test\Tasker\BaseClass;
use Tasker\Manager\Arrange as ArrangeManager;
use Test\Tasker\Misc\ArrangeCallbackRandom;
use Test\Tasker\Misc\ArrangeCallbackOverwrite;


class ArrangeTest extends BaseClass
{
    public function tearDown()
    {}

    public function testArrangeManagerRandom()
    {
        $this->cleanAndPopulateDatabase();

        $myObject = new ArrangeCallbackRandom();

        $manager = new ArrangeManager();
        $manager->setMaxProcesses(6);
        $manager->setConfigPath(__DIR__ . '/../Misc/config.php');
        $manager->setEnvironmentVariable('APP_ENV');
//        $manager->setArrangeMode(Arrange::ARRANGE_MODE_REPEATABLE);
        $manager->registerCallbackObject($myObject);
        $manager->run();
    }

    public function testArrangeManagerOverwrite()
    {
        $this->cleanAndPopulateDatabase();

        $myObject = new ArrangeCallbackOverwrite();

        $manager = new ArrangeManager();
        $manager->setMaxProcesses(10);
        $manager->setConfigPath(__DIR__ . '/../Misc/config.php');
        $manager->setEnvironmentVariable('APP_ENV');
//        $manager->setArrangeMode(Arrange::ARRANGE_MODE_REPEATABLE);
        $manager->registerCallbackObject($myObject);
        $manager->run();
    }
}
