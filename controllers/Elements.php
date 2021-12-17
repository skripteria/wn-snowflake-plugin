<?php

namespace Skripteria\Snowflake\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Skripteria\Snowflake\Classes\EnumFieldType;
use Skripteria\Snowflake\Models\Page;
use Skripteria\Snowflake\Models\Settings;
use Skripteria\Snowflake\Widgets\Dropdown;

/**
 * Elements Back-end Controller
 */
class Elements extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
    ];

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    protected $dropdownWidget;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Skripteria.Snowflake', 'snowflake', 'elements');

        $this->dropdownWidget = new Dropdown($this);
        $this->dropdownWidget->alias = 'pages';
        $this->dropdownWidget->setListItems(Page::lists('filename', 'id'));
        $this->dropdownWidget->bindToController();
    }

    public function listExtendQuery($query)
    {
        $query->withPage($this->dropdownWidget->getActiveIndex());
    }


    public function formExtendFieldsBefore($form)
    {
        $md_mode = 'tab';

        if (Settings::get('markdown_mode')) {
            $md_mode = 'split';
        }

        switch ($form->model->attributes["type_id"]) {
            case EnumFieldType::Text:
                $form->fields = $form->fields + ['content' => ['type' => 'text', 'label' => 'Content', 'span' => 'full']];

                break;
            case EnumFieldType::Link:
                $form->fields = $form->fields + ['content' => ['type' => 'text', 'label' => 'Link', 'span' => 'full']];

                break;
            case EnumFieldType::Image:
                $form->fields = $form->fields + ['image' => ['type' => 'fileupload', 'label' => 'image', 'mode' => 'image', 'span' => 'left', 'useCaption' => false]];
                $form->fields = $form->fields + ['alt' => ['type' => 'text', 'label' => 'Alt Attribute', 'span' => 'left']];


                break;
            case EnumFieldType::Color:
                $form->fields = $form->fields + ['content' => ['type' => 'colorpicker', 'span' => 'left', 'label' => 'Color']];

                break;
            case EnumFieldType::Markdown:
                $form->fields = $form->fields + ['content' => ['type' => 'markdown', 'mode' => $md_mode, 'size' => 'huge']];

                break;
            case EnumFieldType::RichEditor:
                $form->fields = $form->fields + ['content' => ['type' => 'richeditor', 'size' => 'huge']];

                break;
            case EnumFieldType::Code:
                $form->fields = $form->fields + ['content' => ['type' => 'codeeditor', 'size' => 'huge']];

                break;
            case EnumFieldType::Date:
                $form->fields = $form->fields + ['content' => ['type' => 'datepicker', 'mode' => 'date', 'span' => 'left']];

                break;
            case EnumFieldType::Textarea:
                $form->fields = $form->fields + ['content' => ['type' => 'textarea', 'label' => 'Content', 'size' => 'huge']];

                break;
            case EnumFieldType::File:
                $form->fields = $form->fields + ['file' => ['type' => 'fileupload', 'label' => 'file', 'mode' => 'file', 'span' => 'left']];
                $form->fields = $form->fields + ['filename' => ['type' => 'text', 'label' => 'Filename', 'span' => 'left']];

                break;
            case EnumFieldType::MediaImage:
                $form->fields = $form->fields + ['content' => ['type' => 'mediafinder', 'label' => 'Image (Media Manager)', 'mode' => 'image', 'span' => 'left']];
                $form->fields = $form->fields + ['alt' => ['type' => 'text', 'label' => 'Alt Attribute', 'span' => 'left']];
                break;
            case EnumFieldType::MediaFile:
                $form->fields = $form->fields + ['content' => ['type' => 'mediafinder', 'label' => 'File (Media Manager)', 'mode' => 'file', 'span' => 'left']];
                $form->fields = $form->fields + ['filename' => ['type' => 'text', 'label' => 'Filename', 'span' => 'left']];

                break;
        }
    }
}
