<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('id')->get();
        $baseTypeOptions = Role::getBaseTypeOptions();

        $colorPresets = [
            'bg-slate-100 text-slate-700'   => 'Abu-abu (Slate)',
            'bg-blue-50 text-blue-700'      => 'Biru (Blue)',
            'bg-purple-50 text-purple-700'  => 'Ungu (Purple)',
            'bg-teal-50 text-teal-700'      => 'Teal (Teal)',
            'bg-emerald-50 text-emerald-700'=> 'Hijau (Emerald)',
            'bg-orange-50 text-orange-700'  => 'Oranye (Orange)',
            'bg-cyan-50 text-cyan-700'      => 'Cyan (Cyan)',
            'bg-rose-50 text-rose-700'      => 'Merah Muda (Rose)',
            'bg-amber-50 text-amber-700'    => 'Kuning (Amber)',
            'bg-indigo-50 text-indigo-700'  => 'Nila (Indigo)',
            'bg-red-50 text-red-700'        => 'Merah (Red)',
        ];

        return view('admin.roles.index', compact('roles', 'baseTypeOptions', 'colorPresets'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'name' => Str::slug($request->name, '_'),
        ]);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:50', 'alpha_dash', 'unique:roles,name'],
            'label'       => ['required', 'string', 'max:100'],
            'base_type'   => ['required', Rule::in(['member', 'assistant', 'staff', 'admin'])],
            'badge_class' => ['nullable', 'string', 'max:100'],
        ], [
            'name.unique'     => 'Kode role / slug sudah digunakan.',
            'name.alpha_dash' => 'Kode role hanya boleh berupa huruf, angka, dan garis bawah (_).',
            'label.required'  => 'Nama role wajib diisi.',
            'base_type.required' => 'Pilih tipe peran / template tampilan.',
        ]);

        Role::create([
            'name'        => strtolower($validated['name']),
            'label'       => $validated['label'],
            'base_type'   => $validated['base_type'],
            'badge_class' => $validated['badge_class'] ?? 'bg-slate-100 text-slate-700',
            'is_system'   => false,
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$validated['label']}' berhasil ditambahkan.");
    }

    public function update(Request $request, Role $role)
    {
        $rules = [
            'label'       => ['required', 'string', 'max:100'],
            'badge_class' => ['nullable', 'string', 'max:100'],
        ];

        if (! $role->is_system) {
            $rules['base_type'] = ['required', Rule::in(['member', 'assistant', 'staff', 'admin'])];
        }

        $validated = $request->validate($rules);

        $updateData = [
            'label'       => $validated['label'],
            'badge_class' => $validated['badge_class'] ?? $role->badge_class,
        ];

        if (! $role->is_system && isset($validated['base_type'])) {
            $updateData['base_type'] = $validated['base_type'];
        }

        $role->update($updateData);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->label}' berhasil diperbarui.");
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'Role bawaan sistem tidak dapat dihapus.');
        }

        $usersCount = $role->users()->count();
        if ($usersCount > 0) {
            return back()->with('error', "Role '{$role->label}' tidak dapat dihapus karena masih digunakan oleh {$usersCount} pengguna.");
        }

        $roleLabel = $role->label;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleLabel}' berhasil dihapus.");
    }
}
