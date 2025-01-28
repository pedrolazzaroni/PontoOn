<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::where('responsavel_id', auth()->id())->get();
        return view('admin.dashboard', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'responsavel_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user)
    {
        // Verificar se o usuário pertence ao responsável
        if ($user->responsavel_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        // Verificar se o usuário pertence ao responsável
        if ($user->responsavel_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Não autorizado');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Usuário atualizado com sucesso!');
    }

    public function toggleStatus(User $user)
    {
        // Verificar se o usuário pertence ao responsável
        if ($user->responsavel_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->status = !$user->status;
        $user->save();

        return response()->json(['success' => true]);
    }
}
