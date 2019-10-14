<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

class Servicio extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = 'servicio';

    protected $fillable = [
        'denominacion',
        'observaciones'
    ];

    /**
     * @var bool Enable timestamps for this class.
     */
    public $timestamps = true;
}