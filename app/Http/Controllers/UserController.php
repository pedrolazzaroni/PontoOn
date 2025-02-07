<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return view('admin.usuarios', compact('users'));
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
        try {
            if ($user->responsavel_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'expediente' => $user->expediente
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar dados do usuário'], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        if ($user->responsavel_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Não autorizado');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'expediente' => 'required|integer|min:1|max:24'
        ]);

        try {
            $dataToUpdate = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'expediente' => $validated['expediente']
            ];

            if (!empty($validated['password'])) {
                $dataToUpdate['password'] = Hash::make($validated['password']);
            }

            $user->update($dataToUpdate);

            return redirect()->back()->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erro ao atualizar usuário.');
        }
    }

    public function toggleStatus(User $user)
    {
        if ($user->responsavel_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->status = !$user->status;
        $user->save();

        return response()->json(['success' => true]);
    }
}
