<?php

namespace Tests;

use Codewiser\Enum\Castable\AsArray;
use Codewiser\Enum\Castable\AsArrayObject;
use Codewiser\Enum\Castable\AsCollection;
use Codewiser\Enum\Castable\Exceptions\InvalidArgumentException;
use Codewiser\Enum\Castable\Exceptions\NotEnoughArgumentsException;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Tests\Collections\CustomCollection;
use Tests\Enums\EnumerInt;
use Tests\Enums\EnumerString;

abstract class BaseTest extends TestCase
{
    public function testSetFieldType()
    {
        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();
        $object = $class::castUsing(['set', EnumerInt::class]);
        $this->assertObjectHasAttribute('fieldType', $object);
        $this->assertTrue($object->fieldType === 'set');
    }

    public function testJsonFieldType()
    {
        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();
        $object = $class::castUsing(['json', EnumerInt::class]);
        $this->assertObjectHasAttribute('fieldType', $object);
        $this->assertTrue($object->fieldType === 'json');
    }

    public function testInvalidFieldType()
    {
        $this->expectException(InvalidArgumentException::class);

        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();
        $class::castUsing(['invalidSet', EnumerInt::class]);
    }

    public function testInvalidEnum()
    {
        $this->expectException(InvalidArgumentException::class);

        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();
        $class::castUsing(['set', 'InvalidEnum']);
    }

    public function testInvalidCollection()
    {
        $this->expectException(InvalidArgumentException::class);

        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();
        $class::castUsing(['set', EnumerString::class, 'InvalidCollection']);
    }

    public function testNotEnoughArgumentsEnum()
    {
        $this->expectException(NotEnoughArgumentsException::class);

        switch (true) {
            case ($this instanceof AsArrayTest):
                AsArray::castUsing(['set']);
                break;
            case ($this instanceof AsCollectionTest):
                AsCollection::castUsing(['set']);
                break;
            case ($this instanceof AsArrayObjectTest):
                AsArrayObject::castUsing(['set']);
                break;
        }
    }

    public function testNotEnoughArgumentsFieldType()
    {
        $this->expectException(NotEnoughArgumentsException::class);

        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();
        $class::castUsing([EnumerString::class]);
    }

    public function testArgumentsOrder()
    {
        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();

        $orders = [
            ['set', EnumerString::class, CustomCollection::class],
            ['set', CustomCollection::class, EnumerString::class],
            [CustomCollection::class, 'set', EnumerString::class],
            [CustomCollection::class, EnumerString::class, 'set'],
            [EnumerString::class, 'set', CustomCollection::class],
            [EnumerString::class, CustomCollection::class, 'set'],
        ];

        foreach ($orders as $order) {
            $object = $class::castUsing($order);
            $this->assertObjectHasAttribute('fieldType', $object);
            $this->assertTrue($object->fieldType === 'set');
            $this->assertObjectHasAttribute('enumClass', $object);
            $this->assertTrue(enum_exists($object->enumClass));
            $this->assertObjectHasAttribute('customCollection', $object);
            $this->assertTrue(new $object->customCollection instanceof CustomCollection);
        }
    }

    public function testReturnValues()
    {
        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();
        $dbValue = '1,2,3';

        $values = $class::castUsing(['set', EnumerInt::class])
            ->get(null, 'attribute', $dbValue, []);

        switch (true) {
            case ($this instanceof AsArrayTest):
                $this->assertIsArray($values);
                $this->assertCount(3, $values);
                $this->assertTrue(in_array(EnumerInt::one, $values));
                $this->assertTrue(in_array(EnumerInt::two, $values));
                $this->assertTrue(in_array(EnumerInt::three, $values));
                break;
            case ($this instanceof AsCollectionTest):
                $this->assertTrue($values instanceof Collection);
                $this->assertCount(3, $values);
                $this->assertTrue($values->contains(EnumerInt::one));
                $this->assertTrue($values->contains(EnumerInt::two));
                $this->assertTrue($values->contains(EnumerInt::three));
                break;
            case ($this instanceof AsArrayObjectTest):
                $this->assertTrue($values instanceof ArrayObject);
                $this->assertCount(3, $values);
                $this->assertTrue(in_array(EnumerInt::one, $values->toArray()));
                $this->assertTrue(in_array(EnumerInt::two, $values->toArray()));
                $this->assertTrue(in_array(EnumerInt::three, $values->toArray()));
                break;
        }
    }

