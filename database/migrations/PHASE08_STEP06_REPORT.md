# Phase 08 — Step 06: API Resources

**Status:** Completed  
**Date:** 2026-07-04  
**Tests:** 294 passed, 0 failures (745 assertions — no regressions)

---

## Files Created

| Resource | Path | Description |
|----------|------|-------------|
| `ExamTypeResource` | `app/Http/Resources/ExamTypeResource.php` | Exam type serialization |
| `ExamResource` | `app/Http/Resources/ExamResource.php` | Exam serialization with 7 nested relationships |
| `ExamSubjectResource` | `app/Http/Resources/ExamSubjectResource.php` | Exam subject with subject + teacher |
| `MarkResource` | `app/Http/Resources/MarkResource.php` | Mark with student, grade, approved_by |
| `GradeResource` | `app/Http/Resources/GradeResource.php` | Grade serialization |

---

## Resource Structure Overview

### ExamTypeResource

```json
{
    "id": 1,
    "name": "Midterm",
    "code": "MID",
    "description": "...",
    "status": "active"
}
```

Never exposes: `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`.

---

### ExamResource

```json
{
    "id": 1,
    "title": "Midterm 2026",
    "exam_type": { "id": 1, "name": "Midterm" },
    "academic_year": { "id": 1, "name": "2026" },
    "semester": { "id": 1, "name": "Spring" },
    "department": { "id": 1, "name": "CSE" },
    "program": { "id": 1, "name": "BSc CSE" },
    "shift": { "id": 1, "name": "Morning" },
    "section": { "id": 1, "name": "A" },
    "start_date": "2026-03-01",
    "end_date": "2026-03-15",
    "status": "published",
    "total_subjects": 5
}
```

| Feature | Method |
|---------|--------|
| All 7 relationships | `whenLoaded()` with inline closure |
| Date formatting | `?->format('Y-m-d')` |
| Subject count | `whenCounted('examSubjects', fn () => $this->exam_subjects_count)` |

Never exposes: `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, foreign key IDs.

---

### ExamSubjectResource

```json
{
    "id": 1,
    "subject": { "id": 1, "name": "Data Structures", "code": "CSE201" },
    "teacher": { "id": 5, "name": "John Doe" },
    "full_mark": 100.0,
    "pass_mark": 40.0,
    "practical_mark": 30.0,
    "viva_mark": 10.0
}
```

| Feature | Detail |
|---------|--------|
| Decimal fields | Cast to `(float)` — matches `AssignmentResource` convention |
| Nullable fields | `practical_mark` / `viva_mark` return `null` when not set |
| Relationships | `subject` (id, name, code), `teacher` (id, name) — both via `whenLoaded()` |

Never exposes: timestamps, `created_by`, `updated_by`, FK IDs.

---

### MarkResource

```json
{
    "student": {
        "id": 1,
        "admission_no": "STU-001",
        "roll_no": "101",
        "name": "Jane Doe",
        "photo": "photos/jane.jpg"
    },
    "obtained_mark": 85.5,
    "practical_mark": 15.0,
    "viva_mark": 8.0,
    "total_mark": 108.5,
    "grade": {
        "id": 3,
        "grade_name": "A+",
        "grade_letter": "A+",
        "gpa_point": 4.0
    },
    "approval_status": "pending",
    "approved_by": null,
    "approved_at": null,
    "remark": null
}
```

| Feature | Detail |
|---------|--------|
| Student name | Uses `$this->student->full_name` accessor |
| Decimal fields | Cast to `(float)` with null guard |
| `approved_by` | Null-safe: `$this->approvedBy ? [...] : null` inside `whenLoaded()` |
| `approved_at` | Formatted `'Y-m-d H:i:s'` from datetime cast |
| Grade | Inline with `(float) $this->grade->gpa_point` |

Never exposes: `created_at`, `updated_at`, `created_by`, `updated_by`, `grade_id`, `student_id`, `exam_subject_id`, `approved_by_id`.

---

### GradeResource

```json
{
    "id": 1,
    "grade_name": "A+",
    "grade_letter": "A+",
    "min_mark": 80.0,
    "max_mark": 100.0,
    "gpa_point": 4.0,
    "remarks": "Excellent",
    "status": "active"
}
```

Never exposes: timestamps.

---

## Architecture Compliance

| Rule | Status | Evidence |
|------|--------|----------|
| Extends `JsonResource` | ✅ | All 5 resources |
| No DB queries | ✅ | Zero `DB::`, `Model::`, `Repository` calls |
| No business logic | ✅ | Pure response formatting only |
| No lazy loading | ✅ | Uses `whenLoaded()` exclusively for relationships |
| No timestamps exposed | ✅ | Not included in any `toArray()` |
| No FK IDs exposed | ✅ | All FKs (grade_id, student_id, etc.) omitted from output |
| `declare(strict_types=1)` | ✅ | All 5 files |
| PSR-12 | ✅ | Consistent with existing resources |
| `whenCounted()` for aggregation | ✅ | `total_subjects` in ExamResource |
| Null-safe optional fields | ✅ | `approved_by`, `practical_mark`, `viva_mark` etc. |
| Decimal → float casting | ✅ | Matches `AssignmentResource` convention |

---

## Relationship Loading Summary

| Resource | Relations | Loading Method |
|----------|-----------|----------------|
| `ExamTypeResource` | — | No relationships |
| `ExamResource` | examType, academicYear, semester, department, program, shift, section | `whenLoaded()` inline |
| `ExamSubjectResource` | subject, teacher | `whenLoaded()` inline |
| `MarkResource` | student, grade, approvedBy | `whenLoaded()` inline |
| `GradeResource` | — | No relationships |
