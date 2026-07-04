<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGroupRequest;
use App\Http\Requests\Admin\UpdateGroupRequest;
use App\Models\Group;
use App\Services\GroupService;
use App\Services\ProgramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function __construct(
        private readonly GroupService $service,
        private readonly ProgramService $programService,
    ) {
        $this->authorizeResource(Group::class, 'group');
    }

    public function index(): View
    {
        $groups = $this->service->all();

        return view('admin.groups.index', compact('groups'));
    }

    public function create(): View
    {
        $programs = $this->programService->all();

        return view('admin.groups.create', compact('programs'));
    }

    public function store(StoreGroupRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group created successfully.');
    }

    public function show(Group $group): View
    {
        return view('admin.groups.show', compact('group'));
    }

    public function edit(Group $group): View
    {
        $programs = $this->programService->all();

        return view('admin.groups.edit', compact('group', 'programs'));
    }

    public function update(UpdateGroupRequest $request, Group $group): RedirectResponse
    {
        $this->service->update($group->id, $request->validated());

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $this->service->delete($group->id);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Group deleted successfully.');
    }

    public function search(Request $request): View
    {
        return $this->index();
    }

    public function export(): RedirectResponse
    {
        return redirect()->route('admin.groups.index')->with('info', 'Export functionality coming soon.');
    }

    public function print(): View
    {
        return $this->index();
    }
}
