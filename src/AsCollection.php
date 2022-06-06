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
                $result = parent::get($model, $key, $value, $attributes);


                return is_array($result)
                    ? ($this->customCollection
                        ? new $this->customCollection($result)
                        : new Collection($result))
                    : $result;
            }

            public function set($model, $key, $value, $attributes)
            {
                return parent::set($model, $key, $value, $attributes);
            }
        };
    }
}
