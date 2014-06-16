<?php
namespace Tasker\UseCase\Definition;

use MultiTierArchitecture\Boundary\Definition\RequestAbstract;
use Tasker\Boundary\Response;
use Tasker\UseCase\Factory;

trait HelperTrait
{
    public function runUseCaseAndReturnIt($useCaseString, RequestAbstract $request)
    {
        /** @var \MultiTierArchitecture\UseCase\Definition\BaseAbstract $useCase */
        $useCase = Factory::make($useCaseString);
        $useCase->setRequest($request);
        $useCase->execute();
        return $useCase;
    }

    public function runUseCaseAndReturnResponse($useCaseString, RequestAbstract $request)
    {
        $useCase = $this->runUseCaseAndReturnIt($useCaseString, $request);
        return $useCase->getResponse();
    }

    public function runUseCaseAndReturnTotalResultCount($useCaseString, RequestAbstract $request, $maxRetries = 1)
    {
        $response = $this->runUseCaseWithNoOfRetriesOnFailAndReturnResponse($useCaseString, $request, $maxRetries);
        return $response->getTotalResultCount();
    }

    public function runUseCaseWithNoOfRetriesOnFailAndReturnStatus(
        $useCaseString,
        RequestAbstract $request,
        $maxRetries = 1)
    {
        $response = $this->runUseCaseWithNoOfRetriesOnFailAndReturnResponse($useCaseString, $request, $maxRetries);
        return $response->getStatus();
    }

    public function runUseCaseWithNoOfRetriesOnFailAndReturnResponse(
        $useCaseString,
        RequestAbstract $request,
        $maxRetries = 1
    ) {
        $remainingRetries = $maxRetries;
        $loop = true;
        $response = null;
        while ($loop) {
            $response = $this->runUseCaseAndReturnResponse($useCaseString, $request);
            if ($response->getStatus() != Response::STATUS_SUCCESS) {
                $remainingRetries--;
                if ($remainingRetries <= 0) {
                    return $response;
                }
                sleep(1);
            }
            else {
                $loop = false;
            }
        }
        return $response;
    }
}
