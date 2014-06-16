<?php
namespace Tasker\DataGateway\Db\Exception;

use Tasker\DataGateway\Db\Definition\ExceptionInterface;
use Tasker\Exception\Definition\ErrorAbstract;

/**
 * Exception for a not found data base mapper class, this can be used when the system is expected a data base mapper
 *
 * @category DataGateway
 * @package  Tasker\DataGateway\Db\Exception
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class DbMapperNotFound extends ErrorAbstract implements ExceptionInterface
{
}
