<?php

namespace Skripteria\Snowflake;

use Backend\Facades\Backend;
use Skripteria\Snowflake\Classes\SnowflakeParser;
use Skripteria\Snowflake\Models\Settings;
use System\Classes\PluginBase;
use Winter\Storm\Support\Facades\Event;

class Plugin extends PluginBase
{
    public function register()
    {
        Event::listen('cms.template.save', function ($controller, $templateObject, $type) {
            if ($type !== 'page' && $type !== 'layout') {
                return;
            }

            SnowflakeParser::parseSnowflake($templateObject, $type);
        });

        $this->registerConsoleCommand('snowflake.sync', 'Skripteria\Snowflake\Console\SyncCommand');
    }

    public function registerComponents()
    {
        return [
            'Skripteria\Snowflake\Components\SfPage' => 'sf_page',
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
        if (!$label = Settings::get('custom_name')) $label = "Snowflake";
        return [
            'snowflake' => [
                'label'       => $label,
                'url'         => Backend::url('skripteria/snowflake/Elements'),
                'iconSvg'     => 'plugins/skripteria/snowflake/assets/icons/snowflake-blue.svg',
                'permissions' => ['skripteria.snowflake.*'],
                'order'       => 500,
                'sideMenu' => [
                    'pages' => [
                        'label' => 'skripteria.snowflake::lang.plugin.pages',
                        'url' => Backend::url('skripteria/snowflake/Elements'),
                        'icon' => 'wn-icon-copy',
                        'permissions' => ['skripteria.snowflake.*'],
                    ],
                    'layouts' => [
                        'label' => 'skripteria.snowflake::lang.plugin.layouts',
                        'url' => Backend::url('skripteria/snowflake/ElementsLayouts'),
                        'icon' => 'wn-icon-th-large',
                        'permissions' => ['skripteria.snowflake.*'],
                    ],
                ]
            ],
        ];
    }

    public function registerSettings()
    {

        return [
            'snowflake' => [
                'label' => 'Snowflake',
                'description' => 'skripteria.snowflake::lang.plugin.manage_settings',
                'category' => 'system::lang.system.categories.cms',
                'icon'     => 'icon-snowflake-o',
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
                'sf' => function ($cms_key) {
                    return $cms_key;
                },
            ]
        ];
    }

    public function pluginDetails()
    {
        return [
            'name'        => 'Snowflake',
            'description' => 'skripteria.snowflake::lang.plugin.desc',
            'author'      => 'Skripteria',
            'icon'        => 'icon-snowflake-o'
        ];
    }
}
