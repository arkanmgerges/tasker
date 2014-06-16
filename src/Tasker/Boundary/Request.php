<?php
namespace Tasker\Boundary;

use MultiTierArchitecture\Boundary\Definition\RequestAbstract;

/**
 * Request class used for communication with use cases
 *
 * @category Boundary
 * @package  Tasker\Boundary
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class Request extends RequestAbstract
{
    const EXTRA_ORDER_BY  = 'EXTRA_ORDER_BY';
    const EXTRA_ORDER_DIR = 'EXTRA_ORDER_DIR';
    const EXTRA_LIMIT     = 'EXTRA_LIMIT';
    const EXTRA_OFFSET    = 'EXTRA_OFFSET';
}
