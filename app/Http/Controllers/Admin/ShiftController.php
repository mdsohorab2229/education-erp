<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShiftRequest;
use App\Http\Requests\Admin\UpdateShiftRequest;
use App\Models\Shift;
use App\Services\ShiftService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function __construct(
        private readonly ShiftService $service
    ) {
        $this->authorizeResource(Shift::class, 'shift');
    }

    public function index(): View
    {
        $shifts = $this->service->all();

        return view('admin.shifts.index', compact('shifts'));
    }

    public function create(): View
    {
        return view('admin.shifts.create');
    }

    public function store(StoreShiftRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.shifts.index')
            ->with('success', 'Shift created successfully.');
    }

    public function show(Shift $shift): View
    {
        return view('admin.shifts.show', compact('shift'));
    }

    public function edit(Shift $shift): View
    {
        return view('admin.shifts.edit', compact('shift'));
    }

    public function update(UpdateShiftRequest $request, Shift $shift): RedirectResponse
    {
        $this->service->update($shift->id, $request->validated());

        return redirect()->route('admin.shifts.index')
            ->with('success', 'Shift updated successfully.');
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        $this->service->delete($shift->id);

        return redirect()->route('admin.shifts.index')
            ->with('success', 'Shift deleted successfully.');
    }
}
