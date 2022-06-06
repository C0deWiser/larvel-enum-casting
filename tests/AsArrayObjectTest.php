<?php

namespace Tests;

use Codewiser\Enum\Castable\AsArrayObject;

class AsArrayObjectTest extends BaseTest
{
    protected function getCastClass(): string
    {
        return AsArrayObject::class;
    }
}
