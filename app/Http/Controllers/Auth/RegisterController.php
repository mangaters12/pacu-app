<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    // Validator untuk registrasi via web
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:user,admin,penjual'],
            // Validasi field alamat, lat, dan long
            'alamat' => ['nullable', 'string'],
            'lat' => ['nullable', 'numeric'],
            'long' => ['nullable', 'numeric'],
        ]);
    }

    // Pembuatan user dari data input
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'alamat' => $data['alamat'] ?? null,
            'lat' => $data['lat'] ?? null,
            'long' => $data['long'] ?? null,
        ]);
    }

    // Method API Register
    public function apiRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:user,admin,penjual'],
            // Validasi field alamat, lat, dan long
            'alamat' => ['nullable', 'string'],
            'lat' => ['nullable', 'numeric'],
            'long' => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'alamat' => $request->input('alamat'),
            'lat' => $request->input('lat'),
            'long' => $request->input('long'),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }
}
