<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index() {
        return view('planner.index');
    }

    // 2. Tüm görevleri JSON olarak ver (JavaScript buraya soracak)
    public function getTasks() {
        return response()->json(
            Task::where('user_id', auth()->id())->get()
        );
    }

    public function store(Request $request) {
        $task = Task::create([
            'user_id'    => auth()->id(),
            'title'      => $request->title,
            'start_date' => $request->startDate,
            'end_date'   => $request->endDate,
            'color'      => $request->color,
        ]);
        return response()->json($task);
    }

    public function destroy($id) {
        Task::where('id', $id)->where('user_id', auth()->id())->delete();
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id) {
        $task = Task::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $task->update([
            'title'      => $request->title,
            'start_date' => $request->startDate,
            'end_date'   => $request->endDate,
            'color'      => $request->color,
        ]);
        return response()->json($task);
    }

}
