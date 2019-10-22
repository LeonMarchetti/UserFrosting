<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v100;

use UserFrosting\System\Bakery\Migration;
use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Facades\Seeder;

class IntegrantesVinculacionTable extends Migration
{
    /**
     * {@inheritDoc}
     */
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Unlu\Database\Migrations\v100\VinculacionTable'
    ];

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('integrantes_vinculacion')) {
            $this->schema->create('integrantes_vinculacion', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_vinculacion')->unsigned();
                $table->integer('id_usuario')->unsigned()->nullable();
                $table->string('nombre', 32);
                $table->timestamps();

                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';

                $table->foreign('id_usuario')->references('id')->on('users');
                $table->foreign('id_vinculacion')->references('id')->on('vinculacion');
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('integrantes_vinculacion');
    }
}