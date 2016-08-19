<?php
namespace Heneke\Http\Serializer\Illuminate\Providers;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Heneke\Http\Serializer\Handler\CarbonHandler;
use Heneke\Http\Serializer\Illuminate\Facade\Serialization;
use Heneke\Http\Serializer\Illuminate\Facade\SerializationFacade;
use Heneke\Http\Serializer\Illuminate\LaravelTypeRegistry;
use Heneke\Http\Serializer\JmsHttpSerializer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Heneke\Http\Serializer\HttpSerializer;
use Heneke\Http\Serializer\TypeRegistry;

class HttpSerializerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PropertyNamingStrategyInterface::class, function (Application $app) {
            return $this->getPropertyNamingStrategy();
        });
        $this->app->singleton(SerializerInterface::class, function (Application $app) {
            AnnotationRegistry::registerLoader('class_exists');
            if ($this->isCacheEnabled()) {
                $cacheDir = storage_path('jms');
                if (!$cacheDir) {
                    mkdir($cacheDir);
                }
                return SerializerBuilder::create()
                    ->setDebug(env('APP_DEBUG', false))
                    ->configureHandlers(function (HandlerRegistry $handlerRegistry) {
                        $handlerRegistry->registerSubscribingHandler(new CarbonHandler());
                    })
                    ->setPropertyNamingStrategy($this->app->make(PropertyNamingStrategyInterface::class))
                    ->setCacheDir($cacheDir)
                    ->build();
            } else {
                return SerializerBuilder::create()
                    ->setDebug(env('APP_DEBUG', false))
                    ->configureHandlers(function (HandlerRegistry $handlerRegistry) {
                        $handlerRegistry->registerSubscribingHandler(new CarbonHandler());
                    })
                    ->setPropertyNamingStrategy($this->app->make(PropertyNamingStrategyInterface::class))
                    ->build();
            }
        });
        $this->app->singleton(TypeRegistry::class, function (Application $app) {
            return new LaravelTypeRegistry();
        });
        $this->app->singleton(HttpSerializer::class, function (Application $app) {
            return new JmsHttpSerializer(
                $this->app->make(SerializerInterface::class),
                $this->app->make(TypeRegistry::class)
            );
        });
        $this->app->singleton(Serialization::ACCESSOR, function (Application $app) {
            return new SerializationFacade($app, $app->make(HttpSerializer::class));
        });
    }

    public function boot(TypeRegistry $typeRegistry, HttpSerializer $httpSerializer)
    {
        if ($typeRegistry instanceof LaravelTypeRegistry) {
            $typeRegistry->boot($this->app, $httpSerializer);
        }
    }

    /**
     * @return PropertyNamingStrategyInterface
     */
    protected function getPropertyNamingStrategy()
    {
        return new IdenticalPropertyNamingStrategy();
    }

    /**
     * @return bool
     */
    protected function isCacheEnabled()
    {
        return env('APP_JMS_CACHE', true);
    }
}
