<?php

// In /app/sprinkles/site/src/ServicesProvider/ServicesProvider.php

namespace UserFrosting\Sprinkle\Unlu\ServicesProvider;

class ServicesProvider {
    /**
     * Register extended user fields services.
     *
     * @param Container $container A DI container implementing ArrayAccess and container-interop.
     */
    public function register($container) {
        /**
         * Extend the 'classMapper' service to register model classes.
         *
         * Mappings added: Member
         */
        $container->extend('classMapper', function ($classMapper, $c) {
            $classMapper->setClassMapping("user", "UserFrosting\Sprinkle\Unlu\Database\Models\UsuarioUnlu");

            $classMapper->setClassMapping("integrante", "UserFrosting\Sprinkle\Unlu\Database\Models\IntegrantesVinculacion");
            $classMapper->setClassMapping("peticion", "UserFrosting\Sprinkle\Unlu\Database\Models\Peticion");
            $classMapper->setClassMapping("servicio", "UserFrosting\Sprinkle\Unlu\Database\Models\Servicio");
            $classMapper->setClassMapping("tipo_de_usuario", "UserFrosting\Sprinkle\Unlu\Database\Models\TipoUsuario");
            $classMapper->setClassMapping('vinculacion', 'UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion');

            return $classMapper;
        });
    }
}