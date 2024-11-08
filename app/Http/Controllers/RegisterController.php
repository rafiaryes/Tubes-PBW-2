<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        return view('register.index', [
            'title' => 'Register',
            'active' => 'register',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'username' => ['required', 'min:6', 'unique:users'],
            'email'=> 'required|email:dns|unique:users',
            'password' => 'required|min:6|max:255'
        ]);

        dd('registrasi berhasil');
    }
}
