<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $setting = StoreSetting::first() ?? StoreSetting::create([
            'store_name' => 'SmartPOS Coffee & Retail',
        ]);

        return view('settings.index', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'tax_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'receipt_footer' => ['nullable', 'string', 'max:500'],
        ]);

        $setting = StoreSetting::first();
        if ($setting) {
            $setting->update($validated);
        } else {
            StoreSetting::create($validated);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan profil toko & struk berhasil disimpan!');
    }
}
