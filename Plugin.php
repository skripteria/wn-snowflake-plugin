<?php namespace Skripteria\Snowflake;

use Backend;
use System\Classes\PluginBase;
use Event;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'Snowflake CMS',
            'description' => 'Content Manger for CMS Pages',
            'author'      => 'Skripteria',
            'icon'        => 'icon-snowflake'
        ];
    }

    public function register()
    {
        Event::listen('cms.template.save', function ( $controller, $templateObject, $type) {
            $test = (bool)$templateObject->hasComponent('sf_page') || $templateObject->hasComponent('sf_blueprint');
            parse_snowflake($templateObject);
        });

        $this->registerConsoleCommand('snowflake.sync', 'Skripteria\Snowflake\Console\SyncCommand');
    }

    public function boot()
    {

    }

    public function registerComponents()
    {
        return [
            'Skripteria\Snowflake\Components\SfPage' => 'sf_page',
            'Skripteria\Snowflake\Components\BlueprintPage' => 'blueprint_page',
        ];
    }

    public function registerPermissions()
    {
        return [
            'skripteria.snowflake.use_snowflake' => [
                'tab' => 'Snowflake',
                'label' => 'skripteria.snowflake::lang.plugin.use_snowflake',
            ],
            'skripteria.snowflake.manage_snowflake' => [
                'tab' => 'Snowflake',
                'label' => 'skripteria.snowflake::lang.plugin.manage_snowflake',
            ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'snowflake' => [
                'label'       => 'Snowflake CMS',
                'url'         => Backend::url('skripteria/snowflake/Elements'),
                'icon'        => 'icon-snowflake',
                'permissions' => ['skripteria.snowflake.*'],
                'order'       => 500,
            ],
        ];
    }

    public function registerSettings() {
        return [
            'snowflake' => [
                'label' => 'skripteria.snowflake::lang.plugin.name',
                'description' => 'skripteria.snowflake::lang.plugin.manage_settings',
                'category' => 'system::lang.system.categories.cms',
                'icon' => 'icon-snowflake',
                'class' => 'Skripteria\Snowflake\Models\Settings',
                'order' => 500,
                'keywords' => 'snowflake',
                'permissions' => ['skripteria.snowflake.manage_snowflake'],
            ],

        ];
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'sf' => function ($cms_key){
                    return $cms_key;
                },
                ]
            ];
    }


}
