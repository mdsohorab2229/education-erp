# Phase 08 — Step 04: Service Layer

**Status:** Completed  
**Date:** 2026-07-04  
**Tests:** 294 passed, 0 failures (no regressions)

---

## Files Created

### Domain Exceptions (2)

| File | Path | Purpose |
|------|------|---------|
| `GradeNotFoundException` | `app/Exceptions/GradeNotFoundException.php` | Thrown when no grade boundary matches a mark |
| `InvalidApprovalStateException` | `app/Exceptions/InvalidApprovalStateException.php` | Thrown on invalid approval state transitions |

### Services (3)

| File | Path | Dependencies |
|------|------|-------------|
| `GradeCalculationService` | `app/Services/GradeCalculationService.php` | `GradeRepositoryInterface` |
| `MarksEntryService` | `app/Services/MarksEntryService.php` | `MarkRepositoryInterface`, `ExamSubjectRepositoryInterface`, `GradeCalculationService` |
| `MarksApprovalService` | `app/Services/MarksApprovalService.php` | `MarkRepositoryInterface` |

---

## GradeCalculationService

**Pure computation service — NO database writes. NO transactions.**

### Methods

| Method | Signature | Description |
|--------|-----------|-------------|
| `calculate` | `(float $mark): array` | Find grade for a single mark value |
| `calculateCollection` | `(array $marks): array` | Bulk grade calculation for multiple marks |

**Return format:**
```php
[
    'grade_id' => 3,
    'grade_name' => 'A',
    'grade_letter' => 'A',
    'gpa' => 4.00,
]
```

### Design

- Grades loaded **once** from `GradeRepositoryInterface::allOrdered()` and cached in `$this->grades` private property
- Uses **inclusive boundaries**: `$mark >= $g->min_mark && $mark <= $g->max_mark`
- Example: if `80-100 → A+` and `79.99 → A`, then `80` returns A+, `79.99` returns A
- Throws `GradeNotFoundException` if no matching grade found
- `calculateCollection` iterates once — grades are already cached from first calculation

---

## MarksEntryService

**Business logic for marks entry. All writes wrapped in DB::transaction().**

### Methods

| Method | Signature | Transaction | Description |
|--------|-----------|-------------|-------------|
| `bulkStore` | `(array $rows): array` | ✅ | Bulk upsert marks with grade calculation |
| `updateMark` | `(int $id, array $data): Mark` | ✅ | Update single mark, recalculate total & grade |
| `recalculate` | `(int $examSubjectId): Collection` | ✅ | Recalculate all marks for an exam subject |
| `getStudentMarks` | `(int $studentId): Collection` | ❌ | Read-only — delegates to repository |
| `getExamMarks` | `(int $examId): Collection` | ❌ | Read-only — delegates to repository |

### Business Logic in `bulkStore`

```
for each row:
  total_mark = obtained_mark + practical_mark + viva_mark
  grade = GradeCalculationService::calculate(total_mark)
  set grade_id
→ MarkRepository::bulkUpsert()
```

### Business Logic in `recalculate`

```
1. Fetch all marks for exam_subject_id
2. For each mark: recalculate total, recalculate grade
3. Bulk upsert all updated records
4. Return fresh collection
```

### Edge Cases Handled

- Null practical_mark/viva_mark treated as `0` in total calculation
- All decimals rounded to 2 places via `round()`
- Duplicate entries prevented by DB unique constraint + upsert

---

## MarksApprovalService

**Department Head approval workflow. All state changes wrapped in DB::transaction().**

### State Machine

```
┌─────────┐
│ pending │ ◄────────────────────────────┐
└────┬────┘                              │
     │                                   │
     ├──→ approve() → [approved]         │
     │                  ↓                │
     │              ⚠ Cannot modify      │
     │                                   │
     └──→ reject()  → [rejected]         │
                        ↓                │
                    ⚠ Cannot modify      │
                                         │
                    reset() ─────────────┘
                    (sets back to pending)
```

### Methods

| Method | Signature | Transaction | Description |
|--------|-----------|-------------|-------------|
| `approve` | `(int $markId, int $userId): Mark` | ✅ | Approve a pending mark |
| `reject` | `(int $markId, int $userId, string $remark): Mark` | ✅ | Reject a pending mark |
| `reset` | `(int $markId): Mark` | ✅ | Reset approved/rejected back to pending |
| `pendingList` | `(): Collection` | ❌ | Read-only — delegates to repository |

### Validation Rules

| Transition | Allowed From | Blocked From | Exception |
|-----------|-------------|-------------|-----------|
| `approve()` | `pending` | `approved`, `rejected` | `InvalidApprovalStateException` |
| `reject()` | `pending` | `approved`, `rejected` | `InvalidApprovalStateException` |
| `reset()` | `approved`, `rejected` | `pending` | `InvalidApprovalStateException` |

### Approval Audit Trail

- `approved_by` — records the user who approved/rejected
- `approved_at` — timestamp of the action
- `remark` — rejection reason (set on reject, cleared on reset)

---

## Architecture Compliance

| Rule | Status | Evidence |
|------|--------|----------|
| Services depend ONLY on Repository Interfaces | ✅ | `MarkRepositoryInterface`, `GradeRepositoryInterface`, `ExamSubjectRepositoryInterface` |
| No Eloquent Models used in services | ✅ | Zero `use App\Models\*` statements — only repository interfaces |
| No DB facade used in services | ✅ | `use Illuminate\Support\Facades\DB` only for `DB::transaction()` |
| No queries outside repositories | ✅ | All data access delegated to repository interfaces |
| Transactions handled only inside Services | ✅ | GradeCalculationService has NO transactions |
| GradeCalculationService: no DB writes | ✅ | Pure computation, no repository write methods called |
| GradeCalculationService: no hardcoded grades | ✅ | Reads all grade boundaries from `GradeRepository::allOrdered()` |
| GradeCalculationService: grades cached per instance | ✅ | `$this->grades` lazy-loaded property |
| MarksEntryService: calls GradeCalculationService | ✅ | Injected via constructor DI |
| MarksApprovalService: domain exceptions on invalid transitions | ✅ | `InvalidApprovalStateException` for all 3 invalid transitions |
| No business logic in repositories | ✅ | Not modified |
| No business logic in controllers | ✅ | No controllers created |
| Constructor dependency injection | ✅ | All services use `private readonly` constructor promotion |

---

## Performance Review

| Requirement | Status | Evidence |
|-------------|--------|----------|
| Never query grades table inside loop | ✅ | `GradeCalculationService::getGrades()` loads once, caches for entire instance lifetime |
| Never use Model::all() | ✅ | Not called anywhere |
| Bulk upsert for multiple marks | ✅ | `MarkRepository::bulkUpsert()` called from `bulkStore()` and `recalculate()` |
| No N+1 | ✅ | All data retrieval delegated to repositories with eager loads |
| No lazy loading | ✅ | Not triggered anywhere |

---

## Container Verification

All services resolve successfully via the Laravel container:

```php
app(GradeCalculationService::class)  // ✓ App\Services\GradeCalculationService
app(MarksEntryService::class)        // ✓ App\Services\MarksEntryService
app(MarksApprovalService::class)     // ✓ App\Services\MarksApprovalService
```

All dependencies are auto-wired — no additional `AppServiceProvider` bindings needed since services are concrete classes and repositories are already bound to their interfaces.
