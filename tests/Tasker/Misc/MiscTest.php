<?php
namespace Test\Tasker\Misc;

use Test\Tasker\BaseClass;
use Tasker\Exception\UnExpectedException;
use Tasker\Exception\Tool\Helper as ExceptionHelper;

class MiscTest extends BaseClass
{
    public function tearDown()
    {}

    /**
     * @expectedException \Tasker\Exception\UnExpectedException
     */
    public function testThrowAnException()
    {
        ExceptionHelper::setConfigPath(__DIR__ . '/config.php');
        ExceptionHelper::setEnvironmentVariable('APP_ENV');
        throw new UnExpectedException('this is a test', __FILE__, __LINE__);
    }
}
