<?php

namespace CalendArt\Adapter\Office365\Api;

use GuzzleHttp\Message\ResponseInterface;

use CalendArt\Adapter\Office365\Exception;

class ResponseHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $response;
    private $api;

    protected function setUp()
    {
        $this->response = $this->prophesize(ResponseInterface::class);
        $this->api = new Api;
    }

    public function testHandleErrorsWithSuccessfulResponse()
    {
        $this->response->getStatusCode()->shouldBeCalled()->willReturn(200);
        $this->api->get($this->response->reveal());

        $this->response->getStatusCode()->shouldBeCalled()->willReturn(301);
        $this->api->get($this->response->reveal());
    }

    /**
     * @dataProvider getResponses
     */
    public function testHandleErrors($statusCode, $exception)
    {
        $this->setExpectedException($exception);

        $this->response->getStatusCode()->shouldBeCalled()->willReturn($statusCode);
        $this->response->json()->shouldBeCalled()->willReturn(['error' => ['message' => 'foo']]);
        $this->api->get($this->response->reveal());
    }

    public function getResponses()
    {
        return [
            [400,Exception\BadRequestException::class],
            [401,Exception\UnauthorizedException::class],
            [403,Exception\ForbiddenException::class],
            [404,Exception\NotFoundException::class],
            [405,Exception\MethodNotAllowedException::class],
            [406,Exception\BadRequestException::class],
            [409,Exception\ConflictException::class],
            [410,Exception\GoneException::class],
            [411,Exception\BadRequestException::class],
            [412,Exception\PreconditionException::class],
            [413,Exception\BadRequestException::class],
            [415,Exception\BadRequestException::class],
            [416,Exception\BadRequestException::class],
            [422,Exception\BadRequestException::class],
            [429,Exception\LimitExceededException::class],
            [500,Exception\InternalServerErrorException::class],
            [501,Exception\NotFoundException::class],
            [503,Exception\InternalServerErrorException::class],
            [507,Exception\LimitExceededException::class],
            [509,Exception\LimitExceededException::class],
        ];
    }
}

class Api
{
    use ResponseHandler;

    /**
     * Simulate a get method of an API
     */
    public function get(ResponseInterface $response)
    {
        $this->handleResponse($response);
    }
}
