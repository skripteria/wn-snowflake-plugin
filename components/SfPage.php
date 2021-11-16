<?php namespace Skripteria\Snowflake\Components;

use Cms\Classes\ComponentBase;
use Skripteria\Snowflake\Models\Page;
use Twig\Markup;
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
        return [];
    }

    public function onRun() {
        $page = Page::where('filename', $this->page->baseFileName)->with('elements')->first();

        if (!$page) return;

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
