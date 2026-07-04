<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSectionRequest;
use App\Http\Requests\Admin\UpdateSectionRequest;
use App\Models\Section;
use App\Services\ProgramService;
use App\Services\SectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function __construct(
        private readonly SectionService $service,
        private readonly ProgramService $programService,
    ) {
        $this->authorizeResource(Section::class, 'section');
    }

    public function index(): View
    {
        $sections = $this->service->all();

        return view('admin.sections.index', compact('sections'));
    }

    public function create(): View
    {
        $programs = $this->programService->all();

        return view('admin.sections.create', compact('programs'));
    }

    public function store(StoreSectionRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section created successfully.');
    }

    public function show(Section $section): View
    {
        return view('admin.sections.show', compact('section'));
    }

    public function edit(Section $section): View
    {
        $programs = $this->programService->all();

        return view('admin.sections.edit', compact('section', 'programs'));
    }

    public function update(UpdateSectionRequest $request, Section $section): RedirectResponse
    {
        $this->service->update($section->id, $request->validated());

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $this->service->delete($section->id);

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section deleted successfully.');
    }

    public function search(Request $request): View
    {
        return $this->index();
    }

    public function export(): RedirectResponse
    {
        return redirect()->route('admin.sections.index')->with('info', 'Export functionality coming soon.');
    }

    public function print(): View
    {
        return $this->index();
    }
}
