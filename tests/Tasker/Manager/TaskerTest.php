<?php
namespace Test\Tasker\Manager;

use Tasker\Manager\Tasker as TaskerManager;
use Test\Tasker\BaseClass;
use Test\Tasker\Misc\ActCallback;
use Test\Tasker\Misc\ArrangeCallback;

class TaskerTest extends BaseClass
{
    public function testTaskerManager()
    {
        $this->cleanAndPopulateDatabase();

        $arrangeCallback = new ArrangeCallback();
        $actCallback     = new ActCallback();

        $taskerManager = new TaskerManager();
        $taskerManager->registerArrangeCallback($arrangeCallback);
        $taskerManager->registerActCallback($actCallback, 2);
        $taskerManager->setConfigPath(__DIR__ . '/../Misc/config.php');
        $taskerManager->setEnvironmentVariable('APP_ENV');
        $taskerManager->setMaxProcesses(30);
        $taskerManager->run();
    }
}
