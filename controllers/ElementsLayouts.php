<?php namespace Skripteria\Snowflake\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Skripteria\Snowflake\Widgets\Dropdown;
use Skripteria\Snowflake\Models\Layout;

/**
 * Elements Back-end Controller
 */
class ElementsLayouts extends Controller
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
        $this->dropdownWidget->alias = 'layouts';
        $this->dropdownWidget->setListItems(Layout::lists( 'filename', 'id'));
        $this->dropdownWidget->bindToController();

    }

    public function listExtendQuery($query)
    {
        $query->withLayout($this->dropdownWidget->getActiveIndex());
    }


    public function formExtendFields($form)
    {
        switch($form->getField('type')->value) {
            case 1:
                $form->addFields(['content' => ['type' => 'text', 'label' => 'Content', 'span' => 'left']]);
            break;
            case 2:
                $form->addFields(['content' => ['type' => 'text', 'label' => 'Link', 'span' => 'left']]);
            break;
            case 3:
                $form->addFields(['image' => ['type' => 'fileupload', 'label' => 'image','mode' => 'image', 'span' => 'left']]);
                $form->addFields(['alt' => ['type' => 'text', 'label' => 'Alt Attribute', 'span' => 'left']]);
            break;
            case 4:
                $form->addFields(['content' => ['type' => 'colorpicker', 'span' => 'left', 'label' => 'Color']]);
            break;
            case 5:
                $form->addFields(['content' => ['type' => 'markdown', 'size' => 'huge']]);
            break;
            case 6:
                $form->addFields(['content' => ['type' => 'richeditor', 'size' => 'huge']]);
            break;
            case 7:
                $form->addFields(['content' => ['type' => 'codeeditor', 'size' => 'huge']]);
            break;
            case 8:
                $form->addFields(['content' => ['type' => 'datepicker', 'mode' => 'date', 'span' => 'left']]);
            break;
            case 9:
                $form->addFields(['content' => ['type' => 'textarea', 'label' => 'Content', 'size' => 'huge']]);
            break;
        }
    }
}
