<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubTask;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Str;

class SubTaskController extends Controller
{

    public function index(Request $request, $taskId)
    {
        try {
            $subTasks = SubTask::with('task')
                ->where('task_id', $taskId)
                ->orderByDesc('created_at')
                ->get();

            return response()->json($subTasks);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Gagal mengambil data subtask',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, $taskId)
    {
        $request->validate([
            'title' => 'required|string',
            'weight' => 'required|integer|min:1|max:100',
        ]);

        DB::beginTransaction();

        try {
            $task = Task::findOrFail($taskId);

            if (!$task->use_progress) {
                return response()->json([
                    'message' => 'Task ini tidak menggunakan progress'
                ], 422);
            }

            $totalWeight = $task->subTasks()->sum('weight') + $request->weight;
            if ($totalWeight > 100) {
                return response()->json([
                    'message' => 'Total bobot subtask tidak boleh lebih dari 100%'
                ], 422);
            }

            $subTask = SubTask::create([
                'id' => Str::uuid(),
                'task_id' => $task->id,
                'title' => $request->title,
                'weight' => $request->weight,
                'is_done' => false,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Subtask berhasil ditambahkan',
                'data' => $subTask
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menambahkan subtask',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string',
            'weight' => 'sometimes|required|integer|min:1|max:100',
            'is_done' => 'sometimes|required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $subTask = SubTask::findOrFail($id);
            $task = $subTask->task;

            if (isset($request->weight)) {
                $totalWeight = $task->subTasks()
                    ->where('id', '!=', $subTask->id)
                    ->sum('weight') + $request->weight;

                if ($totalWeight > 100) {
                    return response()->json([
                        'message' => 'Total bobot subtask tidak boleh lebih dari 100%'
                    ], 422);
                }
            }

            $subTask->update($request->only([
                'title',
                'weight',
                'is_done',
            ]));

            DB::commit();

            return response()->json([
                'message' => 'Subtask berhasil diperbarui',
                'data' => $subTask
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal update subtask',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $subTask = SubTask::findOrFail($id);
            $task = $subTask->task;

            $subTask->delete();

            DB::commit();

            return response()->json([
                'message' => 'Subtask berhasil dihapus'
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menghapus subtask',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
