<?php
namespace Skripteria\Snowflake\Updates;

use Skripteria\Snowflake\Models\Type;
use Winter\Storm\Database\Updates\Seeder;

class seedTypes extends Seeder {

    public function run()
    {
        Type::create(['name' => 'text', 'id' => 1]);
        Type::create(['name' => 'link', 'id' => 2]);
        Type::create(['name' => 'image', 'id' => 3]);
        Type::create(['name' => 'color', 'id' => 4]);
        Type::create(['name' => 'markdown', 'id' => 5]);
        Type::create(['name' => 'richeditor', 'id' => 6]);
        Type::create(['name' => 'code', 'id' => 7]);
        Type::create(['name' => 'date', 'id' => 8]);
        Type::create(['name' => 'textarea', 'id' => 9]);
        Type::create(['name' => 'file', 'id' => 10]);
    }
}
