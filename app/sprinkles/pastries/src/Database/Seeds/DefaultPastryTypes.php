<?php

namespace UserFrosting\Sprinkle\Pastries\Database\Seeds;

use UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed;
use UserFrosting\Sprinkle\Pastries\Database\Models\PastryType;

class DefaultPastryTypes extends BaseSeed
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        foreach ($this->pastryTypes() as $pastryType) {
            $pastryType = new PastryType($pastryType);
            $pastryType->save();
        }
    }

    protected function pastryTypes()
    {
        return [
            [ 'description' => 'Viennoiserie' ],
            [ 'description' => 'Pastry' ],
            [ 'description' => 'Dessert' ]
        ];
    }
}