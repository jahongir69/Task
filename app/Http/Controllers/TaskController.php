<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;

class TaskController extends Controller
{
    // Barcha vazifalarni olish (filtrlar bilan)
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', auth()->id());

        if ($request->has('title')) {
            $tasks->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->has('completed')) {
            $tasks->where('completed', $request->completed);
        }

        if ($request->has('created_at')) {
            $tasks->whereDate('created_at', $request->created_at);
        }

        if ($request->has('sort_by')) {
            $tasks->orderBy('created_at', $request->sort_by);
        }

        return response()->json($tasks->get());
    }

    // Yangi vazifa yaratish
    public function store(TaskStoreRequest $request)
    {
        $task = Task::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'completed' => $request->completed ?? false,
        ]);

        return response()->json([
            'message' => 'Vazifa muvaffaqiyatli yaratildi!',
            'task' => $task
        ], 201);
    }

    // Bitta vazifani ko‘rish
    public function show(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Sizga ruxsat yo‘q!'], 403);
        }

        return response()->json($task);
    }

    // Vazifani yangilash
    public function update(TaskUpdateRequest $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Sizga ruxsat yo‘q!'], 403);
        }

        $task->update($request->validated());

        return response()->json([
            'message' => 'Vazifa yangilandi!',
            'task' => $task
        ]);
    }

    // Vazifani o‘chirish
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Sizga ruxsat yo‘q!'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Vazifa o‘chirildi!']);
    }
}
