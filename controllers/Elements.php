<?php namespace Skripteria\Snowflake\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Skripteria\Snowflake\Widgets\Dropdown;
use Skripteria\Snowflake\Models\Page;

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
        $this->dropdownWidget->setListItems(Page::lists( 'filename', 'id'));
        $this->dropdownWidget->bindToController();

    }

    public function listExtendQuery($query)
    {
        $query->withPage($this->dropdownWidget->getActiveIndex());
    }


    public function formExtendFieldsBefore($form)
    {

        switch($form->model->attributes["type_id"]) {
            case 1:
                $form->fields = $form->fields + ['content' => ['type' => 'text', 'label' => 'Content', 'span' => 'left']];
            break;
            case 2:
                $form->fields = $form->fields + ['content' => ['type' => 'text', 'label' => 'Link', 'span' => 'left']];
            break;
            case 3:
                $form->fields = $form->fields + ['image' => ['type' => 'fileupload', 'label' => 'image','mode' => 'image', 'span' => 'left']];
                $form->fields = $form->fields + ['alt' => ['type' => 'text', 'label' => 'Alt Attribute', 'span' => 'left']];
            break;
            case 4:
                $form->fields = $form->fields + ['content' => ['type' => 'colorpicker', 'span' => 'left', 'label' => 'Color']];
            break;
            case 5:
                $form->fields = $form->fields + ['content' => ['type' => 'markdown', 'size' => 'huge']];
            break;
            case 6:
                $form->fields = $form->fields + ['content' => ['type' => 'richeditor', 'size' => 'huge']];
            break;
            case 7:
                $form->fields = $form->fields + ['content' => ['type' => 'codeeditor', 'size' => 'huge']];
            break;
            case 8:
                $form->fields = $form->fields + ['content' => ['type' => 'datepicker', 'mode' => 'date', 'span' => 'left']];
            break;
            case 9:
                $form->fields = $form->fields + ['content' => ['type' => 'textarea', 'label' => 'Content', 'size' => 'huge']];
            break;
        }
    }

    
}
