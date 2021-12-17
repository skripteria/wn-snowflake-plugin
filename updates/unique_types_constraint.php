<?php

namespace Skripteria\Snowflake\Updates;

use Schema;
use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class AddTypesConstraint extends Migration
{
    public function up()
    {
        Schema::table('skripteria_snowflake_types', function (Blueprint $table) {
            $table->unique('name');
        });
    }

    public function down()
    {
        Schema::table('skripteria_snowflake_types', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }
}
