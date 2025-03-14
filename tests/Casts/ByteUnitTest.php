<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Casts;

use OpenSoutheners\ExtendedLaravel\Tests\TestCase;
use Workbench\App\Models\File;

class ByteUnitTest extends TestCase
{
    public function testFileModelGetSizeAttributeCastedToByteUnitObject()
    {
        $file = new File(['size' => 1000]);

        $this->assertIsString($file->size);

        $this->assertEquals('1 KB', $file->size);
    }
}
