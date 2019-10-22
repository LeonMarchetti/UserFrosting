<?php

namespace UserFrosting\Sprinkle\Pastries\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

class Pastry extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = 'pastries';

    protected $fillable = [
        'name',
        'description',
        'origin'
    ];

    public function type_2() {

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = static::$ci->classMapper;

        return $this->belongsTo($classMapper->getClassMapping("pastry_type"));
        // return $this->belongsTo("UserFrosting\Sprinkle\Pastries\Database\Models\PastryType");
    }

    /**
     * @var bool Enable timestamps for this class.
     */
    public $timestamps = true;
}