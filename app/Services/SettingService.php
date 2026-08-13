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

    public function updateAppInfo(array $data, ?UploadedFile $logoFile = null): void
    {
        if (isset($data['app_name'])) {
            $this->settingRepo->set('app_name', trim($data['app_name']));
        }

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
