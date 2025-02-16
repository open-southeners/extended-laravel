<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Support;

use Illuminate\Support\Arr;
use OpenSoutheners\ExtendedLaravel\Tests\TestCase;

class ArrTest extends TestCase
{
    public function test_array_except_values()
    {
        $array = Arr::exceptValues([
            'hello' => 'world',
            'foo' => 'yes',
            'another' => 'world',
            'content' => 'Lorem ipsum dolor',
        ], ['test', 'world']);

        $this->assertCount(2, $array);
        $this->assertEmpty(array_diff($array, [
            'foo' => 'yes',
            'content' => 'Lorem ipsum dolor',
        ]));
    }

    public function test_array_only_values()
    {
        $array = Arr::onlyValues([
            'hello' => 'world',
            'foo' => 'yes',
            'another' => 'world',
            'content' => 'Lorem ipsum dolor',
        ], ['test', 'world']);

        $this->assertCount(2, $array);
        $this->assertEmpty(array_diff($array, [
            'hello' => 'world',
            'another' => 'world',
        ]));
    }

    public function test_array_query_string()
    {
        $originalQueryString = Arr::query([
            'filter' => [
                'hello' => ['world', 'mundo'],
            ],
            'q' => 'hello world',
        ]);

        $queryString = Arr::queryString([
            'filter' => [
                'hello' => ['world', 'mundo'],
            ],
            'q' => 'hello world',
        ]);

        $this->assertNotEquals($queryString, $originalQueryString);
        $this->assertIsString($queryString);
        $this->assertEquals('?filter%5Bhello%5D=world&filter%5Bhello%5D=mundo&q=hello+world', $queryString);
    }
}
