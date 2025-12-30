<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class TaskController extends Controller
{
    public function index()
    {
        try {
            $tasks = Task::with('subTasks')
                ->orderByDesc('created_at')
                ->get();

            return response()->json($tasks);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Gagal mengambil data task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'task_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:task_date',
            'use_progress' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $task = Task::create([
                'id' => Str::uuid(),
                'title' => $request->title,
                'description' => $request->description,
                'task_date' => $request->task_date,
                'deadline' => $request->deadline,
                'use_progress' => $request->use_progress,
                'progress' => 0,
                'status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Task berhasil dibuat',
                'data' => $task
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal membuat task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $task = Task::with('subTasks')
                ->findOrFail($id);

            return response()->json($task);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Task tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'task_date' => 'sometimes|required|date',
            'deadline' => 'sometimes|required|date|after_or_equal:task_date',
            'use_progress' => 'sometimes|required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $task = Task::findOrFail($id);

            $task->update($request->only([
                'title',
                'description',
                'task_date',
                'deadline',
                'use_progress',
            ]));

            if (!$task->use_progress) {
                $task->subTasks()->delete();
                $task->recalculateProgress();
            }

            DB::commit();

            return response()->json([
                'message' => 'Task berhasil diperbarui',
                'data' => $task
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal update task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $task = Task::findOrFail($id);
            $task->delete();

            DB::commit();

            return response()->json([
                'message' => 'Task berhasil dihapus'
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menghapus task',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
