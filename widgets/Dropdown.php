<?php namespace Skripteria\Snowflake\Widgets;

use Backend\Classes\WidgetBase;

class Dropdown extends WidgetBase
{
    /**
     * @var constant The default error for empty items list
     */
    const DEFAULT_ERROR = 'No page has been added so far.';

    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'dropdown';

    /**
     * @var string Default error for empty items list
     */
    protected $defaultError = self::DEFAULT_ERROR;

    /**
     * @var integer The first item of index that shows in button
     */
    protected $index = 1;

    /**
     * @var array Cache List of items to show in dropdown
     */
    protected $listItems = [];

    public function __construct($controller, $listItems = [], $defaultError = self::DEFAULT_ERROR)
    {
        parent::__construct($controller);
        $this -> listItems = $listItems;
        $this->defaultError = $defaultError;
    }

    /**
     * Renders the widget.
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('dropdown');
    }

    /**
     * Prepares the view data
     */
    public function prepareVars()
    {
        $this->vars['index'] = $this->getActiveIndex();
        $this->vars['items'] = $this->getListItems();
        $this->vars['error_message'] = $this->defaultError;
    }

    public function onItemChange()
    {
        /*
         * Save or reset dropdown index in session
         */
        $this->setActiveIndex(post('index'));
        // \Skripteria\Snowflake\log(post('index'));
        $widgetId = '#' . $this -> getId();
        $listId = '#' . $this->controller->listGetWidget()->getId();
        $listRefreshData = $this->controller->listRefresh();

        return [
          $listId => $listRefreshData[$listId],
          $widgetId => $this->makePartial('dropdown', ['index' => $this->getActiveIndex(), 'items' => $this->getListItems()])
        ];
    }

    /**
     * Gets the list items array for this widget instance.
     */
    public function getListItems()
    {
        return $this->listItems;
    }

    /**
     * Sets the list items array for this widget instance.
     */
    public function setListItems($listItems)
    {
        $this->listItems = $listItems;
    }

    /**
     * Gets the error message for this widget instance.
     */
    public function getErrorMessage()
    {
        return $this->defaultError;
    }

    /**
     * Sets the error message for this widget instance.
     */
    public function setErrorMessage($message)
    {
        $this->defaultError = $message;
    }

    /**
     * Returns an active index for this widget instance.
     */
    public function getActiveIndex()
    {
        $this->index = $this->getSession('index', 1);
        if (isset($this->listItems[$this->index])) {
            return $this->index;
        } else {
            // return first
            return array_key_first($this->listItems);

        }

        // $this->index = $this->getSession('index', 1);

    }

    /**
     * Sets an active index for this widget instance.
     */
    public function setActiveIndex($index)
    {
        if ($index) {
            $this->putSession('index', $index);
        }
        else {
            $this->resetSession();
        }

        $this->index = $index;
    }

    /**
     * Returns a value suitable for the field name property.
     * @return string
     */
    public function getName()
    {
        return $this->alias . '[index]';
    }
}
