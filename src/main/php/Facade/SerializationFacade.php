<?php
namespace Heneke\Http\Serializer\Illuminate\Facade;

use Heneke\Http\Serializer\HttpSerializable;
use Heneke\Http\Serializer\HttpSerializableResponse;
use Heneke\Http\Serializer\HttpSerializer;
use Illuminate\Foundation\Application;
use Psr\Http\Message\ServerRequestInterface;

class SerializationFacade
{

    /**
     * @var Application
     */
    private $app;

    /**
     * @var HttpSerializer
     */
    private $serializer;

    public function __construct(Application $app, HttpSerializer $http)
    {
        $this->app = $app;
        $this->serializer = $http;
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param string|null $mimeType
     * @param string|array|null $groups
     * @return ResponseBuilder
     */
    public function response($data, $format = null, $mimeType = null, $groups = null)
    {
        return new ResponseBuilder($this->serializer, $this->getRequest(), $data, $format, $mimeType, $groups);
    }

    /**
     * @return ServerRequestInterface
     */
    private function getRequest()
    {
        return $this->app->make(ServerRequestInterface::class);
    }
}
