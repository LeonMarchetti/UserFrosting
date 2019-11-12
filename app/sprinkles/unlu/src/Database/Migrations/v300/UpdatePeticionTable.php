<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v300;

use UserFrosting\Sprinkle\Core\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdatePeticionTable extends Migration {

    /**
     * {@inheritDoc}
     */
    public function up() {
        if ($this->schema->hasTable("peticion")) {
            $this->schema->table("peticion", function(Blueprint $table) {
                $table->boolean("aprobada")->default(false);
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down() {
        if ($this->schema->hasTable("peticion")) {
            $this->schema->table("peticion", function(Blueprint $table) {
                $table->dropColumn("aprobada");
            });
        }
    }
}