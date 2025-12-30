<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'task_date',
        'deadline',
        'use_progress',
        'progress',
        'status',
    ];

    protected $casts = [
        'use_progress' => 'boolean',
        'task_date' => 'date',
        'deadline' => 'date',
    ];

    public function subTasks(): HasMany
    {
        return $this->hasMany(SubTask::class);
    }

    public function recalculateProgress(): void
    {
        if (!$this->use_progress) {
            $this->update([
                'progress' => 0,
                'status' => 'pending',
            ]);
            return;
        }

        $progress = $this->subTasks()
            ->where('is_done', true)
            ->sum('weight');

        $status = 'pending';

        if ($progress > 0 && $progress < 100) {
            $status = 'on_progress';
        }

        if ($progress >= 100) {
            $progress = 100;
            $status = 'done';
        }

        $this->update([
            'progress' => $progress,
            'status' => $status,
        ]);
    }

    public function isWeightValid(): bool
    {
        return $this->subTasks()->sum('weight') === 100;
    }
}
