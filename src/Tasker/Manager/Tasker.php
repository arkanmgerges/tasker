<?php
namespace Tasker\Manager;

use Tasker\Manager\Definition\ActCallbackInterface;
use Tasker\Manager\Definition\ArrangeCallbackInterface;

class Tasker
{
    /** @var null|\Tasker\Manager\Arrange $arrangeManager */
    private $arrangeManager = null;
    /** @var null|\Tasker\Manager\Assign $assignManager */
    private $assignManager  = null;
    /** @var null|\Tasker\Manager\Act $actManager */
    private $actManager     = null;

    public function __construct()
    {
        $this->arrangeManager = new Arrange();
        $this->assignManager  = new Assign();
        $this->actManager     = new Act();

        $this->setEnvironmentVariable('APP_ENV');
    }

    public function registerArrangeCallback(ArrangeCallbackInterface $callback)
    {
        $this->arrangeManager->registerCallbackObject($callback);
    }

    public function registerActCallback(ActCallbackInterface $callback, $externalTypeId)
    {
        $this->actManager->registerCallbackObject($callback, $externalTypeId);
    }

    public function setConfigPath($configPath)
    {
        $this->arrangeManager->setConfigPath($configPath);
        $this->assignManager->setConfigPath($configPath);
        $this->actManager->setConfigPath($configPath);
    }

    public function setEnvironmentVariable($environmentVariable)
    {
        $this->arrangeManager->setEnvironmentVariable($environmentVariable);
        $this->assignManager->setEnvironmentVariable($environmentVariable);
        $this->actManager->setEnvironmentVariable($environmentVariable);
    }

    public function setArrangeMaxProcesses($maxProcesses)
    {
        $this->arrangeManager->setMaxProcesses($maxProcesses);
    }

    public function setAssignMaxProcesses($maxProcesses)
    {
        $this->assignManager->setMaxProcesses($maxProcesses);
    }

    public function setActMaxProcesses($maxProcesses)
    {
        $this->actManager->setMaxProcesses($maxProcesses);
    }

    public function setMaxProcesses($maxProcesses)
    {
        $this->arrangeManager->setMaxProcesses($maxProcesses);
        $this->assignManager->setMaxProcesses($maxProcesses);
        $this->actManager->setMaxProcesses($maxProcesses);
    }

    public function run()
    {
        $this->arrangeManager->run();
        $this->assignManager->run();
        $this->actManager->run();
    }

    public function getArrangeManager()
    {
        return $this->arrangeManager;
    }

    public function getAssignManager()
    {
        return $this->assignManager;
    }

    public function getActManager()
    {
        return $this->actManager;
    }
}
