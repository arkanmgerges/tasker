<?php
namespace Test\Tasker\UseCase\Process;

use Tasker\Entity\Process;
use Tasker\UseCase\Factory as UseCaseFactory;
use Tasker\Entity\Factory as EntityFactory;
use Tasker\Boundary\Request;
use Test\Tasker\BaseClass;

class CreateTest extends BaseClass
{
    public function tearDown()
    {}

    public function testCreateANewProcessWithId12345()
    {
        $this->cleanAndPopulateDatabase();
        $request = new Request(
            ['id' => '12345', 'server' => 'server1234|5678',
            'creatingDateTime' => '2014-04-08 10:10:10']
        );

        /** @var \Tasker\UseCase\Process\Create $useCase */
        $useCase = UseCaseFactory::make('process|create');
        $useCase->setRequest($request);
        $useCase->execute();

        $request = new Request(['id' => '12345']);
        $useCase = UseCaseFactory::make('process|retrieve');
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
