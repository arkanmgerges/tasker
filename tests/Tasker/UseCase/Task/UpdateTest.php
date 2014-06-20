<?php
namespace Test\Tasker\UseCase\Task;

use Tasker\UseCase\Factory as UseCaseFactory;
use Tasker\Boundary\Request;
use Tasker\Entity\Task;
use Test\Tasker\BaseClass;

class UpdateTest extends BaseClass
{
    public function tearDown()
    {}

    public function testCreateANewTaskWithId1234AndUpdateItPassingThenDeleteIt()
    {
        $this->cleanAndPopulateDatabase();
        $request = new Request(['server' => 'server1234']);

        /** @var \Tasker\UseCase\Task\Create $useCase */
        $useCase = UseCaseFactory::make('task|create');
        $useCase->setRequest($request);
        $useCase->execute();

        $request = new Request(
            [
                ['server' => 'server1234'],
                ['server' => 'test-server']
            ]
        );

        /** @var \Tasker\UseCase\Task\Update $useCase */
        $useCase = UseCaseFactory::make('task|update');
        $useCase->setRequest($request);
        $useCase->execute();

        /** @var \Tasker\Boundary\Response $response */
        $response = $useCase->getResponse();
        /** @var array $result */
        $result = $response->getResult();

        $this->assertInstanceOf('MultiTierArchitecture\Entity\Definition\EntityInterface', $result[0]);
        $this->assertEquals('test-server', $result[0]->getServer());

        /** @var \Tasker\UseCase\Task\Delete $useCase */
        $useCase = UseCaseFactory::make('task|delete');
        $useCase->setRequest(new Request(['server' => 'server1234']));
        $useCase->execute();

        /** @var \Tasker\Boundary\Response $response */
        $response = $useCase->getResponse();
        /** @var array $result */
        $result = $response->getResult();

        $this->assertTrue(empty($result[0]));
    }
}
