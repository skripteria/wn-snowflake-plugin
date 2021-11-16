<?php namespace Skripteria\Snowflake\Models;

use Model;

/**
 * Element Model
 */
class Element extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'skripteria_snowflake_elements';

    protected $fillable = ['*'];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsTo = [
        'page' => 'Skripteria\Snowflake\Models\Page',
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

    public function beforeSave() {
        if ($this->type_id == 2) {
            $baseurl = rtrim(url('/'), '/');
            $this->content = str_replace($baseurl, '', $this->content);
        }
    }
}
