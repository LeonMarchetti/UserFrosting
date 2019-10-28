<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v200;

use UserFrosting\System\Bakery\Migration;
use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Facades\Seeder;

class TipoUsuarioTable extends Migration {

    /**
     * {@inheritDoc}
     */
    public function up() {
        if (!$this->schema->hasTable("tipo_usuario")) {
            $this->schema->create("tipo_usuario", function(Blueprint $table) {
                $table->increments("id");
                $table->string("description", 48);

                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
            });
        }

        Seeder::execute('DefaultTiposUsuario');
    }

    /**
     * {@inheritDoc}
     */
    public function down() {
        $this->schema->drop('tipo_usuario');
    }
}