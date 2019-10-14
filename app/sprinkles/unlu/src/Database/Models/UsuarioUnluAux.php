<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

class UsuarioUnluAux extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = 'usuario_unlu';

    protected $fillable = [
        'telefono',
        'institucion',
        'dependencia',
        'rol',
        'activo'
    ];

    /**
     * @var bool Enable timestamps for this class.
     */
    public $timestamps = true;
}