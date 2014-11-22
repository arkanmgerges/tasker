<?php
namespace Tasker\DataGateway\Repository\Definition;

use Tasker\Boundary\Request;

/**
 * Common db functions
 *
 * @category Tasker\DataGateway
 * @package  Tasker\DataGateway\Repository\Definition
 * @author   Arkan M. Gerges <arkan.m.gerges@gmail.com>
 * @version  GIT: $Id:$
 */
class DbAbstract
{
    const STATUS_RESPONSE_SUCCESS = 1;

    /** @var array  $response  Response of last db call */
    private $response = null;

    /** @var array  $entitiesFromResponse  Array of entities' objects that was taken from response and
     * mapped as entities based on the api */
    private $entitiesFromResponse = null;

    /**
     * Init response, entitiesFromResponse to array type and setup propel
     */
    public function __construct()
    {
        // Init response and entitiesFromResponse to be of type array
        $this->response             = [];
        $this->entitiesFromResponse = [];
    }

    /**
     * Count the number of results based on the passed request
     *
     * @param Request  $request  Request object used in this count method
     *
     * @return int
     */
    public function getTotalResultCount(Request $request)
    {
        return 0;
    }

    /**
     * Get last db call response
     *
     * @return array Response of the last api call
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set a response
     *
     * @param array  $response  Response that need to be set
     *
     * @return void
     */
    public function setResponse(array $response)
    {
        $this->response = $response;
    }

    /**
     * Return array of objects that is based on the entities, this means that the response will have data that is
     * mapped as objects of entities and these objects will be put into array
     *
     * @return array
     */
    public function getEntitiesFromResponse()
    {
        return $this->entitiesFromResponse;
    }

    /**
     * Set array of objects that is based on the entities, this means that the response will have data that is
     * mapped as objects of entities and these objects will be put into this array
     *
     * @param array  $entitiesFromResponse  Array of entities that this variable will hold
     *
     * @return void
     */
    public function setEntitiesFromResponse($entitiesFromResponse)
    {
        $this->entitiesFromResponse = $entitiesFromResponse;
    }

    /**
     * Return response status, for this db class, it will always return success status, because errors will be
     * caught through exceptions, this will not be the same for api, because api can return error codes in the
     * response, and in the api class the call can be done successfully but the return code has error
     *
     * @return int
     */
    public function getResponseStatus()
    {
        // Always return success, because any other error will be caught through exceptions
        return self::STATUS_RESPONSE_SUCCESS;
    }

    /**
     * Verify if response status is success
     *
     * @return bool true if response status is success, false is otherwise
     */
    public function isResponseStatusSuccess()
    {
        return self::STATUS_RESPONSE_SUCCESS == $this->getResponseStatus();
    }
}
