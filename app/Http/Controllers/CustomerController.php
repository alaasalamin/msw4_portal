<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $customers = Customer::query()
            ->when($request->string('q')->isNotEmpty(), function ($q) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $q->where(function ($w) use ($term) {
                    $w->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            })
            ->orderByDesc('id')
            ->paginate(25);

        return response()->json($customers);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request, creating: true);
        $data['password'] = Hash::make($data['password']);

        $customer = Customer::create($data);

        return response()->json($customer, 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json($customer);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $data = $this->validatedData($request, creating: false, ignoreId: $customer->id);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $customer->update($data);

        return response()->json($customer);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json(null, 204);
    }

    private function validatedData(Request $request, bool $creating, ?int $ignoreId = null): array
    {
        $emailRule = ['required', 'email', 'max:255', 'unique:customers,email'];
        if ($ignoreId) {
            $emailRule[3] .= ',' . $ignoreId;
        }

        return $request->validate([
            'first_name'              => ['required', 'string', 'max:255'],
            'last_name'               => ['required', 'string', 'max:255'],
            'email'                   => $emailRule,
            'phone'                   => ['nullable', 'string', 'max:50'],
            'address_street'          => ['nullable', 'string', 'max:255'],
            'address_building_number' => ['nullable', 'string', 'max:50'],
            'address_city'            => ['nullable', 'string', 'max:255'],
            'address_zip_code'        => ['nullable', 'string', 'max:20'],
            'password'                => $creating
                ? ['required', 'confirmed', Password::defaults()]
                : ['nullable', 'confirmed', Password::defaults()],
        ]);
    }
}
