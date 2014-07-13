<?php
namespace Tasker\Manager;

use Tasker\Boundary\Request;
use Tasker\Manager\Definition\ArrangeCallbackInterface;
use Tasker\Task\Packet\Information;
use Tasker\UseCase\Definition\HelperTrait;
use Tasker\DataGateway\Db\Tool\Helper as HelperTool;
use Tasker\Task\Arrange as ArrangeTask;

class Arrange
{
    use HelperTrait;

    const ID_TYPE                        = 'ARRANGE';
    const ARRANGE_MODE_UNIQUE            = 'ARRANGE_MODE_UNIQUE';
    const ARRANGE_MODE_REPEATABLE        = 'ARRANGE_MODE_REPEATABLE';
    const MAX_RETRY_TIME_BEFORE_CONTINUE = 5;

    private $arrangeMode = self::ARRANGE_MODE_UNIQUE;

    private $maxProcesses     = 1;
    private $configPathString = null;
    private $processMaxRetryTimeBeforeContinue = 1;


    /** @var ArrangeCallbackInterface $callback */
    private $callback = null;

    public function setArrangeMode($arrangeMode)
    {
        if ($arrangeMode == self::ARRANGE_MODE_UNIQUE || $arrangeMode == self::ARRANGE_MODE_REPEATABLE) {
            $this->arrangeMode = $arrangeMode;
        }
    }

    public function getArrangeMode()
    {
        return $this->arrangeMode;
    }

    public function setMaxProcesses($maxProcesses)
    {
        $this->maxProcesses = $maxProcesses;
    }

    public function setConfigPath($configPathString = null)
    {
        $this->configPathString = $configPathString;
        HelperTool::setConfigPath($configPathString);
    }

    public function setEnvironmentVariable($environmentVariable = 'APP_ENV')
    {
        HelperTool::setEnvironmentVariable($environmentVariable);
    }

    public function registerCallbackObject(ArrangeCallbackInterface $callback)
    {
        $this->callback = $callback;
    }

    public function run()
    {
        $config = HelperTool::getConfig();
        $this->processMaxRetryTimeBeforeContinue = isset($config['process']['maxRetryTimeBeforeContinue']) ?
            $config['process']['maxRetryTimeBeforeContinue'] :
            ((int) ($this->maxProcesses * 0.03)) + self::MAX_RETRY_TIME_BEFORE_CONTINUE;

        $currentRunningProcessesCount = (int) $this->getCurrentRunningProcesses();
        $numberOfThreadsToCreate = $this->maxProcesses - $currentRunningProcessesCount;
        for ($i = 0; $i < $numberOfThreadsToCreate; $i++) {
            switch ($pid = pcntl_fork()) {
                case -1:
                    continue;
                    break;
                case 0:
                    $this->doJob();
                    // Exit the current child
                    exit;
                    break;

                default:
                    // Parent do nothing, just create another child
                    break;
            }
        }
    }

    private function getCurrentRunningProcesses()
    {
        $request = new Request(['server' => gethostname(), 'extra' => self::ID_TYPE]);
        $this->executeUseCase('process|retrieve', $request);
        return $this->getUseCaseTotalResultCount();
    }

    private function doJob()
    {
        date_default_timezone_set('UTC');
        $info = new Information();
        // Save process to db
        $info->setProcessId(getmypid());
        $info->setHostname(gethostname());
        $info->setExtra(self::ID_TYPE);
        $info->setProcessMaxRetryTimeBeforeContinue($this->processMaxRetryTimeBeforeContinue);
        $info->setArrangeMode($this->getArrangeMode());

        $request = new Request([
            'id'               => $info->getProcessId(),
            'server'           => $info->getHostname(),
            'extra'            => $info->getExtra(),
            'creatingDateTime' => date('Y-m-d H:i:s')
        ]);

        $this->runUseCaseWithNoOfRetriesOnFail('process|create', $request, $this->processMaxRetryTimeBeforeContinue);
        $this->callback->callback(clone($info), new ArrangeTask($info));
        $this->runUseCaseWithNoOfRetriesOnFail('process|delete', $request, $this->processMaxRetryTimeBeforeContinue);
    }
}
