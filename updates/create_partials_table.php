<?php namespace Skripteria\Snowflake\Updates;

use Schema;
use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class CreatePartialsTable extends Migration
{
    public function up()
    {
        Schema::create('skripteria_snowflake_partials', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
            $table->string('filename')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('skripteria_snowflake_partials');
    }
}
