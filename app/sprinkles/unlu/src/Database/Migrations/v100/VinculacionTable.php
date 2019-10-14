<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v100;

use UserFrosting\System\Bakery\Migration;
use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Facades\Seeder;

class VinculacionTable extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('vinculacion')) {
            $this->schema->create('vinculacion', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamp('fecha_solicitud');
                $table->timestamp('fecha_fin');
                $table->integer('id_solicitante')->unsigned();
                $table->string('responsable', 32)->nullable();
                $table->string('cargo', 32)->nullable();
                $table->enum('tipo_de_usuario', [
                    "Grupo de investigación",
                    "Doctorando/Estudiante trabajo final",
                    "Docente/Act. Académica",
                    "Grupo de extensión",
                    "Personal T., A. y M."
                ]);
                $table->string('actividad', 32);
                $table->string('telefono', 32);
                $table->string('correo', 32);
                $table->text('descripcion');
                $table->timestamps();

                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';

                $table->foreign('id_solicitante')->references('id')->on('users');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('vinculacion');
    }
}