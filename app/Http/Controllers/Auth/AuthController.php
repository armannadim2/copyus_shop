<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Admin\NewUserRegisteredNotification;
use App\Rules\FiscalIdentity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Login Form
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | Handle Login
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt authentication
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => __('auth.failed'),
                ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Route based on role
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.index'));
        }

        if ($user->role === 'pending') {
            return redirect()->route('pending');
        }

        if ($user->role === 'rejected') {
            Auth::logout();
            return redirect()->route('rejected');
        }

        if ($user->role === 'approved') {
            return redirect()->intended(route('products.index'));
        }

        // Unknown role fallback
        Auth::logout();
        return redirect()->route('login')
            ->withErrors(['email' => 'Compte no autoritzat.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Show Register Form
    |--------------------------------------------------------------------------
    */

    public function showRegister()
    {
        return view('auth.register');
    }

    /*
    |--------------------------------------------------------------------------
    | Handle Registration
    |--------------------------------------------------------------------------
    */

    public function register(Request $request)
    {
        // CIF/VAT is required when the user enters a company name
        // or explicitly requests a business invoice.
        $requiresInvoice = $request->boolean('requires_invoice');
        $hasCompany      = filled($request->input('company_name'));
        $cifRequired     = $requiresInvoice || $hasCompany;

        $cifRules = $cifRequired
            ? ['required', 'string', 'max:30', 'unique:users,cif', new FiscalIdentity]
            : ['nullable', 'string', 'max:30', 'unique:users,cif', new FiscalIdentity];

        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'         => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'company_name'     => ['nullable', 'string', 'max:255'],
            'cif'              => $cifRules,
            'requires_invoice' => ['nullable', 'boolean'],
            'phone'            => ['nullable', 'string', 'max:30'],
            'address'          => ['nullable', 'string', 'max:255'],
            'city'             => ['nullable', 'string', 'max:100'],
            'postal_code'      => ['nullable', 'string', 'max:20'],
            'country'          => ['nullable', 'string', 'max:5'],
        ]);

        $user = User::create([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'password'         => Hash::make($validated['password']),
            'company_name'     => $validated['company_name'] ?? null,
            'cif'              => $validated['cif'] ?? null,
            'requires_invoice' => $requiresInvoice,
            'phone'            => $validated['phone'] ?? null,
            'address'          => $validated['address'] ?? null,
            'city'             => $validated['city'] ?? null,
            'postal_code'      => $validated['postal_code'] ?? null,
            'country'          => $validated['country'] ?? 'ES',
            'role'             => 'pending',
            'locale'           => app()->getLocale(),
        ]);

        // Notify admins of new registration
        User::admins()->get()
            ->each(fn($admin) => $admin->notify(new NewUserRegisteredNotification($user)));

        // Log the user in immediately after registration
        Auth::login($user);

        $request->session()->regenerate();

        // Redirect to pending approval page
        return redirect()->route('pending');
    }

    /*
    |--------------------------------------------------------------------------
    | Pending Approval Page
    |--------------------------------------------------------------------------
    */

    public function pending()
    {
        // If already approved or admin, redirect away
        if (Auth::check()) {
            $role = Auth::user()->role;

            if ($role === 'admin') {
                return redirect()->route('admin.index');
            }

            if ($role === 'approved') {
                return redirect()->route('dashboard');
            }

            if ($role === 'rejected') {
                return redirect()->route('rejected');
            }
        }

        return view('auth.pending');
    }

    /*
    |--------------------------------------------------------------------------
    | Rejected Page
    |--------------------------------------------------------------------------
    */

    public function rejected()
    {
        return view('auth.rejected');
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
        //->with('success', 'Sessió tancada correctament.');
    }
}
