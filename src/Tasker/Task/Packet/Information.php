<?php
namespace Tasker\Task\Packet;


class Information
{
    private $processId;
    private $hostname;
    private $extra;
    private $processMaxRetryTimeBeforeContinue;
    private $arrangeMode;

    /**
     * @param mixed $arrangeMode
     */
    public function setArrangeMode($arrangeMode)
    {
        $this->arrangeMode = $arrangeMode;
    }

    /**
     * @return mixed
     */
    public function getArrangeMode()
    {
        return $this->arrangeMode;
    }

    /**
     * @param mixed $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @return mixed
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param mixed $processId
     */
    public function setProcessId($processId)
    {
        $this->processId = $processId;
    }

    /**
     * @return mixed
     */
    public function getProcessId()
    {
        return $this->processId;
    }

    /**
     * @param mixed $processMaxRetryTimeBeforeContinue
     */
    public function setProcessMaxRetryTimeBeforeContinue($processMaxRetryTimeBeforeContinue)
    {
        $this->processMaxRetryTimeBeforeContinue = $processMaxRetryTimeBeforeContinue;
    }

    /**
     * @return mixed
     */
    public function getProcessMaxRetryTimeBeforeContinue()
    {
        return $this->processMaxRetryTimeBeforeContinue;
    }
} 