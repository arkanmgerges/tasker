<?php
namespace Test\Tasker\UseCase\Process;

use Tasker\UseCase\Factory as UseCaseFactory;
use Tasker\Boundary\Request;
use Tasker\Entity\Process;
use Test\Tasker\BaseClass;

class UpdateTest extends BaseClass
{
    public function tearDown()
    {}

    public function testCreateANewProcessWithId1234AndUpdateItPassingThenDeleteIt()
    {
        $this->cleanAndPopulateDatabase();
        $request = new Request(['id' => '1234', 'server' => 'server1234']);

        /** @var \Tasker\UseCase\Process\Create $useCase */
        $useCase = UseCaseFactory::make('process|create');
        $useCase->setRequest($request);
        $useCase->execute();

        $request = new Request(
            [
                ['id' => '1234'],
                ['id' => '6789', 'server' => 'test-server']
            ]
        );

        /** @var \Tasker\UseCase\Process\Update $useCase */
        $useCase = UseCaseFactory::make('process|update');
        $useCase->setRequest($request);
        $useCase->execute();

        /** @var \Tasker\Boundary\Response $response */
        $response = $useCase->getResponse();
        /** @var array $result */
        $result = $response->getResult();

        $this->assertInstanceOf('MultiTierArchitecture\Entity\Definition\EntityInterface', $result[0]);
        $this->assertEquals('6789', $result[0]->getId());
        $this->assertEquals('test-server', $result[0]->getServer());

        /** @var \Tasker\UseCase\Process\Delete $useCase */
        $useCase = UseCaseFactory::make('process|delete');
        $useCase->setRequest(new Request(['id' => '6789']));
        $useCase->execute();

        /** @var \Tasker\Boundary\Response $response */
        $response = $useCase->getResponse();
        /** @var array $result */
        $result = $response->getResult();

        $this->assertTrue(empty($result[0]));
    }
}
