<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\SubTask;
use Illuminate\Support\Str;

class SubTaskSeeder extends Seeder
{
    public function run(): void
    {
        $taskReact = Task::where('title', 'Belajar React + TypeScript')->first();
        $taskBackend = Task::where('title', 'Backend NearTask Laravel')->first();

        if (!$taskReact || !$taskBackend) {
            return;
        }

        SubTask::insert([
            [
                'id' => Str::uuid(),
                'task_id' => $taskReact->id,
                'title' => 'Belajar JSX & Component',
                'is_done' => true,
                'weight' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'task_id' => $taskReact->id,
                'title' => 'Belajar State & Props',
                'is_done' => false,
                'weight' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'task_id' => $taskReact->id,
                'title' => 'Belajar Hooks',
                'is_done' => false,
                'weight' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'task_id' => $taskBackend->id,
                'title' => 'Buat Migration & Model',
                'is_done' => true,
                'weight' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'task_id' => $taskBackend->id,
                'title' => 'Buat Controller & API',
                'is_done' => false,
                'weight' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
