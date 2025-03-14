<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Events;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use OpenSoutheners\ExtendedLaravel\Tests\TestCase;
use Workbench\App\Events\FileCreating;
use Workbench\App\Events\FileUpdating;
use Workbench\App\Listeners\MeasureFileSize;

class DispatcherTest extends TestCase
{
    public function testAllEventListenersGetsArray()
    {
        $eventListeners = app(Dispatcher::class)->all();

        $this->assertIsArray($eventListeners);
        $this->assertNotEmpty($eventListeners);

        $fileCreatingEvent = array_values(
            array_filter($eventListeners, fn ($item) => $item['Event'] === FileCreating::class)
        )[0] ?? null;

        $this->assertIsArray($fileCreatingEvent);
        $this->assertNotEmpty($fileCreatingEvent['Listeners']);
        $this->assertEquals(MeasureFileSize::class, $fileCreatingEvent['Listeners'][0]);
    }

    public function testAllByEventListenersGetsArray()
    {
        $eventListeners = app(Dispatcher::class)->allBy(FileCreating::class);

        $this->assertIsArray($eventListeners);
        $this->assertNotEmpty($eventListeners);
        $this->assertEquals(MeasureFileSize::class, $eventListeners[0]);
    }

    public function testRemoveListenerFromAllEventsExcludesEventListenerFromAllEvents()
    {
        $eventListeners = app(Dispatcher::class)->allBy(FileCreating::class);

        $this->assertIsArray($eventListeners);
        $this->assertNotEmpty($eventListeners);
        $this->assertEquals(MeasureFileSize::class, $eventListeners[0]);

        $eventListeners = app(Dispatcher::class)->removeListener(MeasureFileSize::class);

        $emptyEventListeners = app(Dispatcher::class)->allBy(FileCreating::class);

        $this->assertIsArray($emptyEventListeners);
        $this->assertEmpty($emptyEventListeners);

        $emptyEventListeners = app(Dispatcher::class)->allBy(FileUpdating::class);
        $this->assertIsArray($emptyEventListeners);
        $this->assertEmpty($emptyEventListeners);
    }

    public function testRemoveListenerFromASpecificEventsExcludesEventListenerFromEvent()
    {
        $eventListeners = app(Dispatcher::class)->allBy(FileCreating::class);

        $this->assertIsArray($eventListeners);
        $this->assertNotEmpty($eventListeners);
        $this->assertEquals(MeasureFileSize::class, $eventListeners[0]);

        $eventListeners = app(Dispatcher::class)->removeListener(MeasureFileSize::class, FileCreating::class);

        $emptyEventListeners = app(Dispatcher::class)->allBy(FileCreating::class);

        $this->assertIsArray($emptyEventListeners);
        $this->assertEmpty($emptyEventListeners);

        $emptyEventListeners = app(Dispatcher::class)->allBy(FileUpdating::class);

        $this->assertIsArray($emptyEventListeners);
        $this->assertNotEmpty($emptyEventListeners);
        $this->assertEquals(MeasureFileSize::class, $emptyEventListeners[0]);
    }
}
