<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v100;

use UserFrosting\System\Bakery\Migration;
use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Facades\Seeder;

class UsuarioUnluTable extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('usuario_unlu')) {
            $this->schema->create('usuario_unlu', function (Blueprint $table) {
                $table->integer('id')->unsigned();
                $table->string('telefono', 32);
                $table->string('institucion', 32);
                $table->string('dependencia', 32);
                $table->string('rol', 32);
                $table->boolean('activo');
                $table->timestamps();

                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';

                $table->primary('id');
                $table->foreign('id')->references('id')->on('users');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('usuario_unlu');
    }
}