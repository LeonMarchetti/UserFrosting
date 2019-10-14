<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v100;

use UserFrosting\System\Bakery\Migration;
use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Facades\Seeder;

class PeticionTable extends Migration
{
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Unlu\Database\Migrations\v100\ServicioTable',
        '\UserFrosting\Sprinkle\Unlu\Database\Migrations\v100\VinculacionTable'
    ];
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('peticion')) {
            $this->schema->create('peticion', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamp('fecha_inicio');
                $table->timestamp('fecha_fin');
                $table->text('descripcion');
                $table->text('observaciones');
                $table->integer('id_usuario')->unsigned();
                $table->integer('id_vinculacion')->unsigned()->nullable();
                $table->integer('id_servicio')->unsigned();
                $table->timestamps();

                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';

                $table->foreign('id_usuario')->references('id')->on('users');
                $table->foreign('id_vinculacion')->references('id')->on('vinculacion');
                $table->foreign('id_servicio')->references('id')->on('servicio');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('peticion');
    }
}