<?php
namespace Test\Tasker\UseCase\Lock;

use Tasker\UseCase\Factory as UseCaseFactory;
use Tasker\Boundary\Request;
use Tasker\Entity\Lock;
use Test\Tasker\BaseClass;

class UpdateTest extends BaseClass
{
    public function tearDown()
    {}

    public function testCreateANewLockWithId1234AndUpdateItPassingThenDeleteIt()
    {
        $this->cleanAndPopulateDatabase();
        $request = new Request(['id' => '1234']);

        /** @var \Tasker\UseCase\Lock\Create $useCase */
        $useCase = UseCaseFactory::make('lock|create');
        $useCase->setRequest($request);
        $useCase->execute();

        $request = new Request(
            [
                ['id' => '1234'],
                ['id' => '6789']
            ]
        );

        /** @var \Tasker\UseCase\Lock\Update $useCase */
        $useCase = UseCaseFactory::make('lock|update');
        $useCase->setRequest($request);
        $useCase->execute();

        /** @var \Tasker\Boundary\Response $response */
        $response = $useCase->getResponse();
        /** @var array $result */
        $result = $response->getResult();

        $this->assertInstanceOf('MultiTierArchitecture\Entity\Definition\EntityInterface', $result[0]);
        $this->assertEquals('6789', $result[0]->getId());

        /** @var \Tasker\UseCase\Lock\Delete $useCase */
        $useCase = UseCaseFactory::make('lock|delete');
        $useCase->setRequest(new Request(['id' => '6789']));
        $useCase->execute();

        /** @var \Tasker\Boundary\Response $response */
        $response = $useCase->getResponse();
        /** @var array $result */
        $result = $response->getResult();

        $this->assertTrue(empty($result[0]));
    }
}
