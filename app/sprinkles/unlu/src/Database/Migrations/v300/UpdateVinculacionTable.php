<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v300;

use UserFrosting\Sprinkle\Core\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateVinculacionTable extends Migration {

    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Unlu\Database\Migrations\v300\ActaTable'
    ];

    /**
     * {@inheritDoc}
     */
    public function up() {
        if ($this->schema->hasTable("vinculacion")) {
            $this->schema->table("vinculacion", function(Blueprint $table) {
                $table->unsignedInteger("id_acta")
                    ->default(null)
                    ->comment("Acta de aprobación")
                    ->nullable();

                $table->foreign('id_acta')->references('id')->on('acta');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down() {
        if ($this->schema->hasTable("vinculacion")) {
            $this->schema->table("vinculacion", function(Blueprint $table) {
                $table->dropColumn("id_acta");
            });
        }
    }
}