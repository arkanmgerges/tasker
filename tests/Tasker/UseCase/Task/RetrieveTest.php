<?php
namespace Test\Tasker\UseCase\Task;

use Tasker\UseCase\Factory as UseCaseFactory;
use Tasker\Boundary\Request;
use Test\Tasker\BaseClass;

class RetrieveTest extends BaseClass
{
    public function tearDown()
    {}

    public function testRetrieve()
    {
        //$this->cleanAndPopulateDatabase();

        $request = new Request(['server' => null], [Request::EXTRA_LIMIT => 1]);
        $useCase = UseCaseFactory::make('task|retrieve');
        $useCase->setRequest($request);
        $useCase->execute();

        /** @var \Tasker\Boundary\Response $response */
        $response = $useCase->getResponse();
        /** @var array $result */
        $resultCount = $response->getTotalResultCount();
        $this->assertGreaterThan(0, $resultCount);
    }

    public function testRetrieve2()
    {
        //$this->cleanAndPopulateDatabase();

        $request = new Request(
            [
                'sql' => [
                    'statement'         => 'SELECT * FROM :table1: WHERE ((startingDateTime + repeatingInterval) ' .
                        '< now()) AND ((statusId != ' . 2 . ') AND ' .
                        '(statusId != ' . 3 . ')) AND ' .
                        '(server IS NOT NULL) ORDER BY priority DESC LIMIT '. 0 .',1;',
                    'statementForCount' => 'SELECT * FROM :table1: WHERE ((startingDateTime + repeatingInterval) ' .
                        '< now()) AND ((statusId != 1) AND (statusId != 2)) AND ' .
                        '(server IS NOT NULL);',
                ]
            ]
        );
        $useCase = UseCaseFactory::make('task|retrieve');
        $useCase->setRequest($request);
        $useCase->execute();

        /** @var \Tasker\Boundary\Response $response */
        $response = $useCase->getResponse();
        /** @var array $result */
        $resultCount = $response->getTotalResultCount();
        $this->assertGreaterThan(0, $resultCount);
    }
}
