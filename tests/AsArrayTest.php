<?php

namespace Tests;

use Codewiser\Enum\Castable\AsArray;
use PHPUnit\Framework\TestCase;

class AsArrayTest extends TestCase
{
    public function testWithNotExistsValues()
    {
        $testValue = '1,2,4';
        $values = $this->getValues($testValue);

        $this->assertIsArray($values);
        $this->assertCount(2, $values);
        $this->assertTrue(in_array(Enumer::one, $values));
        $this->assertTrue(in_array(Enumer::two, $values));
    }

    public function testWithSpaces()
    {
        $testValue = ' 1, 2,  3';
        $values = $this->getValues($testValue);

        $this->assertCount(3, $values);
    }

    private function getValues(string $testValue)
    {
        return AsArray::castUsing(['set', Enumer::class])
            ->get(null, 'attribute', $testValue, []);
    }
}