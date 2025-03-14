<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Console\Commands;

use Illuminate\Support\Facades\Event;
use OpenSoutheners\ExtendedLaravel\Events\CommandFileGenerated;
use OpenSoutheners\ExtendedLaravel\Tests\TestCase;

class ClassMakeCommandTest extends TestCase
{
    public function testMakeClassCommandTriesToOpenAFileOnTheConfiguredCodeEditor()
    {
        putenv('APP_IDE=vscode');

        Event::fake(CommandFileGenerated::class);

        $command = $this->artisan('make:class TestClass');

        $command->assertOk();

        $command->run();

        Event::assertDispatched(fn (CommandFileGenerated $event) => $event->filePath === app_path('TestClass.php'));
    }
}
