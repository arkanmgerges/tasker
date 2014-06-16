<?php
namespace Tasker\Exception;

use Tasker\Exception\Definition\CriticalAbstract;

/**
 * This type of exception is used when critical conditions happened.
 *
 * Example: Application component unavailable, unexpected exception.
 *
 * @category Exception
 * @package  Tasker\Exception
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class ComponentNotFound extends CriticalAbstract
{
}