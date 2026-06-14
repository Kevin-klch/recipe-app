<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->is_admin, 403);

        $users = User::latest()->get();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->is_admin, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Profil wurde angelegt.');
    }

    public function destroy(User $user)
    {
        abort_unless(auth()->user()->is_admin, 403);

        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'user' => 'Du kannst dein eigenes Profil nicht löschen.',
            ]);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Profil wurde gelöscht.');
    }
}