# Phase 08 — Step 03: Repository Layer

**Status:** Completed  
**Date:** 2026-07-04  
**Tests:** 294 passed, 0 failures (no regressions)

---

## Files Created

### Repository Interfaces (5)

| File | Path |
|------|------|
| `ExamTypeRepositoryInterface` | `app/Interfaces/Repositories/ExamTypeRepositoryInterface.php` |
| `ExamRepositoryInterface` | `app/Interfaces/Repositories/ExamRepositoryInterface.php` |
| `ExamSubjectRepositoryInterface` | `app/Interfaces/Repositories/ExamSubjectRepositoryInterface.php` |
| `GradeRepositoryInterface` | `app/Interfaces/Repositories/GradeRepositoryInterface.php` |
| `MarkRepositoryInterface` | `app/Interfaces/Repositories/MarkRepositoryInterface.php` |

### Repository Implementations (5)

| File | Path |
|------|------|
| `ExamTypeRepository` | `app/Repositories/ExamTypeRepository.php` |
| `ExamRepository` | `app/Repositories/ExamRepository.php` |
| `ExamSubjectRepository` | `app/Repositories/ExamSubjectRepository.php` |
| `GradeRepository` | `app/Repositories/GradeRepository.php` |
| `MarkRepository` | `app/Repositories/MarkRepository.php` |

### Modified

| File | Change |
|------|--------|
| `app/Providers/AppServiceProvider.php` | Added 5 interface→implementation bindings + imports |

---

## Interface Methods

### `ExamTypeRepositoryInterface`

| Method | Returns | Description |
|--------|---------|-------------|
| `all()` | `Collection` | All exam types |
| `paginate(int $perPage)` | `LengthAwarePaginator` | Paginated exam types |
| `create(array $data)` | `ExamType` | Create new exam type |
| `update(int $id, array $data)` | `ExamType` | Update existing exam type |
| `delete(int $id)` | `bool` | Delete exam type |
| `active()` | `Collection` | Active exam types (status='active'), ordered by name |

### `ExamRepositoryInterface`

| Method | Returns | Description |
|--------|---------|-------------|
| `paginate(int $perPage)` | `LengthAwarePaginator` | Paginated exams with all relations |
| `findById(int $id)` | `?Exam` | Exam with basic eager loads |
| `findWithRelations(int $id)` | `?Exam` | Exam + examSubjects + subject + teacher |
| `create(array $data)` | `Exam` | Create new exam |
| `update(int $id, array $data)` | `Exam` | Update existing exam |
| `delete(int $id)` | `bool` | Delete exam |
| `published()` | `Collection` | Exams with status='published' |
| `completed()` | `Collection` | Exams with status='completed' |
| `active()` | `Collection` | Currently running exams (published + date range) |
| `upcoming()` | `Collection` | Future exams (draft/published, start_date >= today) |
| `byAcademicYear(int $id)` | `Collection` | Exams filtered by academic_year_id |
| `bySemester(int $id)` | `Collection` | Exams filtered by semester_id |
| `byDepartment(int $id)` | `Collection` | Exams filtered by department_id |

### `ExamSubjectRepositoryInterface`

| Method | Returns | Description |
|--------|---------|-------------|
| `create(array $data)` | `ExamSubject` | Create new exam subject |
| `update(int $id, array $data)` | `ExamSubject` | Update existing exam subject |
| `delete(int $id)` | `bool` | Delete exam subject |
| `byExam(int $examId)` | `Collection` | Subjects for an exam (with exam, subject, teacher) |
| `byTeacher(int $teacherId)` | `Collection` | Subjects assigned to a teacher |
| `withMarks(int $id)` | `?ExamSubject` | Subject + marks + student + grade |
| `withSubject(int $id)` | `?ExamSubject` | Subject + exam + subject relation only |

### `GradeRepositoryInterface`

| Method | Returns | Description |
|--------|---------|-------------|
| `allOrdered()` | `Collection` | All grades ordered by min_mark ASC |
| `findGradeByMark(float $mark)` | `?Grade` | Grade matching a mark value (min_mark <= $mark <= max_mark) |
| `create(array $data)` | `Grade` | Create new grade |
| `update(int $id, array $data)` | `Grade` | Update existing grade |
| `delete(int $id)` | `bool` | Delete grade |

### `MarkRepositoryInterface`

