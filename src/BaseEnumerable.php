<?php

namespace Codewiser\Enum\Castable;

use BackedEnum;
use Codewiser\Enum\Castable\Exceptions\InvalidArgumentException;
use Codewiser\Enum\Castable\Exceptions\NotEnoughArgumentsException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;

abstract class BaseEnumerable implements CastsAttributes
{
    /**
     * @var string $fieldType DB field type
     */
    public string $fieldType;

    /**
     * @var string|BackedEnum $enumClass Enum
     */
    public string|BackedEnum $enumClass;

    /**
     * @var string|null $customCollection Defined result collection
     */
    public string|null $customCollection = null;

    public function __construct(array $arguments)
    {
        $this->init($arguments);
    }

    /**
     * Check and init arguments
     *
     * @param array $arguments
     * @return void
     * @throws InvalidArgumentException
     * @throws NotEnoughArgumentsException
     */
    private function init(array $arguments): void
    {
        if (count($arguments) < 2) {
            throw new NotEnoughArgumentsException();
        }

        foreach ($arguments as $argument) {
            if (enum_exists($argument)) {
                $this->enumClass = $argument;
                continue;
            }
            if ($argument === 'set' || $argument === 'json' || $argument === 'array') {
                $this->fieldType = $argument;
                continue;
            }
            if (!enum_exists($argument) && class_exists($argument) && new $argument instanceof Collection) {
                $this->customCollection = $argument;
            }
        }

        if (!isset($this->fieldType)) {
            throw new InvalidArgumentException('Invalid DB field type argument');
        }

        if (!isset($this->enumClass)) {
            throw new InvalidArgumentException('Invalid Enum argument');
        }

        if (count($arguments) > 2 && !isset($this->customCollection)) {
            throw new InvalidArgumentException('Invalid Collection argument');
        }
    }

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (!isset($attributes[$key])) {
            return;
        }

        $items = match ($this->fieldType) {
            'set' => array_filter(array_map('trim', explode(',', $value))),
            'json', 'array' => json_decode($value, true),
            default => [],
        };

        // Convert array values to Enums
        $arrayOfFoundEnumValues = array_map(function ($item) {
            return $this->enumClass::tryFrom($item);
        }, $items);

        // Filter null values and return result
        return array_filter($arrayOfFoundEnumValues, function ($item) {
            return $item;
        });
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes)
    {
        // Set null if value is not array or value is empty array
        if (!is_array($value) || !count($value)) {
            return null;
        }

        // Filter values by Enum cases
        $filteredByEnumCasesArray = array_filter($value, function ($item) {
            return $this->enumClass::tryFrom($item);
        });

        // If filtered array is empty set null
        if (!count($filteredByEnumCasesArray)) {
            return null;
        }

        // Convert array to defined field type
        $convertedToFieldTypeValue = match ($this->fieldType) {
            'set' => implode(',', $filteredByEnumCasesArray),
            'json', 'array' => json_encode($filteredByEnumCasesArray),
            default => null,
        };

        return $convertedToFieldTypeValue
            ? [$key => $convertedToFieldTypeValue]
            : null;
    }
}
