<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::insert([
            [
                'id' => Str::uuid(),
                'title' => 'Belajar React + TypeScript',
                'description' => 'Mempelajari React dasar dengan TypeScript',
                'task_date' => Carbon::now()->toDateString(),
                'deadline' => Carbon::now()->addDays(7),
                'use_progress' => true,
                'progress' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Backend NearTask Laravel',
                'description' => 'Membangun REST API NearTask',
                'task_date' => Carbon::now()->toDateString(),
                'deadline' => Carbon::now()->addDays(10),
                'use_progress' => true,
                'progress' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Deploy NearTask',
                'description' => 'Deploy frontend dan backend',
                'task_date' => Carbon::now()->toDateString(),
                'deadline' => Carbon::now()->addDays(14),
                'use_progress' => false,
                'progress' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
