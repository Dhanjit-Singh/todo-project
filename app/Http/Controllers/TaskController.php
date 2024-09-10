<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function Index(){
        $tasks = Task::orderBy('id', 'DESC')->get();
        return view('index', compact('tasks'));
    }

    public function store(Request $request){
    $request->validate([
        'name' => 'required|unique:tasks,name',
    ]);

    $task = Task::create(['name' => $request->name]);
    return response()->json(['success' => true, 'message' => 'Task added successfully.', 'task' => $task]);
}

    public function update(Request $request, Task $task){
        $task->update(['is_completed' => !$task->is_completed]);
        return response()->json(['success' => true, 'message' => 'Task status updated.']);
    }

    public function destroy(Task $task){
        $task->delete();
        return response()->json(['success' => true, 'message' => 'Task deleted successfully.']);
    }

}