    public function testWithNotExistsValues()
    {
        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();


        $dbValue = '1,2,4';
        $values = $class::castUsing(['set', EnumerInt::class])
            ->get(null, 'attribute', $dbValue, []);

        $this->assertCount(2, $values);
        switch (true) {
            case ($this instanceof AsArrayTest):
                $this->assertTrue(in_array(EnumerInt::one, $values));
                $this->assertTrue(in_array(EnumerInt::two, $values));
                break;
            case ($this instanceof AsCollectionTest):
                $this->assertTrue($values->contains(EnumerInt::one));
                $this->assertTrue($values->contains(EnumerInt::two));
                break;
            case ($this instanceof AsArrayObjectTest):
                $this->assertTrue(in_array(EnumerInt::one, $values->toArray()));
                $this->assertTrue(in_array(EnumerInt::two, $values->toArray()));
                break;
        }


        $dbValue = 'one,two,four';
        $values = $class::castUsing(['set', EnumerString::class])
            ->get(null, 'attribute', $dbValue, []);

        $this->assertCount(2, $values);
        switch (true) {
            case ($this instanceof AsArrayTest):
                $this->assertTrue(in_array(EnumerString::one, $values));
                $this->assertTrue(in_array(EnumerString::two, $values));
                break;
            case ($this instanceof AsCollectionTest):
                $this->assertTrue($values->contains(EnumerString::one));
                $this->assertTrue($values->contains(EnumerString::two));
                break;
            case ($this instanceof AsArrayObjectTest):
                $this->assertTrue(in_array(EnumerString::one, $values->toArray()));
                $this->assertTrue(in_array(EnumerString::two, $values->toArray()));
                break;
        }
    }

    public function testWithSpaces()
    {
        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();

        $dbValue = ' 1, 2,  3';
        $values = $class::castUsing(['set', EnumerInt::class])
            ->get(null, 'attribute', $dbValue, []);
        $this->assertCount(3, $values);
        switch (true) {
            case ($this instanceof AsArrayTest):
                $this->assertTrue(in_array(EnumerInt::one, $values));
                $this->assertTrue(in_array(EnumerInt::two, $values));
                $this->assertTrue(in_array(EnumerInt::three, $values));
                break;
            case ($this instanceof AsCollectionTest):
                $this->assertTrue($values->contains(EnumerInt::one));
                $this->assertTrue($values->contains(EnumerInt::two));
                $this->assertTrue($values->contains(EnumerInt::three));
                break;
            case ($this instanceof AsArrayObjectTest):
                $this->assertTrue(in_array(EnumerInt::one, $values->toArray()));
                $this->assertTrue(in_array(EnumerInt::two, $values->toArray()));
                $this->assertTrue(in_array(EnumerInt::three, $values->toArray()));
                break;
        }


        $dbValue = ' one,  two, three  ';
        $values = $class::castUsing(['set', EnumerString::class])
            ->get(null, 'attribute', $dbValue, []);
        $this->assertCount(3, $values);
        switch (true) {
            case ($this instanceof AsArrayTest):
                $this->assertTrue(in_array(EnumerString::one, $values));
                $this->assertTrue(in_array(EnumerString::two, $values));
                $this->assertTrue(in_array(EnumerString::three, $values));
                break;
            case ($this instanceof AsCollectionTest):
                $this->assertTrue($values->contains(EnumerString::one));
                $this->assertTrue($values->contains(EnumerString::two));
                $this->assertTrue($values->contains(EnumerString::three));
                break;
            case ($this instanceof AsArrayObjectTest):
                $this->assertTrue(in_array(EnumerString::one, $values->toArray()));
                $this->assertTrue(in_array(EnumerString::two, $values->toArray()));
                $this->assertTrue(in_array(EnumerString::three, $values->toArray()));
                break;
        }
    }

