<?php

namespace Skripteria\Snowflake\Classes;

use Illuminate\Support\Facades\DB;
use Skripteria\Snowflake\Classes\EnumFieldType;
use Skripteria\Snowflake\Models\Element;
use Skripteria\Snowflake\Models\Layout;
use Skripteria\Snowflake\Models\Page;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\Str;

class SnowflakeParser
{
    public static function parseSnowflake($templateObject, $objectType, $cleanup = false)
    {
        $content = $templateObject->markup;

        $pattern = "|\{{2}\s*(\w*)\s*.*sf\(([^\|]*)\).*\}{2}|";
        preg_match_all($pattern, $content, $matches);

        $tags = [];
        $sf_alt_keys = ['__alt', '__name'];

        foreach ($matches[1] as $k => $v) {

            $param_string = $matches[2][$k] . ",";

            // refactor empty arguments for correct parsing
            $param_string = str_replace("''", "' '", $param_string);
            $param_string = str_replace("\"\"", "\" \"", $param_string);

            $pattern = "|[\"\'](.[^,]*)[\'\"]|";
            preg_match_all($pattern, $param_string, $submatches);
            $params = $submatches[1];

            $sf_key = $v;

            // skip if Sf Key ends with __alt or __name
            if (Str::endsWith($sf_key, $sf_alt_keys)) {
                continue;
            }

            if (count($params) === 0 || strlen($sf_key) === 0) {
                throw new ApplicationException('Snowflake: invalid tag: {{ ' . $sf_key . '}} (on page: ' . $templateObject->getFilename() . ').');
            }

            $tags[$sf_key] = ['type' => '', 'desc' => '', 'default' => ''];

            // Process each argument passsed to sf filter
            if (isset($params[0])) {
                $tags[$sf_key]['type'] = $params[0];
            }

            if (isset($params[1])) {

                $ignore_default = ['image','file','date','mediaimage','mediafile'];

                if (!  in_array($tags[$sf_key]['type'], $ignore_default)) {
                    $tags[$sf_key]['default'] = $params[1];
                }
            }

            if (isset($params[2])) {
                $tags[$sf_key]['desc'] = $params[2];
            }
        }
        self::syncDb($tags, $templateObject, $objectType, $cleanup);
    }

    public static function syncDb($tags, $templateObject, $objectType, $cleanup)
    {
        if (count($tags) === 0) {
            return;
        }

        $filename = $templateObject->getBaseFileName();
        $types_raw = Db::table('skripteria_snowflake_types')->get();

        switch ($objectType) {
            case 'page':
                $sfpage = Db::table('skripteria_snowflake_pages')->where('filename', $filename)->first();

                if (!$sfpage) {
                    $sfpage = new Page();
                    $sfpage->filename = $filename;
                    $sfpage->save();
                }

                $elements = Db::table('skripteria_snowflake_elements')->where('page_id', $sfpage->id)->get();

                break;
            case 'layout':
                $sfpage = Db::table('skripteria_snowflake_layouts')->where('filename', $filename)->first();

                if (!$sfpage) {
                    $sfpage = new Layout();
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

        foreach ($elements as $element) {
            $db_array[$element->cms_key]['type_id'] = $element->type_id;
            $db_array[$element->cms_key]['desc'] = $element->desc;
            $db_array[$element->cms_key]['id'] = $element->id;

            // Clean up unused database records
            if (!isset($tags[$element->cms_key])) {
                $el = Element::find($db_array[$element->cms_key]['id']);

                if (($el->type_id != EnumFieldType::Image && empty($el->content)) || ($el->type_id == EnumFieldType::Image && empty($el->image->path)) || $cleanup) {
                    $el->delete();
                } else {
                    $el->in_use = 0;
                    $el->order = 9999;
                    $el->save();
                }
            }
        }

        $order = 1;

        foreach ($tags as $sf_key => $value) {
            if (!isset($types[$value['type']])) {
                $pname = $templateObject->getFilename();

                throw new ApplicationException('Snowflake: type \'' . $value['type'] . '\' is not supported (page: ' . $pname . ') supported types are: text, link, image, color, markdown, richeditor, code, date, textarea, file.');

                continue;
            }

            if (isset($db_array[$sf_key])) {
                // Update
                $el = Element::find($db_array[$sf_key]['id']);
                $el->type_id = $types[$value['type']];
                $el->in_use = 1;
                $el->order = $order;
                $el->save();
            } else {
                // Insert
                $el = new Element();
                $el->type = $types[$value['type']];
                $el->order = $order;
                $el->desc = $value['desc'];
                $el->content = $value['default'];

                switch ($objectType) {
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
}
