<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class CustomerLoginController extends Controller
{
    protected string $guard = 'customer';
    protected string $redirectTo = '/customer/dashboard';

    public function showLoginForm(): Response
    {
        return Inertia::render('Auth/CustomerLogin', [
            'status'     => session('status'),
            'defaultTab' => 'login',
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard($this->guard)->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended($this->redirectTo);
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm(): Response
    {
        return Inertia::render('Auth/CustomerLogin', [
            'defaultTab' => 'register',
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                    => ['required', 'string', 'max:255'],
            'company_name'            => ['nullable', 'string', 'max:255'],
            'email'                   => ['required', 'email', 'max:255', 'unique:customers,email'],
            'password'                => ['required', 'confirmed', Password::defaults()],
            'phone'                   => ['nullable', 'string', 'max:50'],
            'address_street'          => ['nullable', 'string', 'max:255'],
            'address_building_number' => ['nullable', 'string', 'max:50'],
            'address_city'            => ['nullable', 'string', 'max:255'],
            'address_zip_code'        => ['nullable', 'string', 'max:20'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $customer = Customer::create($data);

        event(new Registered($customer));

        Auth::guard($this->guard)->login($customer);

        return redirect($this->redirectTo);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard($this->guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('customer.login'));
    }
}
