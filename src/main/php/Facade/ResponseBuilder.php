<?php
namespace Heneke\Http\Serializer\Illuminate\Facade;

use Heneke\Http\Serializer\HttpSerializableResponse;
use Heneke\Http\Serializer\HttpSerializer;
use Psr\Http\Message\ServerRequestInterface;

class ResponseBuilder
{

    /**
     * @var HttpSerializer
     */
    private $httpSerializer;

    /**
     * @var ServerRequestInterface
     */
    private $serverRequest;

    /**
     * @var HttpSerializableResponse
     */
    private $serializable;

    public function __construct(HttpSerializer $httpSerializer, ServerRequestInterface $serverRequest, $data, $format = null, $mimeType = null, $groups = null)
    {
        $this->httpSerializer = $httpSerializer;
        $this->serverRequest = $serverRequest;
        $this->serializable = new HttpSerializableResponse($data, $format, $mimeType, $groups);
    }

    public function build()
    {
        return $this->httpSerializer->serialize($this->serializable, $this->serverRequest);
    }

    public function withFormat($format)
    {
        $this->serializable->withFormat($format);
        return $this;
    }

    public function withMimeType($mimeType)
    {
        $this->serializable->withMimeType($mimeType);
        return $this;
    }

    public function withGroups($groups, $default = false)
    {
        $this->serializable->withGroups($groups, $default);
        return $this;
    }
}