    public function testWithEmptyValue()
    {
        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();

        $dbValue = '1,,3';
        $values = $class::castUsing(['set', EnumerInt::class])
            ->get(null, 'attribute', $dbValue, []);
        $this->assertCount(2, $values);
        switch (true) {
            case ($this instanceof AsArrayTest):
                $this->assertTrue(in_array(EnumerInt::one, $values));
                $this->assertTrue(in_array(EnumerInt::three, $values));
                break;
            case ($this instanceof AsCollectionTest):
                $this->assertTrue($values->contains(EnumerInt::one));
                $this->assertTrue($values->contains(EnumerInt::three));
                break;
            case ($this instanceof AsArrayObjectTest):
                $this->assertTrue(in_array(EnumerInt::one, $values->toArray()));
                $this->assertTrue(in_array(EnumerInt::three, $values->toArray()));
                break;
        }


        $dbValue = 'one,,three';
        $values = $class::castUsing(['set', EnumerString::class])
            ->get(null, 'attribute', $dbValue, []);
        $this->assertCount(2, $values);
        switch (true) {
            case ($this instanceof AsArrayTest):
                $this->assertTrue(in_array(EnumerString::one, $values));
                $this->assertTrue(in_array(EnumerString::three, $values));
                break;
            case ($this instanceof AsCollectionTest):
                $this->assertTrue($values->contains(EnumerString::one));
                $this->assertTrue($values->contains(EnumerString::three));
                break;
            case ($this instanceof AsArrayObjectTest):
                $this->assertTrue(in_array(EnumerString::one, $values->toArray()));
                $this->assertTrue(in_array(EnumerString::three, $values->toArray()));
                break;
        }
    }

    public function testSet()
    {
        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();

        // test set with int enum
        $value = [1, 2, 3];
        $result = $class::castUsing(['set', EnumerInt::class])
            ->set(null, 'key', $value, []);

        $this->assertIsArray($result);
        $this->assertTrue($result === ['key' => '1,2,3']);

        // test json with int enum
        $result = $class::castUsing(['json', EnumerInt::class])
            ->set(null, 'key', $value, []);

        $this->assertIsArray($result);
        $this->assertJson($result['key']);
        $this->assertTrue($result === ["key" => "[1,2,3]"]);


        // test set with int enum
        $value = ['one', 'two', 'three'];
        $result = $class::castUsing(['set', EnumerString::class])
            ->set(null, 'key', $value, []);

        $this->assertIsArray($result);
        $this->assertTrue($result === ['key' => 'one,two,three']);

        // test json with string enum
        $result = $class::castUsing(['json', EnumerString::class])
            ->set(null, 'key', $value, []);

        $this->assertIsArray($result);
        $this->assertJson($result['key']);
        $this->assertTrue($result === ['key' => '["one","two","three"]']);
    }

    public function testSetWithNotExistsValues()
    {
        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();

        // test int enum with set
        $value = [1, 2, 4];
        $result = $class::castUsing(['set', EnumerInt::class])
            ->set(null, 'key', $value, []);
        $this->assertIsArray($result);
        $this->assertTrue($result === ['key' => '1,2']);

        // test int enum with json
        $result = $class::castUsing(['json', EnumerInt::class])
            ->set(null, 'key', $value, []);
        $this->assertIsArray($result);
        $this->assertJson($result['key']);
        $this->assertTrue($result === ['key' => '[1,2]']);


        // test string enum with set
        $value = ['one', 'two', 'four'];
        $result = $class::castUsing(['set', EnumerString::class])
            ->set(null, 'key', $value, []);
        $this->assertIsArray($result);
        $this->assertTrue($result === ['key' => 'one,two']);

        // test string enum with json
        $result = $class::castUsing(['json', EnumerString::class])
            ->set(null, 'key', $value, []);
        $this->assertIsArray($result);
        $this->assertJson($result['key']);
        $this->assertTrue($result === ['key' => '["one","two"]']);
    }

    public function testSetEmpty()
    {
        /** @var AsArray|AsCollection|AsArrayObject $class */
        $class = $this->getCastClass();
        $value = [];

        // test with set
        $result = $class::castUsing(['set', EnumerInt::class])
            ->set(null, 'key', $value, []);
        $this->assertNull($result);

        // test with json
        $result = $class::castUsing(['json', EnumerInt::class])
            ->set(null, 'key', $value, []);
        $this->assertNull($result);
    }

    public function testSetInvalid()
    {
        $class = $this->getCastClass();
        $value = 'invalid value';

        // test with set
        $result = $class::castUsing(['set', EnumerInt::class])
            ->set(null, 'key', $value, []);
        $this->assertNull($result);

        // test with json
        $result = $class::castUsing(['json', EnumerInt::class])
            ->set(null, 'key', $value, []);
        $this->assertNull($result);
    }



    private function getCastClass(): AsArray|AsCollection|AsArrayObject|string
    {
        switch (true) {
            case ($this instanceof AsCollectionTest):
                $class = AsCollection::class;
                break;
            case ($this instanceof AsArrayObjectTest):
                $class = AsArrayObject::class;
                break;
            default:
                $class = AsArray::class;
                break;
        }
        return $class;
    }
}