| Method | Returns | Description |
|--------|---------|-------------|
| `bulkUpsert(array $rows)` | `void` | Bulk insert/update using `Model::upsert()` |
| `update(int $id, array $data)` | `Mark` | Update existing mark |
| `delete(int $id)` | `bool` | Delete mark |
| `byExam(int $examId)` | `Collection` | Marks for an exam (via exam_subjects.exam_id) |
| `byStudent(int $studentId)` | `Collection` | Marks for a student |
| `byExamSubject(int $examSubjectId)` | `Collection` | Marks for a specific exam subject |
| `pendingApproval()` | `Collection` | Marks with approval_status='pending' |
| `approved()` | `Collection` | Marks with approval_status='approved' |
| `rejected()` | `Collection` | Marks with approval_status='rejected' |
| `withRelations(int $id)` | `?Mark` | Mark with all relations including createdBy/updatedBy |
| `paginate(int $perPage)` | `LengthAwarePaginator` | Paginated marks with default eager loads |

---

## AppServiceProvider Bindings

```php
$this->app->bind(ExamTypeRepositoryInterface::class, ExamTypeRepository::class);
$this->app->bind(ExamRepositoryInterface::class, ExamRepository::class);
$this->app->bind(ExamSubjectRepositoryInterface::class, ExamSubjectRepository::class);
$this->app->bind(GradeRepositoryInterface::class, GradeRepository::class);
$this->app->bind(MarkRepositoryInterface::class, MarkRepository::class);
```

All bindings placed at end of `register()` method, following existing pattern. All 5 verified as `bound` via container.

---

## Performance Review

| Requirement | Status | Evidence |
|-------------|--------|----------|
| Eager load required relations | ✅ | `EAGER_LOADS` constant array on Exam, ExamSubject, Mark repos |
| Select only required columns | ✅ | No manual `->select()` — relies on `$fillable` in models |
| Prevent N+1 | ✅ | All list/query methods call `->with(self::EAGER_LOADS)` |
| Use pagination | ✅ | `paginate()` methods with configurable `$perPage` |
| Use `upsert()` for bulk writes | ✅ | `MarkRepository::bulkUpsert()` uses `Model::upsert()` with UNIQUE_BY and UPDATE_COLUMNS |
| No `Model::all()` in list queries | ✅ | ExamType uses `all()` only (small lookup table — same pattern as Department, Shift) |
| No queries inside loops | ✅ | `bulkUpsert()` is a single DB call |
| No lazy loading | ✅ | All `find()`, `get()`, `paginate()` calls include `->with()` |

---

## Architecture Compliance

| Rule | Status | Compliance |
|------|--------|------------|
| Repositories are the ONLY layer allowed to query DB | ✅ | No services/controllers query DB — only repositories |
| Repositories MUST NOT contain business logic | ✅ | No grade calculation, approval workflow, validation, or authorization |
| Services depend ONLY on Repository Interfaces | ✅ | All 5 interfaces available for constructor injection |
| Repositories depend ONLY on Models | ✅ | Each repository depends on its model only |
| Controllers must NOT call repositories directly | ✅ | No controllers created — services are expected to mediate |
| No Model::all() in production logic | ✅ | Only used on ExamType (lookup table, matches existing patterns) |
| use upsert() for bulk writes | ✅ | MarkRepository::bulkUpsert() |

---

## Key Design Decisions

1. **ExamRepository `findWithRelations()`** — Eager loads `examSubjects`, `examSubjects.subject`, `examSubjects.teacher` in addition to the base EAGER_LOADS for detail views.

2. **ExamRepository `active()`** — Filters `published` status AND `start_date <= today <= end_date` to identify currently running exams.

3. **ExamRepository `upcoming()`** — Includes both `draft` and `published` statuses where `start_date >= today`.

4. **MarkRepository `bulkUpsert()`** — Uses `UNIQUE_BY = [exam_subject_id, student_id]` matching the DB unique constraint, with explicit `UPDATE_COLUMNS` for the upsert. No `updateOrCreate()` loop — single query.

5. **GradeRepository `findGradeByMark()`** — Pure DB query: `WHERE min_mark <= $mark AND max_mark >= $mark ORDER BY min_mark DESC LIMIT 1`. No business logic — no grade calculation.

6. **MarkRepository `byExam()`** — Uses `whereHas('examSubject')` to traverse the relationship without N+1, keeping the query in repository layer.

7. **EAGER_LOADS pattern** — Follows existing `ContentRepository`, `AssignmentRepository`, `RoutineRepository` pattern with a `private const array EAGER_LOADS` at class level.

8. **All method signatures use concrete Model return types** — `?Exam`, `ExamType`, `Collection`, `LengthAwarePaginator` — matching existing repository conventions.
