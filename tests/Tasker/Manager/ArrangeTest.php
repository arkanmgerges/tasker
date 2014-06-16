<?php
namespace Test\Tasker\Manager;

use Tasker\Boundary\Request;
use Tasker\Manager\Arrange;
use Test\Tasker\BaseClass;
use Tasker\Manager\Arrange as ArrangeManager;
use Test\Tasker\Misc\ArrangeCallback;
use Tasker\UseCase\Factory;


class ArrangeTest extends BaseClass
{
    public function tearDown()
    {}

    public function testArrangeManager()
    {
        $this->cleanAndPopulateDatabase();

        $myObject = new ArrangeCallback();

        $manager = new ArrangeManager();
        $manager->setMaxProcesses(7);
        $manager->setConfigPath(__DIR__ . '/../Misc/config.php');
        $manager->setEnvironmentVariable('APP_ENV');
        $manager->setArrangeMode(Arrange::ARRANGE_MODE_REPEATABLE);
        $manager->registerCallbackObject($myObject);
        $manager->run();
    }
}
