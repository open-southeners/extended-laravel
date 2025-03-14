<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenSoutheners\ExtendedLaravel\Casts\ByteUnit;

class File extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['size'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts()
    {
        return [
            'size' => ByteUnit::class,
        ];
    }
}
