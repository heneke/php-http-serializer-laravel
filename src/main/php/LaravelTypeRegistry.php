<?php
namespace Heneke\Http\Serializer\Illuminate;

use Heneke\Http\Serializer\HttpSerializer;
use Heneke\Http\Serializer\SimpleTypeRegistry;
use Illuminate\Contracts\Foundation\Application;

use Psr\Http\Message\ServerRequestInterface;

class LaravelTypeRegistry extends SimpleTypeRegistry
{

    public function boot(Application $app, HttpSerializer $httpSerializer)
    {
        foreach ($this->types as $mime => $type) {
            $app->bind($type, function () use ($app, $httpSerializer, $type) {
                $serverRequest = $app->make(ServerRequestInterface::class);
                return $httpSerializer->deserialize($serverRequest, $type);
            });
        }
    }
}
