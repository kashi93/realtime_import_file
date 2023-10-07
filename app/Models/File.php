<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $appends = [
        "updated_date",
        "updated_diff"
    ];

    public function getUpdatedDateAttribute()
    {
        return $this->updated_at->format('Y-m-d g:i A');
    }

    public function getUpdatedDiffAttribute()
    {
        return $this->updated_at->diffForHumans();
    }
}
