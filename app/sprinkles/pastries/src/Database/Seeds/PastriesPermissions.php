<?php

namespace UserFrosting\Sprinkle\Pastries\Database\Seeds;

use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;
use UserFrosting\Sprinkle\Account\Database\Models\Permission;

class PastriesPermissions extends BaseSeed
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->validateMigrationDependencies([
            '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\RolesTable',
            '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\PermissionsTable'
        ]);

        foreach ($this->pastryPermissions() as $permissionInfo) {
            $permission = new Permission($permissionInfo);
            $permission->save();
        }
    }

    protected function pastryPermissions()
    {
        return [
            [
                'slug' => 'see_pastries',
                'name' => 'See the pastries page',
                'conditions' => 'always()',
                'description' => 'Enables the user to see the pastries page'
            ],
            [
                'slug' => 'see_pastry_origin',
                'name' => 'See pastry origin',
                'conditions' => 'always()',
                'description' => 'Allows the user to see the origin of a pastry'
            ]
        ];
    }
}
