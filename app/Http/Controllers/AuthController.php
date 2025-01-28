<?php

namespace App\Http\Controllers;

use App\Models\Responsavel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

        $responsavel = Responsavel::where('email', $credentials['email'])->first();

        if ($responsavel && Hash::check($credentials['password'], $responsavel->password)) {
            Auth::login($responsavel);
            return redirect()->route('dashboard')->with('success', 'Login realizado com sucesso!');
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
            'email' => 'required|string|email|max:255|unique:responsaveis',
            'cpf' => 'required|string|unique:responsaveis',
            'nome_empresa' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $responsavel = Responsavel::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'cpf' => preg_replace('/[^0-9]/', '', $validated['cpf']), // Remove caracteres não numéricos do CPF
                'nome_empresa' => $validated['nome_empresa'],
                'password' => bcrypt($validated['password']), // Usar bcrypt para hash
                'remember_token' => null,
            ]);

            // Redireciona para o login após registro bem-sucedido
            return redirect()->route('login')
                ->with('success', 'Cadastro realizado com sucesso! Por favor, faça login.');

        } catch (\Exception $e) {
            // Log do erro e retorno amigável
            Log::error('Erro ao registrar responsável: ' . $e->getMessage());
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['error' => 'Erro ao criar conta. Por favor, tente novamente.']);
        }
    }
}
