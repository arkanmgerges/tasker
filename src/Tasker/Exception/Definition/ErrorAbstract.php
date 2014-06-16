<?php
namespace Tasker\Exception\Definition;

use MultiTierArchitecture\Exception\Definition\ErrorAbstract as MultiTierArchErrorAbstract;
use Tasker\Exception\Tool\Helper;
use Monolog\Logger;

/**
 * This type of exception is used when runtime errors that do not require immediate action but should typically
 * be logged and monitored.
 *
 * @category Exception
 * @package  Tasker\Exception
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
abstract class ErrorAbstract extends MultiTierArchErrorAbstract
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
        $logger = Helper::getLogger(Logger::ERROR);
        $logger->error('\'' . $message . '\' in file \'' . $file . '\', at line ' . $lineNo);
    }
}
