<?php
namespace Tasker\DataGateway\Exception;

use Tasker\Exception\Definition\ErrorAbstract;

/**
 * Exception for a not found class, this can be used when the system is expected a class in the data gateway
 *
 * @category DataGateway
 * @package  Tasker\DataGateway\Exception
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class ClassNotFound extends ErrorAbstract
{
}
