<?php
namespace Skripteria\Snowflake;
use Db;
use Winter\Storm\Support\Facades\Flash;
use Winter\Storm\Exception\ApplicationException;

use Skripteria\Snowflake\Models\Page;
use Skripteria\Snowflake\Models\Layout;
use Skripteria\Snowflake\Models\Element;

function log($message) {
    \Log::info($message);
}

function parse_snowflake($templateObject, $objectType, $cleanup = false) {
    $content = $templateObject->markup;

    preg_match_all('|{{(...*)}}|U', $content, $op);
    $tags = [];

    foreach ($op[1] as $key=>$value) {

        $checkval = str_replace(' ','', $value);

        if (strpos($checkval, '|sf(')) {
            $pattern = "|([\w\.\ ]+)|";
            preg_match_all($pattern, $value, $matches);

            $matches = $matches[0];
            foreach ($matches as $mkey=>$match) {
                $matches[$mkey] = $match = trim($match, ' ');
                if (empty($match) || $match == 'sf') unset($matches[$mkey]);
            }

            $i = 0;
            $desc = '';

            foreach($matches as $val) {
                switch ($i) {
                    case 0:
                        $key = explode('.', $val)[0];
                    break;
                    case 1:
                        $tagType = $val;
                    break;
                    case 2:
                        $desc = $val;
                }
            $i++;
            }
            if (count($matches) > 3 || count($matches) < 2) {
                log("Warning: Snowflake expects 1-2 parameters, ". count($matches)-1 . " detected: Tag {{ $value }}  ,page: ".$templateObject->getFilename());

            } else {
                if (! strlen($tagType)) throw new ApplicationException("Snowflake: invalid tag: {{ $value }} (on page: ".$templateObject->getFilename().")");
                $tags[$key]['type'] = $tagType;
                $tags[$key]['desc'] = $desc;
            }
        }
    }
    sync_db($tags, $templateObject, $objectType, $cleanup);
}

function sync_db($tags, $templateObject, $objectType, $cleanup) {

    log ($tags);

    $filename = $templateObject->getBaseFileName();
    $types_raw = Db::table('skripteria_snowflake_types')->get();

    switch ($objectType) {
        case "page" :
            $sfpage = Db::table('skripteria_snowflake_pages')->where('filename', $filename)->first();
            if (! $sfpage) {
                $sfpage = new Page;
                $sfpage->filename = $filename;
                $sfpage->save();
            }
            $elements = Db::table('skripteria_snowflake_elements')->where('page_id', $sfpage->id)->get();
            break;

        case "layout" :
            $sfpage = Db::table('skripteria_snowflake_layouts')->where('filename', $filename)->first();
            if (! $sfpage) {
                $sfpage = new Layout;
                $sfpage->filename = $filename;
                $sfpage->save();
            }
            $elements = Db::table('skripteria_snowflake_elements')->where('layout_id', $sfpage->id)->get();
            break;

        default:
        return;

    }

    $types = [];

    foreach ($types_raw as $type) {
        $types[$type->name] = $type->id;
    }

    $db_array = [];

    foreach($elements as $element){
        $db_array[$element->cms_key]['type_id'] = $element->type_id;
        $db_array[$element->cms_key]['desc'] = $element->desc;
        $db_array[$element->cms_key]['id'] = $element->id;

        // Clean up unused database records
        if (! isset($tags[$element->cms_key])) {
            $el = Element::find($db_array[$element->cms_key]["id"]);
            if (($el->type_id != 3 && empty($el->content)) || ($el->type_id == 3 && empty($el->image->path)) || $cleanup) {
                $el->delete();
            } else {
                $el->in_use = 0;
                $el->save();
            }
        }
    }

    foreach ($tags as $cms_key =>$value) {
        if (! isset($types[$value['type']])) {

            $pname = $templateObject->getFilename();
            throw new ApplicationException("Snowflake: type '".$value['type']."' is not supported (page: $pname)

            supported are: text markdown html code link color date image");
            Flash::warning('test');

            continue;
        }

        if (isset($db_array[$cms_key])) {
            // Update
            $el = Element::find($db_array[$cms_key]["id"]);
            $el->type_id = $types[$value["type"]];
            $el->desc = $value["desc"];
            $el->in_use = 1;
            $el->save();
        } else {
            //Insert
            $el = new Element();
            $el->type = $types[$value["type"]];
            $el->desc = $value["desc"];
            switch($objectType) {
                case 'page':
                    $el->page_id = $sfpage->id;
                break;
                case 'layout':
                    $el->layout_id = $sfpage->id;
                break;
            }
            $el->cms_key = $cms_key;
            $el->save();
        }

    }
}
