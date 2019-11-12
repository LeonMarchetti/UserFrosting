<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v300;

use UserFrosting\Sprinkle\Core\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class ActaTable extends Migration {

    /**
     * {@inheritDoc}
     */
    public function up() {
        if (!$this->schema->hasTable("acta")) {
            $this->schema->create("acta", function(Blueprint $table) {
                $table->increments("id");
                $table->string("ubicacion", 255);

                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down() {
        if ($this->schema->hasTable("acta")) {
            $this->schema->drop("acta");
        }
    }
}