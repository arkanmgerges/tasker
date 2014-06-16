<?php
namespace Tasker\Exception\Definition;

use MultiTierArchitecture\Exception\Definition\AlertAbstract as MultiTierArchAlertAbstract;
use Tasker\Exception\Tool\Helper;
use Monolog\Logger;


/**
 * This type of exception is used when action must be taken immediately
 *
 * @category Exception
 * @package  Tasker\Exception
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
abstract class AlertAbstract extends MultiTierArchAlertAbstract
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
        $logger = Helper::getLogger(Logger::ALERT);
        $logger->alert('\'' . $message . '\' in file \'' . $file . '\', at line ' . $lineNo);
    }
}
