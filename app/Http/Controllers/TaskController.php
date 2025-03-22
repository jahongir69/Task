<?php


namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;

class TaskController extends Controller
{
    protected function sendResponse($result, $message)
    {
        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => $message
        ], 200);
    }

    protected function sendError($message, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }

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

        return $this->sendResponse($tasks->get(), 'Tasks retrieved successfully.');
    }

    public function store(TaskStoreRequest $request)
    {
        $task = Task::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'completed' => $request->completed ?? false,
        ]);

        return $this->sendResponse($task, 'Task created successfully.');
    }

    public function show(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return $this->sendError('Unauthorized access!', 403);
        }

        return $this->sendResponse($task, 'Task retrieved successfully.');
    }

    public function update(TaskUpdateRequest $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return $this->sendError('Unauthorized access!', 403);
        }

        $task->update($request->validated());

        return $this->sendResponse($task, 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return $this->sendError('Unauthorized access!', 403);
        }

        $task->delete();

        return $this->sendResponse([], 'Task deleted successfully.');
    }
}
