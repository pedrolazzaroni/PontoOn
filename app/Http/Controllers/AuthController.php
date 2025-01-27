<?php

namespace App\Http\Controllers;

use App\Models\Responsavel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Remove the constructor with middleware

    public function showAuthForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.auth');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/')->with('success', 'Login realizado com sucesso!');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')
                        ->with('success', 'Você foi desconectado com sucesso.');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => 'required|string|unique:responsaveis',
            'nome_empresa' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Criar o responsável primeiro
        $responsavel = Responsavel::create([
            'name' => $validated['name'],
            'cpf' => $validated['cpf'],
            'email' => $validated['email'],
            'nome_empresa' => $validated['nome_empresa'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('login')
                        ->with('success', 'Cadastro realizado com sucesso! Por favor, faça login.');
    }
}
