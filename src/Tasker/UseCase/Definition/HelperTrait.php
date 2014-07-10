<?php
namespace Tasker\UseCase\Definition;

use MultiTierArchitecture\Boundary\Definition\RequestAbstract;
use Tasker\Boundary\Response;
use Tasker\UseCase\Factory;

trait HelperTrait
{
    private $useCase = null;
    private $useCaseResponse = [];
    private $useCaseTotalResultCount = 0;
    private $useCaseResponseStatus = Response::STATUS_FAIL;

    public function executeUseCase($useCaseString, RequestAbstract $request)
    {
        /** @var \MultiTierArchitecture\UseCase\Definition\BaseAbstract $useCase */
        $useCase = Factory::make($useCaseString);
        $useCase->setRequest($request);
        $useCase->execute();

        $this->useCase                 = $useCase;
        $this->useCaseResponse         = $useCase->getResponse();
        $this->useCaseTotalResultCount = $this->useCaseResponse->getTotalResultCount();
        $this->useCaseResponseStatus   = $this->useCaseResponse->getStatus();
    }

    public function getUseCase()
    {
        return $this->useCase;
    }

    public function getUseCaseResponse()
    {
        return $this->useCaseResponse;
    }

    public function getUseCaseTotalResultCount()
    {
        return $this->useCaseTotalResultCount;
    }

    public function getUseCaseResponseStatus()
    {
        return $this->useCaseResponseStatus;
    }

    public function runUseCaseWithNoOfRetriesOnFail(
        $useCaseString,
        RequestAbstract $request,
        $maxRetries = 1
    ) {
        $remainingRetries = $maxRetries;
        $loop = true;
        $response = null;
        while ($loop) {
            $this->executeUseCase($useCaseString, $request);
            $response = $this->getUseCaseResponse();
            if ($response->getStatus() != Response::STATUS_SUCCESS) {
                $remainingRetries--;
                if ($remainingRetries > 0) {
                    sleep(1);
                }
            }
            else {
                $loop = false;
            }
        }
        return $response;
    }
}
