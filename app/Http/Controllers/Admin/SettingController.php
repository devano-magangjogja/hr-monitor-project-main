<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaGroup;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(protected SettingService $settingService) {}

    public function index()
    {
        $settings = $this->settingService->getAll();
        $waGroups = $this->settingService->getAllWaGroups();

        return view('admin.settings.index', compact('settings', 'waGroups'));
    }

    // ── Update nama & logo ───────────────────────────────

    public function updateAppInfo(Request $request)
    {
        $request->validate([
            'app_name'    => ['required', 'string', 'max:60'],
            'logo'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'remove_logo' => ['nullable', 'in:0,1'],
        ]);

        $this->settingService->updateAppInfo(
            ['app_name' => $request->app_name, 'remove_logo' => $request->remove_logo],
            $request->hasFile('logo') ? $request->file('logo') : null
        );

        return back()->with('success', 'Informasi aplikasi berhasil diperbarui.');
    }

    // ── WA Group CRUD ────────────────────────────────────

    public function storeWaGroup(Request $request)
    {
        $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'url'   => ['required', 'url', 'max:255'],
        ]);

        $this->settingService->createWaGroup($request->only('label', 'url'));

        return back()->with('success', 'Link grup berhasil ditambahkan.');
    }

    public function updateWaGroup(Request $request, WaGroup $waGroup)
    {
        $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'url'   => ['required', 'url', 'max:255'],
        ]);

        $this->settingService->updateWaGroup($waGroup, $request->only('label', 'url'));

        return back()->with('success', 'Link grup berhasil diperbarui.');
    }

    public function destroyWaGroup(WaGroup $waGroup)
    {
        $this->settingService->deleteWaGroup($waGroup);

        return back()->with('success', 'Link grup berhasil dihapus.');
    }
}
