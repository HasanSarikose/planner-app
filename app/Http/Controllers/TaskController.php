<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index() {
        return view('planner');
    }

    // 2. Tüm görevleri JSON olarak ver (JavaScript buraya soracak)
    public function getTasks() {
        return response()->json(Task::all());
    }

    // 3. Yeni görev kaydet
    public function store(Request $request) {
        $task = Task::create([
            'title' => $request->title,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'color' => $request->color,
        ]);
        return response()->json($task);
    }

    // 4. Görev sil
    public function destroy($id) {
        Task::destroy($id);
        return response()->json(['success' => true]);
    }

    // 5. Görev Güncelle (Düzenle)
    public function update(Request $request, $id) {
        $task = Task::find($id);
        if($task) {
            $task->update([
                'title' => $request->title,
                'start_date' => $request->startDate,
                'end_date' => $request->endDate,
                'color' => $request->color,
            ]);
            return response()->json($task);
        }
        return response()->json(['success' => false], 404);
    }

}
