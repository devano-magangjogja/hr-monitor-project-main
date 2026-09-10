<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\WaGroup;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    use LogsActivity;

    public function __construct(protected SettingService $settingService) {}

    public function index()
    {
        $settings = $this->settingService->getAll();
        $waGroups = collect(); // WA Groups feature disabled

        return view('admin.settings.index', compact('settings', 'waGroups'));
    }

    // ── Update nama & logo ───────────────────────────────

    public function updateAppInfo(Request $request)
    {
        $request->validate([
            'app_name'          => ['required', 'string', 'max:60'],
            'logo'              => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'logo_banner'       => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'remove_logo'       => ['nullable', 'in:0,1'],
            'remove_logo_banner'=> ['nullable', 'in:0,1'],
        ]);

        try {
            $this->settingService->updateAppInfo(
                [
                    'app_name'           => $request->app_name,
                    'remove_logo'        => $request->remove_logo,
                    'remove_logo_banner' => $request->remove_logo_banner,
                ],
                $request->hasFile('logo')        ? $request->file('logo')        : null,
                $request->hasFile('logo_banner') ? $request->file('logo_banner') : null
            );

            $this->logActivity('setting.updated', 'Pengaturan', "Memperbarui informasi aplikasi/logo");

            return back()->with('success', 'Informasi aplikasi berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            Log::error('Gagal memperbarui informasi aplikasi/logo: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Terjadi kendala saat memperbarui informasi aplikasi atau logo. Silakan coba beberapa saat lagi.')->withInput();
        }
    }

    // ── Update Template Pesan WhatsApp ───────────────────

    public function updateWaTemplate(Request $request)
    {
        $request->validate([
            'wa_template_tidak_hadir' => ['required', 'string', 'max:1000'],
        ]);

        $this->settingService->updateWaTemplate($request->input('wa_template_tidak_hadir'));
        $this->logActivity('setting.updated', 'Pengaturan', "Memperbarui template WhatsApp");

        return back()->with('success', 'Template pesan WhatsApp konfirmasi ketidakhadiran berhasil diperbarui.');
    }

    // ── WA Group CRUD ────────────────────────────────────

    public function storeWaGroup(Request $request)
    {
        $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'url'   => ['required', 'url', 'max:255'],
        ]);

        $this->settingService->createWaGroup($request->only('label', 'url'));
        $this->logActivity('setting.updated', 'Pengaturan', "Menambahkan link grup WhatsApp '{$request->label}'");

        return back()->with('success', 'Link grup berhasil ditambahkan.');
    }

    public function updateWaGroup(Request $request, WaGroup $waGroup)
    {
        $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'url'   => ['required', 'url', 'max:255'],
        ]);

        $this->settingService->updateWaGroup($waGroup, $request->only('label', 'url'));
        $this->logActivity('setting.updated', 'Pengaturan', "Memperbarui link grup WhatsApp '{$waGroup->label}'");

        return back()->with('success', 'Link grup berhasil diperbarui.');
    }

    public function destroyWaGroup(WaGroup $waGroup)
    {
        $label = $waGroup->label;
        $this->settingService->deleteWaGroup($waGroup);
        $this->logActivity('setting.updated', 'Pengaturan', "Menghapus link grup WhatsApp '{$label}'");

        return back()->with('success', 'Link grup berhasil dihapus.');
    }
}
