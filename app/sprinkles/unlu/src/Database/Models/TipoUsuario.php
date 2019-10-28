<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

class TipoUsuario extends Model {
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = 'tipo_usuario';

    protected $fillable = [
        'description',
    ];

    public function vinculaciones() {

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        // $this->hasMany('App\Comment', 'foreign_key', 'local_key');
        return $this->hasMany($classMapper->getClassMapping('vinculacion'), "tipo_de_usuario", "id");
    }
}