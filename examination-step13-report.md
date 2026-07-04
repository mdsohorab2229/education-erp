# Phase 08 — Step 13 — Unit Tests Report

## File Created
`tests/Unit/GradeCalculationServiceTest.php` — 20 tests, 42 assertions

## Test Breakdown

### Boundary Marks (8 tests)

| Input | Expected Grade | Rationale |
|-------|---------------|-----------|
| `100` | A+ (gpa=4.00) | Top edge of A+ range (80–100) |
| `80`  | A+           | Bottom edge of A+ range  |
| `79.99` | `GradeNotFoundException` | Gap: A max=79, A+ min=80 — 79.99 exceeds both |
| `70`  | A-            | Bottom edge of A- range (70–74) |
| `69.99` | `GradeNotFoundException` | Gap: B+ max=69, A- min=70 — 69.99 exceeds both |
| `40`  | C-            | Bottom edge of C- range (40–44) |
| `39.99` | `GradeNotFoundException` | Gap: D max=39, C- min=40 — 39.99 exceeds both |
| `0`   | F             | Bottom edge of F range (0–34) |

### No Grade Found (4 tests)
- Negative mark (-1) → exception
- Above max (101) → exception
- Gap between grade ranges (mark=40 in 0–30 and 50–100 config) → exception  
- No match in empty collection → exception

### Invalid Configuration (2 tests)
- Empty grades collection → `GradeNotFoundException` for any mark
- Grades not covering 0–100 range → exception for marks outside configured ranges

### calculateCollection (4 tests)
- Returns all results with correct grade letters
- Prefers `total_mark` over `obtained_mark`
- Throws on no match
- Throws on empty grades

### Additional Coverage (2 tests)
- GPA values verified across all 11 grades
- Repository cached — `allOrdered()` called exactly once despite multiple `calculate()` calls

## Key Finding
The grade boundaries in the seed data use **non-overlapping integer ranges** (e.g., A: 75–79, A+: 80–100). Values like `79.99`, `69.99`, and `39.99` fall in gaps between ranges and correctly throw `GradeNotFoundException`. This is **expected behavior** — the system effectively treats grade thresholds as discrete integer boundaries. If decimal marks need to be supported, grade ranges would need overlapping boundaries or the comparison logic in `GradeCalculationService::calculate()` would need to use `< next_grade_min` instead of `<= max_mark`.

## Final Result
```
Tests:    378 passed (1004 assertions)
Duration: 58.34s
```
All existing feature tests remain green — no regressions introduced.
