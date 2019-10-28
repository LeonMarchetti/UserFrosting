<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v200;

use UserFrosting\System\Bakery\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateVinculacionTable extends Migration {

    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Unlu\Database\Migrations\v200\TipoUsuarioTable'
    ];

    /**
     * {@inheritDoc}
     */
    public function up() {
        if ($this->schema->hasTable("vinculacion")) {
            $this->schema->table("vinculacion", function(Blueprint $table) {
                $table->dropColumn("tipo_de_usuario");
            });
        }

        if ($this->schema->hasTable("vinculacion")) {
            $this->schema->table("vinculacion", function(Blueprint $table) {
                $table->unsignedInteger("tipo_de_usuario")
                    ->default(null)
                    ->comment("Tipo de usuario")
                    ->nullable();

                $table->foreign('tipo_de_usuario')->references('id')->on('tipo_usuario');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down() {
        if ($this->schema->hasTable("vinculacion")) {
            $this->schema->table("vinculacion", function(Blueprint $table) {
                $table->dropColumn("tipo_de_usuario");
            });
        }

        if ($this->schema->hasTable("vinculacion")) {
            $this->schema->table("vinculacion", function(Blueprint $table) {
                $table->enum('tipo_de_usuario', [
                    "Grupo de investigación",
                    "Doctorando/Estudiante trabajo final",
                    "Docente/Act. Académica",
                    "Grupo de extensión",
                    "Personal T., A. y M."
                ]);
            });
        }
    }
}