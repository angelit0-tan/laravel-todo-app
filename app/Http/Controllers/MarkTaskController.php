<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Resources\TaskResource;

class MarkTaskController extends Controller
{
/**
     * Mark task as complete
     *
     * @param Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function markedAsComplete(Task $task) {
        $task->completed_at = now(); // Set to now
        $task->task_status = 1;
        $task->save();
        return response()->json(new TaskResource($task));
    }

    /**
     * Mark task as incomplete
     *
     * @param  Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function markedAsIncomplete(Task $task) {
        $task->completed_at = null;
        $task->task_status = 2;
        $task->save();
        return response()->json(new TaskResource($task));
    }

    /**
     * Mark task as archived
     * 
     * @param  Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function markedAsArchived(Task $task) {
        $task->archived_at = now();
        $task->save();
        return response()->json(new TaskResource($task));
    }

    /**
     * Mark task as restore archived
     * 
     * @param  Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function markedRestoreArchived(Task $task) {
        $task->archived_at = null;
        $task->save();
        return response()->json(new TaskResource($task));
    }
}
