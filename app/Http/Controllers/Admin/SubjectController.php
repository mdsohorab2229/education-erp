<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubjectRequest;
use App\Http\Requests\Admin\UpdateSubjectRequest;
use App\Models\Subject;
use App\Services\ProgramService;
use App\Services\SubjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function __construct(
        private readonly SubjectService $service,
        private readonly ProgramService $programService,
    ) {
        $this->authorizeResource(Subject::class, 'subject');
    }

    public function index(): View
    {
        $subjects = $this->service->all();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        $programs = $this->programService->all();

        return view('admin.subjects.create', compact('programs'));
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function show(Subject $subject): View
    {
        return view('admin.subjects.show', compact('subject'));
    }

    public function edit(Subject $subject): View
    {
        $programs = $this->programService->all();

        return view('admin.subjects.edit', compact('subject', 'programs'));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $this->service->update($subject->id, $request->validated());

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $this->service->delete($subject->id);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
