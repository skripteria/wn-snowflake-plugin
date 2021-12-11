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

    preg_match_all('|\{{2}.*\s*(\w+)\s*\|.*sf\((.*)\).*\}{2}|U', $content, $matches);
    $tags = [];

    foreach($matches[1] as $k=>$v) {
        $param_string = explode('|',$matches[2][$k])[0];
        log ($param_string);
        log($v);
        $param_string = str_replace(['\'','\"'], '', $param_string);
        $param_string = trim($param_string, ' ');
        $params = explode(',', $param_string);

        $sf_key = $v;

        // Skip if Sf Key ends with _alt or _name
        if (substr($sf_key, -5) == "__alt" || substr($sf_key, -6) == "__name") continue;

        if (count($params) == 0 || strlen($sf_key) == 0) {
            throw new ApplicationException("Snowflake: invalid tag: {{ $sf_key }} (on page: ".$templateObject->getFilename().")");
        }
        $tags[$sf_key] = ['type'=>'','desc'=>'','default'=>''];
        foreach ($params as $i=>$param) {
            switch ($i) {
                case 0:
                    $tags[$sf_key]['type'] = $param;
                    break;
                case 1:
                    if ($tags[$sf_key]['type'] != 'image' && $tags[$sf_key]['type'] != 'file')
                    $tags[$sf_key]['default'] = $param;
                case 2:
                    $tags[$sf_key]['desc'] = $param;
                    break;
                break;
            }
        }
    }

    sync_db($tags, $templateObject, $objectType, $cleanup);
}

function sync_db($tags, $templateObject, $objectType, $cleanup) {

    if (count($tags) == 0) return;

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
                $el->order = 9999;
                $el->save();
            }
        }
    }

    $order = 1;

    foreach ($tags as $sf_key=>$value) {
        if (! isset($types[$value['type']])) {

            $pname = $templateObject->getFilename();
            throw new ApplicationException("Snowflake: type '".$value['type']."' is not supported (page: $pname)
            supported are: text markdown richeditor code link color date image textarea file");

            continue;
        }

        if (isset($db_array[$sf_key])) {
            // Update
            $el = Element::find($db_array[$sf_key]["id"]);
            $el->type_id = $types[$value["type"]];
            // $el->desc = $value["desc"];
            $el->in_use = 1;
            $el->order = $order;
            $el->save();
        } else {
            //Insert
            $el = new Element();
            $el->type = $types[$value["type"]];
            $el->order = $order;
            $el->desc = $value["desc"];
            $el->content = $value["default"];
            switch($objectType) {
                case 'page':
                    $el->page_id = $sfpage->id;
                break;
                case 'layout':
                    $el->layout_id = $sfpage->id;
                break;
            }
            $el->cms_key = $sf_key;
            $el->save();
        }
        $order++;

    }
}
