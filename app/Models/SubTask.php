<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubTask extends Model
{
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'task_id',
        'title',
        'weight',
        'is_done',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    protected static function booted()
    {
        static::saved(function (SubTask $subTask) {
            $subTask->task?->recalculateProgress();
        });

        static::deleted(function (SubTask $subTask) {
            $subTask->task?->recalculateProgress();
        });
    }
}
