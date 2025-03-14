<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Support;

use Illuminate\Support\Facades\Storage;
use OpenSoutheners\ByteUnitConverter\ByteUnitConverter;
use OpenSoutheners\ExtendedLaravel\Tests\TestCase;

class StorageTest extends TestCase
{
    public function test_storage_human_size()
    {
        Storage::put('human_size_test.txt', 'test');

        $this->assertInstanceOf(ByteUnitConverter::class, Storage::humanSize('human_size_test.txt'));
    }
}
