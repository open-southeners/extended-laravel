<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Config;

use Illuminate\Support\Collection;
use OpenSoutheners\ExtendedLaravel\Tests\TestCase;

class RepositoryTest extends TestCase
{
    public function testNumericValueOnDifferentTypedNumbers()
    {
        config(['vendor_tests' => [
            'string_number' => '123.45',
            'float_number' => 123.45,
            'integer_number' => 123,
            'nullable_number' => null,
        ]]);

        $this->assertIsString(config()->numeric('vendor_tests.string_number'));
        $this->assertIsFloat(config()->numeric('vendor_tests.float_number'));
        $this->assertIsInt(config()->numeric('vendor_tests.integer_number'));
        $this->assertNull(config()->numeric('vendor_tests.nullable_number'));
    }

    public function testStringish()
    {
        config(['vendor_tests' => [
            'nullable_string' => null,
            'present_string' => 'hello world',
        ]]);

        $this->assertNull(config()->stringish('vendor_tests.nullable_string'));
        $this->assertIsString(config()->stringish('vendor_tests.present_string'));
    }

    public function testIntegerish()
    {
        config(['vendor_tests' => [
            'nullable_integer' => null,
            'present_integer' => 123,
        ]]);

        $this->assertNull(config()->integerish('vendor_tests.nullable_integer'));
        $this->assertIsInt(config()->integerish('vendor_tests.present_integer'));
    }

    public function testFloatish()
    {
        config(['vendor_tests' => [
            'nullable_float' => null,
            'present_float' => 123.45,
        ]]);

        $this->assertNull(config()->floatish('vendor_tests.nullable_float'));
        $this->assertIsFloat(config()->floatish('vendor_tests.present_float'));
    }

    public function testBooleanish()
    {
        config(['vendor_tests' => [
            'nullable_boolean' => null,
            'present_boolean' => true,
        ]]);

        $this->assertNull(config()->booleanish('vendor_tests.nullable_boolean'));
        $this->assertIsBool(config()->booleanish('vendor_tests.present_boolean'));
    }

    public function testArrayish()
    {
        config(['vendor_tests' => [
            'nullable_array' => null,
            'present_array' => ['hello', 'world'],
        ]]);

        $this->assertNull(config()->arrayish('vendor_tests.nullable_array'));
        $this->assertIsArray(config()->arrayish('vendor_tests.present_array'));
    }

    public function testCollectionish()
    {
        config(['vendor_tests' => [
            'nullable_array' => null,
            'present_array' => ['hello', 'world'],
        ]]);

        $this->assertTrue(config()->collectionish('vendor_tests.nullable_array') instanceof Collection);
        $this->assertTrue(config()->collectionish('vendor_tests.nullable_array')->isEmpty());
        $this->assertTrue(config()->collectionish('vendor_tests.present_array') instanceof Collection);
        $this->assertFalse(config()->collectionish('vendor_tests.present_array')->isEmpty());
    }
}
