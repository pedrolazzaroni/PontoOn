<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showAuthForm()
    {
        return view('auth.auth'); // Retorna a nova view consolidada
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->remember)) {
            return redirect('/')->with('success', 'Login realizado com sucesso!');
        }

        return redirect('auth')->with('error', 'As credenciais fornecidas não correspondem aos nossos registros.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('auth');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if($user) {
            return redirect()->route('auth')
                           ->with('success', 'Cadastro realizado com sucesso! Por favor, faça login.');
        }

        return back()->with('error', 'Erro ao realizar o cadastro. Tente novamente.');
    }
}
