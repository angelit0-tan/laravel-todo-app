<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Http\Classes\TaskSearch;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'task_priority',
        'task_order',
        'user_id',
        'task_status',
        'completed_at',
        'archived_at'
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    // This will get the max task order
    public function newTaskOrder() {
        return static::query()->count() + 1;
    }

    // Get tags
    public function tags() : BelongsToMany {
        return $this->belongsToMany(Tag::class);
    } 

    // Get attachments
    public function attachments() : BelongsToMany {
        return $this->belongsToMany(Attachment::class);
    } 

    /**
     * Task search
     *
     * @param  Builder $query 
     * @param  TaskSearch $search
     *
     * @return Builder
     */
    public function scopeTaskSearch(Builder $query, TaskSearch $search) {
        $search->apply($query);
    }

    // getters
    public function getCreatedAtAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function getCompletedAtAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d');
    }
}
