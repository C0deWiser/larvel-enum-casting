<?php

namespace Codewiser\Enum\Castable;

use Illuminate\Contracts\Database\Eloquent\Castable;

class AsArray implements Castable
{
    /**
     * @inheritDoc
     */
    public static function castUsing(array $arguments): BaseEnumerable
    {
        return new class($arguments) extends BaseEnumerable
        {
            //
        };
    }
}
