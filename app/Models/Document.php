<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer id
 * @property string name
 * @property string content
 * @property integer client_id
 */
class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'content',
    ];

    protected $guarded = [
        'client_id',
    ];
}
