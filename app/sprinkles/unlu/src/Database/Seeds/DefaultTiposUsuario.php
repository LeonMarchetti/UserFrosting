<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Seeds;

use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;
use UserFrosting\Sprinkle\Unlu\Database\Models\TipoUsuario;

class DefaultTiposUsuario extends BaseSeed {
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        foreach ($this->tiposUsuario() as $tipo_usuario) {
            $tipo_usuario = new TipoUsuario($tipo_usuario);
            $tipo_usuario->save();
        }
    }

    protected function tiposUsuario() {
        return [
            [ 'description' => 'Grupo de investigación' ],
            [ 'description' => 'Doctorando/Estudiante trabajo final' ],
            [ 'description' => 'Docente/Act. Académica' ],
            [ 'description' => 'Grupo de extensión' ],
            [ 'description' => 'Personal T., A. y M.' ]
        ];
    }
}