<?php namespace Skripteria\Snowflake\Updates;

use Schema;
use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class CreateLayoutsTable extends Migration
{
    public function up()
    {
        Schema::create('skripteria_snowflake_layouts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
            $table->string('filename')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('skripteria_snowflake_layouts');
    }
}
