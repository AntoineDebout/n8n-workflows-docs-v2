<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Index', [
            'users' => User::with(['team', 'role'])
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orderBy($request->sort ?? 'name', $request->direction ?? 'asc')
                ->paginate(10)
                ->withQueryString(),
            'teams' => Team::all(),
            'roles' => UserRole::all(),
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    public function updateUserTeam(Request $request, User $user)
    {
        $validated = $request->validate([
            'team_id' => ['nullable', 'exists:teams,id'],
        ]);

        $user->update($validated);

        return back();
    }

    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'user_role_id' => ['required', 'exists:user_roles,id'],
        ]);

        $user->update($validated);

        return back();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'user_role_id' => ['required', 'exists:user_roles,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'team_id' => $validated['team_id'],
            'user_role_id' => $validated['user_role_id'],
        ]);

        return back();
    }
}
