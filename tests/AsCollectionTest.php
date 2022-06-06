<?php

namespace Tests;

use Codewiser\Enum\Castable\AsCollection;

class AsCollectionTest extends BaseTest
{
    protected function getCastClass(): string
    {
        return AsCollection::class;
    }
}
