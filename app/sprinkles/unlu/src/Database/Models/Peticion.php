<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

class Peticion extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = 'peticion';

    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'observaciones',
        'id_usuario',
        'id_vinculacion',
        'id_servicio'
    ];

    public function servicio() {
        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping("servicio"), "id_servicio", "id");
    }

    /**
     * @var bool Enable timestamps for this class.
     */
    public $timestamps = true;
}