<?php

namespace UserFrosting\Sprinkle\Pastries\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

class PastryType extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = 'pastry_type';

    protected $fillable = [
        'description',
    ];

    public function pastries() {

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        // $this->hasMany('App\Comment', 'foreign_key', 'local_key');
        return $this->hasMany($classMapper->getClassMapping('pastry'), "type", "id");
        // return $this->hasMany("UserFrosting\Sprinkle\Pastries\Database\Models\Pastry");
    }
}