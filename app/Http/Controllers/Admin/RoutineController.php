<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoutineRequest;
use App\Http\Requests\Admin\UpdateRoutineRequest;
use App\Http\Resources\RoutineResource;
use App\Services\RoutineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoutineController extends Controller
{
    public function __construct(
        private readonly RoutineService $service,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return RoutineResource::collection($this->service->all());
    }

    public function store(StoreRoutineRequest $request): RoutineResource
    {
        $routine = $this->service->create($request->validated());

        return new RoutineResource($routine);
    }

    public function update(UpdateRoutineRequest $request, int $id): RoutineResource
    {
        $routine = $this->service->update($id, $request->validated());

        return new RoutineResource($routine);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(['message' => 'Routine deleted successfully.']);
    }

    public function weekly(Request $request): AnonymousResourceCollection
    {
        $routines = $this->service->getWeeklyRoutine(
            (int) $request->query('section_id'),
            $request->query('day_of_week'),
        );

        return RoutineResource::collection($routines);
    }

    public function teacher(Request $request): AnonymousResourceCollection
    {
        $routines = $this->service->getTeacherRoutine(
            (int) $request->query('teacher_id'),
            $request->query('day_of_week'),
        );

        return RoutineResource::collection($routines);
    }

    public function student(Request $request): AnonymousResourceCollection
    {
        $routines = $this->service->getStudentRoutine(
            (int) $request->query('section_id'),
            $request->query('group_id') ? (int) $request->query('group_id') : null,
            $request->query('day_of_week'),
        );

        return RoutineResource::collection($routines);
    }
}
