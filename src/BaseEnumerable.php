<?php

namespace Codewiser\Enum\Castable;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

abstract class BaseEnumerable implements CastsAttributes
{
    /** @var string $dbType Тип поля в БД */
    public string $dbType;

    /** @var string $enumClass Enum в который необходимо привести значения из БД */
    public string $enumClass;

    /** @var string|null $customCollection В какую коллекцию необходимо привести результирующую коллекцию */
    public string|null $customCollection;

    public function __construct(array $arguments)
    {
        $this->dbType = $arguments[0];
        $this->enumClass = $arguments[1];
        if (isset($arguments[2])) {
            $this->customCollection = $arguments[2];
        }
    }

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $items = match ($this->dbType) {
            'set' => explode(',', $value),
            'json' => json_decode($value),
            default => [],
        };

        // Приводим элементы к элементам Enum'а
        $resultArray = array_map(function ($item) {
            return $this->enumClass::tryFrom($item);
        }, $items);

        // Фильтруем на всякий случай соответствию определенным в Enum'е возможным значениям
        return array_filter($resultArray, function ($item) {
            return $item;
        });
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes)
    {
        // Если значение не массив или этот массив пустой, то пишем null
        if (!is_array($value) || !count($value)) {
            return null;
        }

        // Фильтруем на допустимые значения
        $value = array_filter($value, function ($item) {
            return $this->enumClass::tryFrom($item);
        });

        // Если после фильтрации массив пуст
        if (!count($value)) {
            return null;
        }

        $items = match ($this->dbType) {
            'set' => implode(',', $value),
            'json' => json_encode($value),
            default => null,
        };

        return [$key => $items];
    }
}
