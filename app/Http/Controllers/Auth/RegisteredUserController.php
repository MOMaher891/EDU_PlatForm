<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('phone')) {
            $request->merge([
                'phone' => preg_replace('/[^0-9]/', '', (string) $request->phone)
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'min:6', 'max:15', 'regex:/^[0-9]{6,15}$/'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.regex' => 'رقم الهاتف يجب أن يحتوي على أرقام فقط بين 6 و 15 رقم.',
            'phone.min' => 'رقم الهاتف قصير جداً.',
            'phone.max' => 'رقم الهاتف طويل جداً (الحد الأقصى 15 رقم).',
        ]);

        $studentRole = \App\Models\Role::where('slug', 'student')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?: null,
            'country_code' => $request->country_code ?? '+20',
            'password' => Hash::make($request->password),
            'role' => 'student',
            'role_id' => $studentRole?->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
