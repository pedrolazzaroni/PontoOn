<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('responsavel_id', auth()->id());

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status !== '') {
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
                'status' => $user->status
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
            'password' => 'nullable|string|min:8'
        ]);

        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email']
        ];

        if (!empty($validated['password'])) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        $user->update($dataToUpdate);

        return redirect()->back()->with('success', 'Usuário atualizado com sucesso!');
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
