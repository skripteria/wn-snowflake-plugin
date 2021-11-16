<?php namespace Skripteria\Snowflake\Updates;

use Schema;
use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class CreatePagesTable extends Migration
{
    public function up()
    {
        Schema::create('skripteria_snowflake_pages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
            $table->string('meta_keywords', 512)->nullable();
            $table->string('meta_desc', 512)->nullable();
            $table->string('filename',255)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('skripteria_snowflake_pages');
    }
}
