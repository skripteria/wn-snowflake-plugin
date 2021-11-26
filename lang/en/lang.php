<?php
return [
    'plugin' => [
        'name' => 'Snowflake CMS',
        'desc' => 'Dynamic Content Manager for Winter CMS',
        'use_snowflake' => 'Use Snowflake Plugin',
        'manage_snowflake' => 'Manage Snowflake Plugin',
        'manage_settings' => 'Manage Snowflake Settings',
        'pages' => 'Pages',
        'layouts' => 'Layouts',
    ],
    'settings' => [
        'develop' => 'Development Mode',
        'develop_comment' =>'Allows access to blueprint pages in frontent',
        'clear_url' => 'Clear Image Urls',
        'clear_url_comment' => 'Use original filenames for image and file urls',
        'custom_name' => 'Snowflake Menu Label'
    ],
    'list' => [
        'select_page' => 'Select Page: ',
        'error_no_page' => 'No CMS Pages found in Snowflake, please run a Sync first ( e.g.: php artisan snowflake:sync)',
        'select_layout' => 'Select Layout: ',
        'error_no_page' => 'No CMS Pages found in Snowflake, please run a Sync first ( e.g.: php artisan snowflake:sync)',
        'new_element' => 'New Element',
        'delete_element' => 'Delete selected',
        'manage_elements' => 'Manage Page Content',
        'manage_elements_layouts' => 'Manage Layout Content',
    ],
    'update' => [
        'element' => 'content',
        'save' => 'Save',
        'save_close' => 'Save and Close',
        'cancel' => 'Cancel',
        'elements' => 'Manage content blocks',
        'elements_layouts' => 'Layout Content'
    ],
    'components' => [
        'sf_page_desc' => 'Render Snowflake Content',
    ],

    ];
