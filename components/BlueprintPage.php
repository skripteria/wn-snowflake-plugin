<?php namespace Skripteria\Snowflake\Components;

use Cms\Classes\ComponentBase;
use Skripteria\Snowflake\Models\Settings;
use Illuminate\Support\Facades\Redirect;

class BlueprintPage extends SfPage
{
    public function componentDetails()
    {
        return [
            'name'        => 'BlueprintPage Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun() {
        if (!(bool)Settings::get('develop')) return Redirect::to('404');
        parent::onRun();
    }
}
