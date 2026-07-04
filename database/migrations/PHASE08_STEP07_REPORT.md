# Phase 08 — Step 07: Controllers

**Status:** Completed  
**Date:** 2026-07-04  
**Tests:** 294 passed, 0 failures (745 assertions — no regressions)

---

## Files Created

### Localization (1)
| File | Description |
|------|-------------|
| `lang/en/examination.php` | 14 translation keys for exam/marks/approval messages |

### Service (1)
| File | Description |
|------|-------------|
| `app/Services/ExamService.php` | CRUD service following `DepartmentService`/`AcademicYearService` pattern |

### Controllers (3)
| Controller | Namespace | Service Dependency |
|------------|-----------|-------------------|
| `ExamController` | `App\Http\Controllers\Admin` | `ExamService` |
| `MarksEntryController` | `App\Http\Controllers\Admin` | `MarksEntryService` |
| `MarksApprovalController` | `App\Http\Controllers\Admin` | `MarksApprovalService` |

### Modified Files (1)
| File | Change |
|------|--------|
| `app/Services/MarksEntryService.php` | Added `loadStudents(int $examSubjectId): array` method (returns exam_subject with exam.section + existing marks) |

---

## Controller Method Mapping

### ExamController

| Method | Route/Verb | Request | Response | Service Call |
|--------|-----------|---------|----------|-------------|
| `index` | GET | `Request` (query: per_page) | `success()` + `ExamResource::collection()` + pagination meta | `service->paginate()` |
| `store` | POST | `StoreExamRequest` | `created()` + `new ExamResource()` | `service->create()` |
| `show` | GET | `int $id` | `success()` + `new ExamResource()` or `notFound()` | `service->findWithRelations()` |
| `update` | PUT/PATCH | `UpdateExamRequest` + `int $id` | `success()` + `new ExamResource()` | `service->update()` |
| `destroy` | DELETE | `int $id` | `success()` (message only) | `service->delete()` |

### MarksEntryController

| Method | Route/Verb | Request | Response | Service Call |
|--------|-----------|---------|----------|-------------|
| `index` | GET | `Request` (query: exam_id) | `success()` + `MarkResource::collection()` | `service->getExamMarks()` |
| `loadStudents` | GET | `Request` (query: exam_subject_id) | `success()` + `ExamSubjectResource` + `MarkResource::collection()` | `service->loadStudents()` |
| `bulkStore` | POST | `MarksEntryRequest` | `created()` + result array | `service->bulkStore()` (merges exam_subject_id into each row) |
| `update` | PUT/PATCH | `Request` (obtained_mark, practical_mark, viva_mark, remark) + `int $id` | `success()` + `new MarkResource()` | `service->updateMark()` |

### MarksApprovalController

| Method | Route/Verb | Request | Response | Service Call |
|--------|-----------|---------|----------|-------------|
| `pending` | GET | — | `success()` + `MarkResource::collection()` | `service->pendingList()` |
| `approve` | POST | `MarksApprovalRequest` + `int $id` | `success()` + `new MarkResource()` | `service->approve($id, auth()->id())` |
| `reject` | POST | `MarksApprovalRequest` + `int $id` | `success()` + `new MarkResource()` | `service->reject($id, auth()->id(), remark)` |
| `reset` | POST | `int $id` | `success()` + `new MarkResource()` | `service->reset($id)` |

---

## Architecture Compliance

| Rule | Status | Evidence |
|------|--------|----------|
| Thin controllers | ✅ | Each action calls exactly one service method |
| No Eloquent in controllers | ✅ | Zero `Model` imports in controllers |
| No Repositories in controllers | ✅ | Zero `Repository` imports in controllers |
| No DB facade in controllers | ✅ | Zero `DB::` calls in controllers |
| No business logic in controllers | ✅ | All logic delegated to services |
| Use ApiResponse trait | ✅ | All 3 controllers use `use ApiResponse;` |
| Use Form Requests | ✅ | `StoreExamRequest`, `UpdateExamRequest`, `MarksEntryRequest`, `MarksApprovalRequest` |
| Use API Resources | ✅ | `ExamResource`, `ExamSubjectResource`, `MarkResource` |
| Localization via `__()` | ✅ | All messages use `__('examination.*')` |
| `declare(strict_types=1)` | ✅ | All files |
| PSR-12 | ✅ | Consistent with existing controllers |

---

## ExamService Design

```
ExamService
├── paginate(int $perPage) → LengthAwarePaginator
├── findById(int $id) → ?Exam
├── findWithRelations(int $id) → ?Exam
├── create(array $data) → Exam         [DB::transaction]
├── update(int $id, array $data) → Exam [DB::transaction]
└── delete(int $id) → void              [DB::transaction]
```

Follows the exact same pattern as `DepartmentService`, `AcademicYearService`, and other CRUD services in the project. `ExamRepositoryInterface` is already bound in `AppServiceProvider`.
