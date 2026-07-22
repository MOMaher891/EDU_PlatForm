<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'min:6', 'max:15', 'regex:/^[0-9]{6,15}$/'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'phone.regex' => 'رقم الهاتف يجب أن يحتوي على أرقام فقط بين 6 و 15 رقم.',
            'phone.min' => 'رقم الهاتف قصير جداً.',
            'phone.max' => 'رقم الهاتف طويل جداً (الحد الأقصى 15 رقم).',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $studentRole = \App\Models\Role::where('slug', 'student')->first();
        $cleanPhone = isset($data['phone']) ? preg_replace('/[^0-9]/', '', (string) $data['phone']) : null;

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $cleanPhone ?: null,
            'country_code' => $data['country_code'] ?? '+20',
            'password' => Hash::make($data['password']),
            'role' => 'student',
            'role_id' => $studentRole?->id,
        ]);
    }
}
