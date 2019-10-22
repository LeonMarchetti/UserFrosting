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
}