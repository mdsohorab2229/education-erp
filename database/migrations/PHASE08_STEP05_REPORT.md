# Phase 08 — Step 05: Form Requests

**Status:** Completed  
**Date:** 2026-07-04  
**Tests:** 294 passed, 0 failures (745 assertions — no regressions)

---

## Files Created

### Enum (1)

| File | Purpose |
|------|---------|
| `app/Enums/ApprovalStatus.php` | Backed string enum: `pending`, `approved`, `rejected` — with `values()` and `labels()` helpers (matches `AttendanceStatus` pattern) |

### Custom Validation Rules (2)

| File | Purpose | Constructor Args |
|------|---------|-----------------|
| `app/Rules/NoGradeRangeOverlap.php` | Validates that a grade range `[min_mark, max_mark]` does not overlap existing grade ranges | `float $minMark`, `float $maxMark`, `?int $excludeId = null` |
| `app/Rules/MarkNotExceedFullMark.php` | Validates that `obtained_mark` does not exceed `full_mark` from exam_subjects table | `int $examSubjectId` |

### Form Requests (10)

| Request | Namespace | Route Binding (Update) | Status Values |
|---------|-----------|----------------------|---------------|
| `StoreExamTypeRequest` | `App\Http\Requests\Examination` | — | `active`, `inactive` |
| `UpdateExamTypeRequest` | `App\Http\Requests\Examination` | `exam_type` | `active`, `inactive` |
| `StoreExamRequest` | `App\Http\Requests\Examination` | — | `draft`, `published`, `completed` |
| `UpdateExamRequest` | `App\Http\Requests\Examination` | (none — no unique rules) | `draft`, `published`, `completed` |
| `StoreExamSubjectRequest` | `App\Http\Requests\Examination` | — | — |
| `UpdateExamSubjectRequest` | `App\Http\Requests\Examination` | (none — no unique rules) | — |
| `MarksEntryRequest` | `App\Http\Requests\Examination` | — | — |
| `MarksApprovalRequest` | `App\Http\Requests\Examination` | — | `Rule::enum(ApprovalStatus::class)` |
| `GradeStoreRequest` | `App\Http\Requests\Examination` | — | `active`, `inactive` |
| `GradeUpdateRequest` | `App\Http\Requests\Examination` | `grade` | `active`, `inactive` |

---

## Validation Rules Summary

### StoreExamTypeRequest
| Field | Rules |
|-------|-------|
| `name` | required, string, max:100, unique:exam_types |
| `code` | required, string, max:20, unique:exam_types |
| `description` | nullable, string |
| `status` | sometimes, string, Rule::in(['active', 'inactive']) |

### UpdateExamTypeRequest
Same as store but with `sometimes` for name/code + `Rule::unique()->ignore($this->route('exam_type'))`.

### StoreExamRequest
| Field | Rules |
|-------|-------|
| `exam_type_id` | required, exists:exam_types,id |
| `academic_year_id` | required, exists:academic_years,id |
| `semester_id` | required, exists:semesters,id |
| `department_id` | required, exists:departments,id |
| `program_id` | nullable, exists:programs,id |
| `shift_id` | required, exists:shifts,id |
| `section_id` | required, exists:sections,id |
| `title` | required, string, max:255 |
| `start_date` | required, date |
| `end_date` | required, date, after_or_equal:start_date |
| `status` | sometimes, string, Rule::in(['draft', 'published', 'completed']) |

### UpdateExamRequest
Same as store but with `sometimes` for required fields + `nullable` for `program_id`.

### StoreExamSubjectRequest
| Field | Rules |
|-------|-------|
| `exam_id` | required, exists:exams,id |
| `subject_id` | required, exists:subjects,id |
| `teacher_id` | required, exists:users,id |
| `full_mark` | required, numeric, gt:0 |
| `pass_mark` | required, numeric, min:0, lte:full_mark |
| `practical_mark` | nullable, numeric, min:0, lte:full_mark |
| `viva_mark` | nullable, numeric, min:0, lte:full_mark |

