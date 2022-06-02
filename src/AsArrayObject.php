<?php

namespace Codewiser\Enum\Castable;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Database\Eloquent\Casts\ArrayObject;

class AsArrayObject implements Castable
{
    /**
     * @inheritDoc
     */
    public static function castUsing(array $arguments)
    {
        return new class($arguments) extends BaseEnumerable {

            public function get($model, $key, $value, $attributes): ArrayObject
            {
                $resultArray = parent::get($model, $key, $value, $attributes);
                return new ArrayObject($resultArray);
            }

            /**
             * @param $model
             * @param string $key
             * @param ArrayObject $value
             * @param array $attributes
             * @return mixed
             */
            public function serialize($model, string $key, $value, array $attributes)
            {
                return $value->getArrayCopy();
            }
        };
    }
}
