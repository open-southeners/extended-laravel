<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class UuidModel extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['uuid'];
}
