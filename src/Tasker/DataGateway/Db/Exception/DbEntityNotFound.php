<?php
namespace Tasker\DataGateway\Db\Exception;

use Tasker\DataGateway\Db\Definition\ExceptionInterface;
use Tasker\Exception\Definition\ErrorAbstract;

/**
 * Exception for a not found database entity, this can be used when the system is expected a db entity
 *
 * @category DataGateway
 * @package  Tasker\DataGateway\Db\Exception
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class DbEntityNotFound extends ErrorAbstract implements ExceptionInterface
{
}
