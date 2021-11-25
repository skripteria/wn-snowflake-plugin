<?php namespace Skripteria\Snowflake;

use Backend;
use System\Classes\PluginBase;
use Event;
use Skripteria\Snowflake\Models\Settings;
class Plugin extends PluginBase
{
    public function register()
    {
        Event::listen('cms.template.save', function ( $controller, $templateObject, $type) {
            if ($type != 'page' || $type != 'layout') return;
            // (bool)$templateObject->hasComponent('sf_page') || $templateObject->hasComponent('sf_blueprint');
            parse_snowflake($templateObject, $type);
        });

        $this->registerConsoleCommand('snowflake.sync', 'Skripteria\Snowflake\Console\SyncCommand');
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
        $label = (Settings::get('custom_name'));
        return [
            'snowflake' => [
                'label'       => $label,
                'url'         => Backend::url('skripteria/snowflake/Elements'),
                'icon'        => 'icon-snowflake',
                'permissions' => ['skripteria.snowflake.*'],
                'order'       => 500,
                'sideMenu' => [
                    'pages' => [
                        'label' => 'Pages',
                        'url' => Backend::url('skripteria/snowflake/Elements'),
                        'icon' => 'wn-icon-copy',
                        'permissions' => ['skripteria.snowflake.*'],
                    ],
                    'layouts' => [
                        'label' => 'Layouts',
                        'url' => Backend::url('skripteria/snowflake/ElementsLayouts'),
                        'icon' => 'wn-icon-th-large',
                        'permissions' => ['skripteria.snowflake.*'],
                    ],
                ]
            ],
        ];
    }

    public function registerSettings() {

        $label = (Settings::get('custom_name'));
        return [
            'snowflake' => [
                'label' => $label,
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

    public function pluginDetails()
    {
        $name = "Snowflake";
        return [
            'name'        =>  $name,
            'description' => 'Content Manger for CMS Pages',
            'author'      => 'Skripteria',
            'icon'        => 'icon-snowflake'
        ];
    }

}
