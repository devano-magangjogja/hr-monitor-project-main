<?php

namespace App\Services;

use App\Repositories\AppSettingRepository;
use App\Repositories\WaGroupRepository;
use App\Models\WaGroup;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    public function __construct(
        protected AppSettingRepository $settingRepo,
        protected WaGroupRepository    $waGroupRepo,
    ) {}

    // ── App Settings ─────────────────────────────────────

    public function getAll(): array
    {
        return $this->settingRepo->all();
    }

    public function updateAppInfo(array $data, ?UploadedFile $logoFile = null, ?UploadedFile $logoBannerFile = null): void
    {
        if (isset($data['app_name'])) {
            $this->settingRepo->set('app_name', trim($data['app_name']));
        }

        // Square/sidebar logo
        if ($logoFile) {
            $oldLogo = $this->settingRepo->get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $logoFile->store('app', 'public');
            $this->settingRepo->set('app_logo', $path);
        }

        if (!empty($data['remove_logo']) && $data['remove_logo'] === '1') {
            $oldLogo = $this->settingRepo->get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $this->settingRepo->set('app_logo', null);
        }

        // Banner/login logo
        if ($logoBannerFile) {
            $oldBanner = $this->settingRepo->get('app_logo_banner');
            if ($oldBanner && Storage::disk('public')->exists($oldBanner)) {
                Storage::disk('public')->delete($oldBanner);
            }
            $path = $logoBannerFile->store('app', 'public');
            $this->settingRepo->set('app_logo_banner', $path);
        }

        if (!empty($data['remove_logo_banner']) && $data['remove_logo_banner'] === '1') {
            $oldBanner = $this->settingRepo->get('app_logo_banner');
            if ($oldBanner && Storage::disk('public')->exists($oldBanner)) {
                Storage::disk('public')->delete($oldBanner);
            }
            $this->settingRepo->set('app_logo_banner', null);
        }
    }

    public function updateWaTemplate(string $template): void
    {
        $this->settingRepo->set('wa_template_tidak_hadir', trim($template));
    }

    // ── WA Groups ────────────────────────────────────────

    public function getAllWaGroups()
    {
        return $this->waGroupRepo->all();
    }

    public function getActiveWaGroups()
    {
        return $this->waGroupRepo->active();
    }

    public function createWaGroup(array $data): WaGroup
    {
        return $this->waGroupRepo->create([
            'label' => $data['label'],
            'url'   => $data['url'],
        ]);
    }

    public function updateWaGroup(WaGroup $group, array $data): bool
    {
        return $this->waGroupRepo->update($group, [
            'label' => $data['label'],
            'url'   => $data['url'],
        ]);
    }

    public function deleteWaGroup(WaGroup $group): bool
    {
        return $this->waGroupRepo->delete($group);
    }
}
