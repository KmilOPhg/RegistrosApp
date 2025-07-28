<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|object
     */
    public function showLoginForm() {
        return view('vista_login.login');
    }

    /**
     * Maneja el intento de login
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request) {
        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            //Mensajes personalizados con un segundo arrray
            'email.required' => 'El campo email es obligatorio',
            'email.email' => 'Por favor, introduce un correo electrónico válido',
            'password.required' => 'La contraseña es obligatoria'
        ]);

        //Validamos el login
        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();

            return redirect()->intended('/registros');
        }

        //Retornamos a la vista de login de nuevo si las credenciales fueron incorrectas
        return back()->withErrors([
            'email' => 'Las credenciales son incorrectas',
        ])->onlyInput('email'); //Mantenemos el email
    }

    /**
     * Manejar el logout
     *
     * @param Request $request
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|object
     */
    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
