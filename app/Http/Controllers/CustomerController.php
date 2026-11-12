<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('orders');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(10)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:25', 'unique:customers,phone'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama pelanggan / member wajib diisi.',
            'phone.unique' => 'Nomor HP ini sudah terdaftar sebagai member.',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', "Member '{$validated['name']}' berhasil didaftarkan!");
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:25', 'unique:customers,phone,' . $customer->id],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', "Data member '{$customer->name}' berhasil diperbarui!");
    }

    public function show(Customer $customer): View
    {
        $customer->load(['orders' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('customers.show', compact('customer'));
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $name = $customer->name;
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', "Member '{$name}' telah dihapus.");
    }
}
