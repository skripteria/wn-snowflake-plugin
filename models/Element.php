<?php namespace Skripteria\Snowflake\Models;

use Model;
use Skripteria\Snowflake\Models\Settings;

class Element extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    public $implement = ['@Winter.Translate.Behaviors.TranslatableModel'];

    public $table = 'skripteria_snowflake_elements';

    public $translatable = [
        'content',
        'alt',
    ];

    protected $fillable = ['*'];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $rules = [];

    public $belongsTo = [
        'page' => 'Skripteria\Snowflake\Models\Page',
        'layout' => 'Skripteria\Snowflake\Models\Layout',
        'type' => 'Skripteria\Snowflake\Models\Type',
    ];

    public $attachOne = [
        'image' => 'System\Models\File',
    ];


    public function scopeWithPage($query, $filtered) {
        return $query->whereHas('page', function($q) use ($filtered) {
            $q->where('id', $filtered);
        });
    }
    public function scopeWithLayout($query, $filtered) {
        return $query->whereHas('layout', function($q) use ($filtered) {
            $q->where('id', $filtered);
        });
    }

    public function beforeSave() {
        if ($this->type_id == 2) {
            $baseurl = rtrim(url('/'), '/');
            $this->content = str_replace($baseurl, '', $this->content);
        }
    }
}
