<?php

namespace Codewiser\Enum\Castable;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Database\Eloquent\Casts\ArrayObject;

class AsArrayObject implements Castable
{
    /**
     * @inheritDoc
     */
    public static function castUsing(array $arguments): BaseEnumerable
    {
        return new class($arguments) extends BaseEnumerable {

            public function get($model, $key, $value, $attributes): ArrayObject
            {
                $resultArray = parent::get($model, $key, $value, $attributes);
                return new ArrayObject($resultArray);
            }
        };
    }
}
