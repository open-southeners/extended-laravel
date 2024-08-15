<?php

namespace OpenSoutheners\ExtendedLaravel\Events;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * This is NOT supposed to be used alone, use the main from Laravel framework
 * instead as is the one this is extending with new methods.
 *
 * @mixin \Illuminate\Events\Dispatcher
 */
class Dispatcher
{
    public function all()
    {
        /**
         * Get all events and listeners.
         *
         * @return array<string>
         */
        return function () {
            $events = [];

            foreach ($this->getRawListeners() as $event => $rawListeners) {
                foreach ($rawListeners as $rawListener) {
                    if (is_string($rawListener)) {
                        $events[$event][] = $rawListener;
                    } elseif ($rawListener instanceof \Closure) {
                        $reflection = new \ReflectionFunction($rawListener);

                        $path = str_replace(base_path(), '', $reflection->getFileName() ?: '');

                        $events[$event][] = 'Closure at: '.$path.':'.$reflection->getStartLine();
                    } elseif (is_array($rawListener) && count($rawListener) === 2) {
                        if (is_object($rawListener[0])) {
                            $rawListener[0] = $rawListener[0]::class;
                        }

                        $events[$event][] = implode('@', $rawListener);
                    }
                }
            }

            return collect($events)->map(fn ($listeners, $event) => ['Event' => $event, 'Listeners' => $listeners])->sortBy('Event')->values()->toArray();
        };
    }

    public function allBy()
    {
        /**
         * Get all events and listeners filtered by event.
         *
         * @param  string  $filter
         * @return array<string>
         */
        return function (string $filter) {
            return Collection::make($this->all())
                ->filter(fn ($item) => str_contains((string) $item['Event'], $filter))
                ->pluck('Listeners')
                ->flatten()
                ->toArray();
        };
    }

    public function removeListener()
    {
        /**
         * Remove listener filtering by event(s).
         *
         * @param  array<string>|string  $remove
         * @param  string|null  $event
         * @return void
         */
        return function ($remove, $event = null) {
            $removeArr = Arr::wrap($remove);

            foreach ($this->listeners as $eventName => $eventListeners) {
                if ($event && ! str_contains($eventName, $event)) {
                    continue;
                }

                $listeners = array_filter($eventListeners, 'is_string');

                foreach ($listeners as $listenerIndex => $listener) {
                    if (! in_array($listener, $removeArr)) {
                        continue;
                    }

                    unset($this->listeners[$eventName][$listenerIndex]);
                }
            }
        };
    }
}
