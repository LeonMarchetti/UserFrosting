<?php

// In /app/sprinkles/site/src/ServicesProvider/ServicesProvider.php

namespace UserFrosting\Sprinkle\Pastries\ServicesProvider;

class ServicesProvider
{
    /**
     * Register extended user fields services.
     *
     * @param Container $container A DI container implementing ArrayAccess and container-interop.
     */
    public function register($container)
    {
        /**
         * Extend the 'classMapper' service to register model classes.
         *
         * Mappings added: Member
         */
        $container->extend('classMapper', function ($classMapper, $c) {
            $classMapper->setClassMapping('pastry', 'UserFrosting\Sprinkle\Pastries\Database\Models\Pastry');
            $classMapper->setClassMapping("pastry_type", "UserFrosting\Sprinkle\Pastries\Database\Models\PastryType");
            return $classMapper;
        });
    }
}