<?php
namespace Test\Tasker\UseCase\Lock;

use Tasker\Entity\Lock;
use Tasker\UseCase\Factory as UseCaseFactory;
use Tasker\Entity\Factory as EntityFactory;
use Tasker\Boundary\Request;
use Test\Tasker\BaseClass;

class CreateTest extends BaseClass
{
    public function tearDown()
    {}

    public function testCreateANewLockWithId12345()
    {
        $this->cleanAndPopulateDatabase();
        $request = new Request(
            ['id' => '12345',
            'creatingDateTime' => '2014-04-08 10:10:10']
        );

        /** @var \Tasker\UseCase\Lock\Create $useCase */
        $useCase = UseCaseFactory::make('lock|create');
        $useCase->setRequest($request);
        $useCase->execute();

        $request = new Request(['id' => '12345']);
        $useCase = UseCaseFactory::make('lock|retrieve');
        $useCase->setRequest($request);
        $useCase->execute();

        /** @var \Tasker\Boundary\Response $response */
        $response = $useCase->getResponse();
        /** @var array $result */
        $result = $response->getResult();

        $this->assertInstanceOf('MultiTierArchitecture\Entity\Definition\EntityInterface', $result[0]);
        $this->assertEquals('12345', $result[0]->getId());
    }

    public function testCreate()
    {
        $this->cleanAndPopulateDatabase();
        $request = new Request(
            ['id' => '12345',
                'creatingDateTime' => '2014-04-08 10:10:10']
        );

        /** @var \Tasker\UseCase\Lock\Create $useCase */
        $useCase = UseCaseFactory::make('lock|create');
        $useCase->setRequest($request);
        $useCase->execute();

        $useCase->execute();

//
//        $request = new Request(['id' => '12345']);
//        $useCase = UseCaseFactory::make('lock|retrieve');
//        $useCase->setRequest($request);
//        $useCase->execute();
//
//        /** @var \Tasker\Boundary\Response $response */
//        $response = $useCase->getResponse();
//        /** @var array $result */
//        $result = $response->getResult();
//
//        $this->assertInstanceOf('MultiTierArchitecture\Entity\Definition\EntityInterface', $result[0]);
//        $this->assertEquals('12345', $result[0]->getId());

    }
}
