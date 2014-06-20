<?php
namespace Test\Tasker\UseCase\Task;

use Tasker\Entity\Task;
use Tasker\UseCase\Factory as UseCaseFactory;
use Tasker\Entity\Factory as EntityFactory;
use Tasker\Boundary\Request;
use Test\Tasker\BaseClass;

class CreateTest extends BaseClass
{
    public function tearDown()
    {}

    public function testCreateANewTaskWithId12345()
    {
        $this->cleanAndPopulateDatabase();
        $request = new Request(
            ['server' => 'server1234|5678',
            'creatingDateTime' => '2014-04-08 10:10:10']
        );

        /** @var \Tasker\UseCase\Task\Create $useCase */
        $useCase = UseCaseFactory::make('task|create');
        $useCase->setRequest($request);
        $useCase->execute();

        $request = new Request(['server' => 'server1234|5678']);
        $useCase = UseCaseFactory::make('task|retrieve');
        $useCase->setRequest($request);
        $useCase->execute();

        /** @var \Tasker\Boundary\Response $response */
        $response = $useCase->getResponse();
        /** @var array $result */
        $result = $response->getResult();

        $this->assertInstanceOf('MultiTierArchitecture\Entity\Definition\EntityInterface', $result[0]);
        $this->assertEquals('server1234|5678', $result[0]->getServer());
    }
}
