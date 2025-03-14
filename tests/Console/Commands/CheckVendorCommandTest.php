<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Console\Commands;

use OpenSoutheners\ExtendedLaravel\Tests\TestCase;

class CheckVendorCommandTest extends TestCase
{
    public function testVendorCheckGetsTableWithListOfChangedPublishableFiles()
    {
        $command = $this->artisan('vendor:check');

        $command->assertOk();
    }
}
