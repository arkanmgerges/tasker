<?php
namespace Tasker\Exception\Definition;

use MultiTierArchitecture\Exception\Definition\EmergencyAbstract as MultiTierArchEmergencyAbstract;
use Tasker\Exception\Tool\Helper;
use Monolog\Logger;

/**
 * This type of exception is used when the system is unstable
 *
 * Example: Entire website down, database unavailable, etc. This should
 * trigger the SMS alerts and wake you up.
 *
 * @category Exception
 * @package  Tasker\Exception
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
abstract class EmergencyAbstract extends MultiTierArchEmergencyAbstract
{
    /**
     * Prepare config and logger
     *
     * @param string  $message  Message of this exception
     * @param string  $file     File path and name where the exception has taken place
     * @param int     $lineNo   Line number where the exception has taken place
     */
    public function __construct($message, $file, $lineNo)
    {
        parent::__construct($message);
        $logger = Helper::getLogger(Logger::EMERGENCY);
        $logger->emergency('\'' . $message . '\' in file \'' . $file . '\', at line ' . $lineNo);
    }
}
