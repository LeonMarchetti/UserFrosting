<?php

namespace UserFrosting\Sprinkle\Unlu\Database\Migrations\v100;

use UserFrosting\System\Bakery\Migration;
use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Facades\Seeder;

class ServicioTable extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('servicio')) {
            $this->schema->create('servicio', function (Blueprint $table) {
                $table->increments('id');
                $table->text('denominacion');
                $table->text('observaciones');

                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('servicio');
    }
}