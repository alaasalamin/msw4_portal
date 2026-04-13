# Auth System Architecture

## Overview

Multi-guard authentication system with three account types sharing a single `users` table.
Each account type has its own Guard, Model, and LoginController.

---

## Database

### `users` table (shared)

```sql
users
├── id                  -- bigint, primary key
├── name                -- string
├── email               -- string, unique
├── password            -- string, hashed
├── type                -- enum: 'employee' | 'customer' | 'partner'
├── email_verified_at   -- timestamp, nullable
├── remember_token      -- string, nullable
├── created_at          -- timestamp
└── updated_at          -- timestamp
```

> All three account types live in this single table. The `type` column differentiates them.
> Each Model uses a `where('type', ...)` global scope to scope queries automatically.

---

## Directory Structure

```
app/
├── Http/
│   └── Controllers/
│       └── Auth/
│           ├── EmployeeLoginController.php
│           ├── CustomerLoginController.php
│           └── PartnerLoginController.php
│
├── Models/
│   ├── Employee.php
│   ├── Customer.php
│   └── Partner.php
│
└── Providers/
    └── AuthServiceProvider.php

config/
└── auth.php              ← defines guards + providers for all 3 types

routes/
├── web.php               ← shared/public routes
├── employee.php          ← employee-only routes (middleware: auth:employee)
├── customer.php          ← customer-only routes (middleware: auth:customer)
└── partner.php           ← partner-only routes  (middleware: auth:partner)
```

---

## Models

### `app/Models/Employee.php`

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class Employee extends Authenticatable
{
    protected $table = 'users';

    protected static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'employee');
        });
    }

    protected static function creating($model): void
    {
        $model->type = 'employee';
    }
}
```

### `app/Models/Customer.php`

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Authenticatable
{
    protected $table = 'users';

    protected static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'customer');
        });
    }

    protected static function creating($model): void
    {
        $model->type = 'customer';
    }
}
```

### `app/Models/Partner.php`

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class Partner extends Authenticatable
{
    protected $table = 'users';

    protected static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'partner');
        });
    }

    protected static function creating($model): void
    {
        $model->type = 'partner';
    }
}
```

---

## Guards & Providers — `config/auth.php`

```php
'guards' => [
    'web' => [
        'driver'   => 'session',
        'provider' => 'users',
    ],

    'employee' => [
        'driver'   => 'session',
        'provider' => 'employees',
    ],

    'customer' => [
        'driver'   => 'session',
        'provider' => 'customers',
    ],

    'partner' => [
        'driver'   => 'session',
        'provider' => 'partners',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model'  => App\Models\User::class,
    ],

    'employees' => [
        'driver' => 'eloquent',
        'model'  => App\Models\Employee::class,
    ],

    'customers' => [
        'driver' => 'eloquent',
        'model'  => App\Models\Customer::class,
    ],

    'partners' => [
        'driver' => 'eloquent',
        'model'  => App\Models\Partner::class,
    ],
],
```

---

## Login Controllers

### `app/Http/Controllers/Auth/EmployeeLoginController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeLoginController extends Controller
{
    protected string $guard = 'employee';
    protected string $redirectTo = '/employee/dashboard';

    public function showLoginForm()
    {
        return view('auth.employee.login');
    }

    public function login(Request $request)
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

    public function logout(Request $request)
    {
        Auth::guard($this->guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/employee/login');
    }
}
```

### `app/Http/Controllers/Auth/CustomerLoginController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerLoginController extends Controller
{
    protected string $guard = 'customer';
    protected string $redirectTo = '/customer/dashboard';

    public function showLoginForm()
    {
        return view('auth.customer.login');
    }

    public function login(Request $request)
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

    public function logout(Request $request)
    {
        Auth::guard($this->guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/customer/login');
    }
}
```

### `app/Http/Controllers/Auth/PartnerLoginController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerLoginController extends Controller
{
    protected string $guard = 'partner';
    protected string $redirectTo = '/partner/dashboard';

    public function showLoginForm()
    {
        return view('auth.partner.login');
    }

    public function login(Request $request)
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

    public function logout(Request $request)
    {
        Auth::guard($this->guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/partner/login');
    }
}
```

---

## Routes

### `routes/web.php` (shared entry points)

```php
// Employee auth routes
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('login',  [EmployeeLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [EmployeeLoginController::class, 'login']);
    Route::post('logout',[EmployeeLoginController::class, 'logout'])->name('logout');
});

// Customer auth routes
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('login',  [CustomerLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [CustomerLoginController::class, 'login']);
    Route::post('logout',[CustomerLoginController::class, 'logout'])->name('logout');
});

// Partner auth routes
Route::prefix('partner')->name('partner.')->group(function () {
    Route::get('login',  [PartnerLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [PartnerLoginController::class, 'login']);
    Route::post('logout',[PartnerLoginController::class, 'logout'])->name('logout');
});
```

### Protected routes — `routes/employee.php`

```php
Route::prefix('employee')->name('employee.')->middleware('auth:employee')->group(function () {
    Route::get('dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    // ... more employee routes
});
```

### Protected routes — `routes/customer.php`

```php
Route::prefix('customer')->name('customer.')->middleware('auth:customer')->group(function () {
    Route::get('dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    // ... more customer routes
});
```

### Protected routes — `routes/partner.php`

```php
Route::prefix('partner')->name('partner.')->middleware('auth:partner')->group(function () {
    Route::get('dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
    // ... more partner routes
});
```

---

## Views Structure

```
resources/views/auth/
├── employee/
│   └── login.blade.php
├── customer/
│   └── login.blade.php
└── partner/
    └── login.blade.php
```

---

## Migration

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->enum('type', ['employee', 'customer', 'partner']);
    $table->rememberToken();
    $table->timestamps();

    $table->index('type'); // index for global scope queries
});
```

---

## How It Works — Summary

| Account Type | Guard      | Model      | Login URL          | Dashboard URL          | Middleware        |
|--------------|------------|------------|--------------------|------------------------|-------------------|
| Employee     | `employee` | `Employee` | `/employee/login`  | `/employee/dashboard`  | `auth:employee`   |
| Customer     | `customer` | `Customer` | `/customer/login`  | `/customer/dashboard`  | `auth:customer`   |
| Partner      | `partner`  | `Partner`  | `/partner/login`   | `/partner/dashboard`   | `auth:partner`    |

- One `users` table — no separate tables needed.
- Each Model adds a global scope filtering by `type` automatically.
- Each guard uses its own session key — users can be logged into multiple guards simultaneously.
- Middleware `auth:employee` only accepts sessions from the `employee` guard, and so on.
