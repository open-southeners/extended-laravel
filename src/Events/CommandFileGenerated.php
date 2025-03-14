<?php

namespace OpenSoutheners\ExtendedLaravel\Events;

class CommandFileGenerated
{
    public function __construct(public string $filePath)
    {
        //
    }
}
