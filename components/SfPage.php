<?php namespace Skripteria\Snowflake\Components;

use Cms\Classes\ComponentBase;
use Skripteria\Snowflake\Models\Page;
use Skripteria\Snowflake\Models\Layout;
use Cms\Classes\Page as CmsPage;
use Winter\Storm\Support\Facades\Markdown;

class SfPage extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'SF Page Component',
            'description' => 'Manage Content in Snowflake Cms',
            'icon' => 'icon-snowflake'
        ];
    }

    public function defineProperties()
    {
        return [
            'blueprints' => [
                'title' => 'Blueprint Pages',
                'type' => 'dropdown',
                'default' => 'imperial',
                'placeholder' => 'no Blueprint Page',
                ]
            ];
    }

    public function getBlueprintsOptions() {
        // $blueprints = Page::getClassMethods();
        $pages = CmsPage::all();
        $op = [];
        foreach ($pages as $page) {
            if ($page->hasComponent('blueprint_page')) {
                $op[] = [$page->basefilename => $page->title];
            }
            \Log::info($op);
        }

         return $op;

    }

    public function onRun() {
        $page = Page::where('filename', $this->page->baseFileName)->with('elements')->first();
        $layout = $this->page->layout->baseFileName;
        // dump($layout);

        // dump($this->page);

        if (!$page) return;
        $layout = Layout::where('filename', $this->page->layout->baseFileName)->with('elements')->first();
        // dump($layout->elements);
        // $this->page["testkey"] = "Testkey Layout";
        // $this->page["testkey2"] = "Testkey auf layoutebene";
        if ($layout) $page->elements = $page->elements->merge($layout->elements);

        foreach ($page->elements as $element) {
            switch ($element->type_id) {
                case 3:
                    if ($element->image) {
                        $img = [
                            'path' => $element->image->getPath(),
                            'alt' => $element->alt
                        ];
                    } else {
                        $img = [
                            'path' =>'',
                            'alt' => $element->alt
                        ];
                    }
                    $this->page[$element->cms_key] = $img;
                break;

                case 5:
                    $this->page[$element->cms_key] = Markdown::parse($element->content);
                break;

                default:
                    $this->page[$element->cms_key] = $element->content;
            }

        }

    }
}
