<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

class IntegrantesVinculacion extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = 'integrantes_vinculacion';

    protected $fillable = [
        'id_vinculacion',
        'id_usuario',
        'nombre'
    ];

    /**
     * @var bool Enable timestamps for this class.
     */
    public $timestamps = true;
}