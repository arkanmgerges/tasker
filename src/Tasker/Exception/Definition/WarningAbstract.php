<?php
namespace Tasker\Exception\Definition;

use MultiTierArchitecture\Exception\Definition\WarningAbstract as MultiTierArchWarningAbstract;
use Tasker\Exception\Tool\Helper;
use Monolog\Logger;

/**
 * This type of exception is used when exceptional occurrences that are not errors.
 *
 * Example: Use of deprecated APIs, poor use of an API, undesirable things
 * that are not necessarily wrong.
 *
 * @category Exception
 * @package  Tasker\Exception
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
abstract class WarningAbstract extends MultiTierArchWarningAbstract
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
        $logger = Helper::getLogger(Logger::WARNING);
        $logger->warning('\'' . $message . '\' in file \'' . $file . '\', at line ' . $lineNo);
    }
}
