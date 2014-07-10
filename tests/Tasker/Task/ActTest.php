<?php
namespace Test\Tasker\Task;

use Test\Tasker\BaseClass;
use Tasker\Manager\Act as ActManager;
use Test\Tasker\Misc\ActCallback;

use Tasker\Boundary\Request;
use Tasker\Task\Packet\Information;
use Tasker\UseCase\Definition\HelperTrait;
use Tasker\Task\Act as ActTask;

class ActTest extends BaseClass
{
    use HelperTrait;

    public function tearDown()
    {}

    public function testActTask()
    {
        //$this->cleanAndPopulateDatabase();

        $myObject = new ActCallback();
        $info = new Information();
        // Save process to db
        $info->setProcessId(1234);
        $info->setHostname('anim.stage.lsex.dev');
        $info->setExtra(1);
        $info->setProcessMaxRetryTimeBeforeContinue(3);

        $request = new Request([
            'id'               => $info->getProcessId(),
            'server'           => $info->getHostname(),
            'extra'            => $info->getExtra(),
            'creatingDateTime' => date('Y-m-d H:i:s')
        ]);

        $this->runUseCaseWithNoOfRetriesOnFail('process|create', $request, 3);
        $actTask = new ActTask($info, $myObject, 2);
        $actTask->execute();
        $this->runUseCaseWithNoOfRetriesOnFail('process|delete', $request, 3);
    }
}
