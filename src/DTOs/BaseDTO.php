<?php


namespace rateb\structure\DTOs;

use Illuminate\Http\Request;

abstract class BaseDTO
{

    public static function fromRequest(?Request $request, ...$params): static
    {
        $instance = new static();


        foreach ($request->all() as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
         }


        $instance->handleAdditionalParams($request, ...$params);

        return $instance;
    }


    protected function handleAdditionalParams(Request $request, ...$params): void
    {

    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }


    protected static function getArg(string $key, mixed ...$args): mixed
    {
        $args = $args[0];

        foreach ($args as $arg)
        {
            if (is_array($arg) && isset($arg[$key]))
            {
                return $arg[$key];
            }
        }

        return null;
    }
}


