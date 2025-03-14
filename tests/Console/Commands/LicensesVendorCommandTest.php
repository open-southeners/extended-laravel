<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Console\Commands;

use OpenSoutheners\ExtendedLaravel\Tests\TestCase;

class LicensesVendorCommandTest extends TestCase
{
    public function testVendorLicensesGetsJsonFormatListOfComposerDependenciesWithLicenses()
    {
        $command = $this->artisan('vendor:licenses');

        $command->assertOk();
    }
}
