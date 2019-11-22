<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Seeds;

use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;
use UserFrosting\Sprinkle\Account\Database\Models\Permission;

class UnluPermissions extends BaseSeed {

    /**
     * {@inheritDoc}
     */
    public function run() {
        $this->validateMigrationDependencies([
            '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\RolesTable',
            '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\PermissionsTable'
        ]);

        foreach ($this->unluPermissions() as $permissionInfo) {
            $permission = new Permission($permissionInfo);
            $permission->save();
        }
    }

    protected function unluPermissions() {
        return [
            [
                'slug' => 'usuario_unlu',
                'name' => 'Ver la página de la UNLu',
                'conditions' => 'always()',
                'description' => 'Permite al usuario ver la parte de la UNLu del sitio'
            ],
            [
                'slug' => 'admin_unlu',
                'name' => 'Administrador UNLu',
                'conditions' => 'always()',
                'description' => 'Permite acceder a funcionalidad avanzada de la parte de la UNLu del sitio'
            ]
        ];
    }
}
