<?php

namespace Tests;

use Codewiser\Enum\Castable\AsArray;

class AsArrayTest extends BaseTest
{
    protected function getCastClass(): string
    {
        return AsArray::class;
    }
}
