<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NoteController extends Controller
{
    public function index() {
        return response()->json(
            Note::where('user_id', auth()->id())
                ->orderBy('done')
                ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function store(Request $request) {
        $note = Note::create([
            'user_id' => auth()->id(),
            'text' => $request->text,
            'priority' => $request->priority ?? 'medium',
            'done' => false,
        ]);
        return response()->json($note);
    }

    public function toggle($id) {
        $note = Note::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $note->update(['done' => !$note->done]);
        return response()->json($note);
    }

    public function destroy($id) {
        Note::where('id', $id)->where('user_id', auth()->id())->delete();
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id) {
        $note = Note::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $note->update([
            'description' => $request->description,
        ]);
        return response()->json($note);
    }
}
