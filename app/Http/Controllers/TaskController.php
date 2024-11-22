<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Classes\TaskSearch;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    private $search;

    public function __construct(TaskSearch $search)
    {
        $this->search = $search;
    }

    /**
     * Show lists of tasks
     * 
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = $request->items_per_page ?? 12; // Default is 12
        $sortBy = $request->sort_by ?? null;
        $sortDirection = $request->sort_direction ?? null;
        $dataType = $request->data_type ?? 0;

        $tasks = Auth::user()->tasks()
                ->when($dataType == 1, function ($query) { // archived
                    return $query->whereNotNull('archived_at');
                })
                ->when($dataType == 2, function ($query) { // completed
                    return $query->where('task_status', 1);
                })
                ->when($dataType == 3, function ($query) { // todo
                    return $query->where('task_status', 2);
                })
                ->when($sortBy && $sortDirection, function($query) use ($sortBy, $sortDirection) {
                    return $query->orderBy($sortBy, $sortDirection);
                })
                ->taskSearch($this->search)
                ->latest()
                ->paginate($perPage);

        return response()->json(TaskResource::collection($tasks)->response()->getData(true));
    }

    /**
     * 
     * Add new task
     */
    public function store(TaskRequest $request)
    {
        $query = new Task;
        $task = $query->create([
            'title'       => $request->title,
            'description' => $request->description,
            'due_date'    => $request->due_date ?? null,
            'task_priority' => $request->task_priority ?? 0,
            'task_order' => $query->newTaskOrder(),
            'user_id'    => Auth::user()->id
        ]);

        // Save tags
        if ($request->tags) {
            $task->tags()->sync($request->tags);
        }

        // Save attachments
        if ($request->attachments) {
            $task->attachments()->sync($request->attachments);
        }

        return response()->json(new TaskResource($task));
    }

    /**
     * Display single task
     * Check if logged-in user is the owner of the task
     */
    public function show(TaskRequest $request, Task $task)
    {
        return response()->json(new TaskResource($task));
    }

    /**
     * Update task
     * Check if logged-in user is the owner of the task
     */
    public function update(TaskRequest $request, Task $task)
    {
        $task->update($request->all());

        // Save tags
        if ($request->tags) {
            $task->tags()->sync($request->tags);
        }

        // Save attachments
        if ($request->attachments) {
            $task->attachments()->sync($request->attachments);
        }
        
        return response()->json(new TaskResource($task));
    }

    /**
     * Delete task
     * Check if logged-in user is the owner of the task
     */
    public function destroy(TaskRequest $request, Task $task)
    {
        $task->delete();
        return response()->noContent();
    }
}
