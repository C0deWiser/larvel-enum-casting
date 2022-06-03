<?php

namespace Codewiser\Enum\Castable;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Support\Collection;

class AsCollection implements Castable
{

    /**
     * @inheritDoc
     */
    public static function castUsing(array $arguments): BaseEnumerable
    {
        return new class($arguments) extends BaseEnumerable {

            public function get($model, $key, $value, $attributes)
            {
                $resultArray = parent::get($model, $key, $value, $attributes);

                return $this->customCollection
                    ? new $this->customCollection($resultArray)
                    : new Collection($resultArray);

            }

            public function set($model, $key, $value, $attributes)
            {
                return parent::set($model, $key, $value, $attributes);
            }
        };
    }
}
