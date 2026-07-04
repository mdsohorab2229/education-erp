<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct(
        private readonly PermissionService $permissionService,
    ) {
        $this->authorizeResource(Permission::class, 'permission');
    }

    public function index(): View
    {
        $permissions = $this->permissionService->all();

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create(): View
    {
        return view('admin.permissions.create');
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->permissionService->create($request->validated());

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function show(Permission $permission): View
    {
        return view('admin.permissions.show', compact('permission'));
    }

    public function edit(Permission $permission): View
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->permissionService->update($permission->id, $request->validated());

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $this->permissionService->delete($permission->id);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }

    public function search(Request $request): View
    {
        return $this->index();
    }

    public function export(): RedirectResponse
    {
        return redirect()->route('admin.permissions.index')->with('info', 'Export functionality coming soon.');
    }

    public function print(): View
    {
        return $this->index();
    }
}
