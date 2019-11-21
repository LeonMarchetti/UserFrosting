<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

class Vinculacion extends Model {
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = 'vinculacion';

    protected $fillable = [
        'id_solicitante',
        'fecha_solicitud',
        'fecha_fin',
        'responsable',
        'cargo',
        'tipo_de_usuario',
        'actividad',
        'telefono',
        'correo',
        'descripcion',
        'id_acta'
    ];

    public function tipo_usuario() {

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping("tipo_de_usuario"), "tipo_de_usuario", "id");
    }

    public function solicitante() {
        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping("user"), "id_solicitante", "id");
    }

    public function integrantes() {
        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->hasMany($classMapper->getClassMapping("integrante"), "id_vinculacion", "id");
    }

    /**
     * @var bool Enable timestamps for this class.
     */
    public $timestamps = true;
}