<?php

namespace UserFrosting\Sprinkle\Pastries\Database\Migrations\v200;

use UserFrosting\System\Bakery\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdatePastriesTable extends Migration
{

    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Pastries\Database\Migrations\v200\PastryTypeTable'
    ];

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if ($this->schema->hasTable("pastries")) {
            $this->schema->table("pastries", function(Blueprint $table) {
                $table->unsignedInteger("type")
                    ->default(null)
                    ->comment("Tipo de postre")
                    ->nullable();

                $table->foreign('type')->references('id')->on('pastry_type');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        if ($this->schema->hasTable("pastries")) {
            $this->schema->table("pastries", function(Blueprint $table) {
                $table->dropColumn("type");
            });
        }
    }
}