### MarksEntryRequest
| Field | Rules |
|-------|-------|
| `exam_subject_id` | required, exists:exam_subjects,id |
| `marks` | required, array, min:1 |
| `marks.*.student_id` | required, exists:students,id |
| `marks.*.obtained_mark` | required, numeric, min:0 + **MarkNotExceedFullMark** custom rule |
| `marks.*.practical_mark` | nullable, numeric, min:0 |
| `marks.*.viva_mark` | nullable, numeric, min:0 |
| `marks.*.remark` | nullable, string, max:500 |

### MarksApprovalRequest
| Field | Rules |
|-------|-------|
| `approval_status` | required, string, **Rule::enum(ApprovalStatus::class)** |
| `remark` | required_if:approval_status,rejected, string, max:500 |

### GradeStoreRequest
| Field | Rules |
|-------|-------|
| `grade_name` | required, string, max:50, unique:grades |
| `grade_letter` | required, string, max:10 |
| `min_mark` | required, numeric, min:0 + **NoGradeRangeOverlap** custom rule |
| `max_mark` | required, numeric, gte:min_mark |
| `gpa_point` | required, numeric, min:0, max:5 |
| `remarks` | nullable, string |
| `status` | sometimes, string, Rule::in(['active', 'inactive']) |

### GradeUpdateRequest
Same as store but with `sometimes` for required fields + `Rule::unique()->ignore($this->route('grade'))` + `NoGradeRangeOverlap` with `excludeId`.

---

## Custom Rules Detail

### `NoGradeRangeOverlap`
Rejects overlapping grade boundaries using:
```sql
SELECT EXISTS(
  SELECT 1 FROM grades
  WHERE max_mark >= :minMark
    AND min_mark <= :maxMark
    AND id != :excludeId  -- when updating
)
```
| Scenario | exludeId |
|----------|----------|
| Create | `null` |
| Update | `$this->route('grade')` (the grade ID being updated) |

### `MarkNotExceedFullMark`
Looks up `full_mark` from `exam_subjects` table using `$examSubjectId` passed from the request input.

---

## Architecture Compliance

| Rule | Status | Evidence |
|------|--------|----------|
| No business logic | ✅ | Pure validation only |
| No repositories | ✅ | Zero `Repository` imports |
| No services | ✅ | Zero `Service` imports |
| No models | ✅ | Zero `Model` imports |
| No DB queries except exists + custom rules | ✅ | `MarkNotExceedFullMark` and `NoGradeRangeOverlap` use `DB::table()` — no Eloquent |
| `Rule::enum()` used over `Rule::in()` for enums | ✅ | `ApprovalStatus` enum created; `Rule::enum(ApprovalStatus::class)` in MarksApprovalRequest |
| `Rule::in()` used for string sets only | ✅ | Exam status, exam_type status, grade status use `Rule::in()` |
| `authorize()` returns `true` | ✅ | All 10 requests follow same pattern |
| `declare(strict_types=1)` | ✅ | All 13 files |
| PSR-12 | ✅ | Space after control structures, 4-space indent, braces on new line |
| Constructor promotion NOT required | ✅ | Not used (follows `StoreDepartmentRequest` pattern) |
| Localization-ready messages via `messages()` | ✅ | All requests have custom messages following existing project patterns |
| Grade ranges never overlap | ✅ | `NoGradeRangeOverlap` custom rule on `min_mark` |
| Obtained mark ≤ full_mark | ✅ | `MarkNotExceedFullMark` custom rule |
| Approval status enum | ✅ | `ApprovalStatus` backed string enum + `Rule::enum()` |
| Remark required only when rejected | ✅ | `required_if:approval_status,rejected` |
| `approved_by` never from client | ✅ | No validation rule for `approved_by` |

---

## Verification Checklist

| Check | Status |
|-------|--------|
| `Rule::enum()` used where applicable | ✅ `ApprovalStatus` |
| No `Rule::in()` for enums | ✅ |
| No duplicated validation rules | ✅ |
| No business logic | ✅ |
| No controller modifications | ✅ |
| Full test suite passes (294 tests) | ✅ |
| `composer dump-autoload` succeeds (6860 classes) | ✅ |
