<?php

namespace UserFrosting\Sprinkle\Pastries\Database\Migrations\v200;

use UserFrosting\System\Bakery\Migration;
use Illuminate\Database\Schema\Blueprint;
use UserFrosting\Sprinkle\Core\Facades\Seeder;

class PastryTypeTable extends Migration {
    /**
     * {@inheritDoc}
     */
    public function up() {
        if (!$this->schema->hasTable("pastry_type")) {
            $this->schema->create("pastry_type", function(Blueprint $table) {
                $table->increments("id");
                $table->string("description", 32);

                $table->engine = 'InnoDB';
                $table->collation = 'utf8_unicode_ci';
                $table->charset = 'utf8';
            });
        }

        Seeder::execute('DefaultPastryTypes');
    }

    /**
     * {@inheritDoc}
     */
    public function down() {
        $this->schema->drop('pastry_type');
    }
}