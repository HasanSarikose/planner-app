<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Task;
use App\Models\Note;

class AccountController extends Controller
{
    public function info()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'name'       => $user->name,
            'created_at' => $user->created_at ? $user->created_at->format('d M Y') : '-',
            'task_count' => Task::count(), // user_id yok, tüm taskları say
            'note_count' => Note::where('user_id', $user->id)->count(),
            'done_count' => Note::where('user_id', $user->id)->where('done', true)->count(),
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Mevcut şifre hatalı!'], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['success' => true, 'message' => 'Şifre başarıyla güncellendi!']);
    }
}
