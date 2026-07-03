<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProgramRequest;
use App\Http\Requests\Admin\UpdateProgramRequest;
use App\Models\Program;
use App\Services\DepartmentService;
use App\Services\ProgramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function __construct(
        private readonly ProgramService $service,
        private readonly DepartmentService $departmentService,
    ) {
        $this->authorizeResource(Program::class, 'program');
    }

    public function index(): View
    {
        $programs = $this->service->all();

        return view('admin.programs.index', compact('programs'));
    }

    public function create(): View
    {
        $departments = $this->departmentService->all();

        return view('admin.programs.create', compact('departments'));
    }

    public function store(StoreProgramRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program created successfully.');
    }

    public function show(Program $program): View
    {
        return view('admin.programs.show', compact('program'));
    }

    public function edit(Program $program): View
    {
        $departments = $this->departmentService->all();

        return view('admin.programs.edit', compact('program', 'departments'));
    }

    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $this->service->update($program->id, $request->validated());

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program updated successfully.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        $this->service->delete($program->id);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program deleted successfully.');
    }
}
