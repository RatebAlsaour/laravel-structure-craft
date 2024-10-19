<?php
namespace RatebSa\Structure\DTOs;

use Illuminate\Http\Request;

abstract class BaseDTO
{
    public static function fromRequest(?Request $request, ...$params): static
    {
        $instance = new static();

        foreach ($request->all() as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = is_array($value) ? self::formArray($value) : $value;
            }
        }

        $instance->handleAdditionalParams($request, ...$params);

        return $instance;
    }

    protected static function formArray(?array $inputArray, ...$params): static
    {
        $instance = new static();

        foreach ($inputArray as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = is_array($value) ? self::formArray($value) : $value;
            }
        }

        return $instance;
    }

    protected function handleAdditionalParams(Request $request, ...$params): void
    {

    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function prepareDataForUpdate(): array
    {
        $arrayRepresentation = $this->toArray();
        $filteredArray = array_filter($arrayRepresentation, function($value) {
            return !is_null($value);
        });

        foreach ($filteredArray as $key => $value) {
            if (is_array($value)) {
                $filteredArray[$key] = self::formArray($value);
            }
        }

        return $filteredArray;
    }

    protected static function getArg(string $key, mixed ...$args): mixed
    {
        $args = $args[0];

        foreach ($args as $arg) {
            if (is_array($arg) && isset($arg[$key])) {
                return $arg[$key];
            }
        }

        return null;
    }
}
