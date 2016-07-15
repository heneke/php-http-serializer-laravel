<?php
namespace Heneke\Http\Serializer\Illuminate\Facade;

use Illuminate\Support\Facades\Facade;
use Heneke\Http\Serializer\HttpSerializer;

class Serialization extends Facade
{
    const ACCESSOR = 'serialization.facade';

    protected static function getFacadeAccessor()
    {
        return self::ACCESSOR;
    }
}
