<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DefaultTask;
use App\Services\DefaultTaskService;
use Illuminate\Http\Request;

class DefaultTaskController extends Controller
{
    public function __construct(
        protected DefaultTaskService $defaultTaskService
    ) {}

    public function index()
    {
        $defaultTasks = $this->defaultTaskService->getAll();
        $roles = \App\Models\Role::where('name', '!=', 'admin')->orderBy('id')->get();
        return view('admin.default-tasks.index', compact('defaultTasks', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'target_role' => ['required', 'exists:roles,name'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $this->defaultTaskService->create($validated);

        return redirect()->route('admin.default-tasks.index')
            ->with('success', 'Default task berhasil ditambahkan.');
    }

    public function update(Request $request, DefaultTask $defaultTask)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'target_role' => ['required', 'exists:roles,name'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $this->defaultTaskService->update($defaultTask, $validated);

        return redirect()->route('admin.default-tasks.index')
            ->with('success', 'Default task berhasil diperbarui.');
    }

    public function destroy(DefaultTask $defaultTask)
    {
        $this->defaultTaskService->delete($defaultTask);

        return redirect()->route('admin.default-tasks.index')
            ->with('success', 'Default task berhasil dihapus.');
    }
}