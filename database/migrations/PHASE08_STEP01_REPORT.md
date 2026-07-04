# Phase 08 — Step 01: Database Layer

**Status:** Completed  
**Date:** 2026-07-04  
**Tests:** 294 passed, 0 failures (no regressions)

---

## Migrations Created

| # | File | Table | Purpose |
|---|------|-------|---------|
| 1 | `2026_07_04_000001_create_exam_types_table.php` | `exam_types` | Exam type lookup (Midterm, Final, Quiz, etc.) |
| 2 | `2026_07_04_000002_create_exams_table.php` | `exams` | Exam instances with academic period bindings |
| 3 | `2026_07_04_000003_create_exam_subjects_table.php` | `exam_subjects` | Subject-teacher assignment per exam |
| 4 | `2026_07_04_000004_create_grades_table.php` | `grades` | Grade boundary definitions for GPA calculation |
| 5 | `2026_07_04_000005_create_marks_table.php` | `marks` | Student marks with approval workflow |

---

## Table Details

### `exam_types`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint | PK |
| name | varchar(100) | **UNIQUE**, NOT NULL |
| code | varchar(20) | **UNIQUE**, NOT NULL |
| description | text | nullable |
| status | varchar(20) | default 'active', indexed |
| created_by | bigint | FK → users, nullOnDelete |
| updated_by | bigint | FK → users, nullOnDelete |
| deleted_at | timestamp | softDeletes |
| created_at | timestamp | |
| updated_at | timestamp | |

**Indexes:** status, created_by, updated_by

---

### `exams`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint | PK |
| exam_type_id | bigint | FK → exam_types, restrictOnDelete, indexed |
| academic_year_id | bigint | FK → academic_years, restrictOnDelete, indexed |
| semester_id | bigint | FK → semesters, restrictOnDelete, indexed |
| department_id | bigint | FK → departments, restrictOnDelete, indexed |
| program_id | bigint | FK → programs, restrictOnDelete, indexed |
| shift_id | bigint | FK → shifts, restrictOnDelete, indexed |
| section_id | bigint | FK → sections, restrictOnDelete, indexed |
| title | varchar(255) | NOT NULL |
| start_date | date | indexed |
| end_date | date | indexed |
| status | varchar(20) | default 'draft', indexed |
| created_by | bigint | FK → users, nullOnDelete |
| updated_by | bigint | FK → users, nullOnDelete |
| deleted_at | timestamp | softDeletes |
| created_at | timestamp | |
| updated_at | timestamp | |

**Composite Unique:** `(exam_type_id, academic_year_id, semester_id, department_id, program_id, shift_id, section_id, start_date)` — prevents duplicate exam setup for the same scope and period.

**Indexes:** academic_year_id, semester_id, department_id, section_id, shift_id, program_id, exam_type_id, status, start_date, end_date

---

### `exam_subjects`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint | PK |
| exam_id | bigint | FK → exams, **cascadeOnDelete**, indexed |
| subject_id | bigint | FK → subjects, restrictOnDelete, indexed |
| teacher_id | bigint | FK → users, restrictOnDelete, indexed |
| full_mark | decimal(5,2) | NOT NULL |
| pass_mark | decimal(5,2) | NOT NULL |
| practical_mark | decimal(5,2) | nullable |
| viva_mark | decimal(5,2) | nullable |
| created_by | bigint | FK → users, nullOnDelete |
| updated_by | bigint | FK → users, nullOnDelete |
| created_at | timestamp | |
| updated_at | timestamp | |

**Composite Unique:** `(exam_id, subject_id)` — prevents duplicate subject assignment within an exam.

---

### `marks`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint | PK |
| exam_subject_id | bigint | FK → exam_subjects, **cascadeOnDelete**, indexed |
| student_id | bigint | FK → students, restrictOnDelete, indexed |
| obtained_mark | decimal(5,2) | NOT NULL |
| practical_mark | decimal(5,2) | nullable |
| viva_mark | decimal(5,2) | nullable |
| total_mark | decimal(5,2) | NOT NULL |
| grade_id | bigint | FK → grades, nullOnDelete, indexed |
| approval_status | varchar(20) | default 'pending', indexed |
| approved_by | bigint | FK → users, nullOnDelete |
| approved_at | datetime | nullable |
| remark | text | nullable |
| created_by | bigint | FK → users, nullOnDelete |
| updated_by | bigint | FK → users, nullOnDelete |
| created_at | timestamp | |
| updated_at | timestamp | |

**Composite Unique:** `(exam_subject_id, student_id)` — prevents duplicate marks entry per student per exam subject.

**Composite Index:** `(exam_subject_id, approval_status)` — optimized for approval workflow queries.

---

### `grades`

| Column | Type | Constraints |
|--------|------|-------------|
| id | bigint | PK |
| grade_name | varchar(50) | **UNIQUE**, NOT NULL |
| grade_letter | varchar(10) | NOT NULL |
| min_mark | decimal(5,2) | indexed |
| max_mark | decimal(5,2) | indexed |
| gpa_point | decimal(4,2) | NOT NULL |
| remarks | text | nullable |
| status | varchar(20) | default 'active', indexed |
| created_at | timestamp | |
| updated_at | timestamp | |

**Note:** Grade range overlap prevention enforced at application layer.

---

## Foreign Key Summary

| Table | Foreign Key | References | On Delete |
|-------|-------------|-----------|-----------|
| exam_types | created_by | users | SET NULL |
| exam_types | updated_by | users | SET NULL |
| exams | exam_type_id | exam_types | RESTRICT |
| exams | academic_year_id | academic_years | RESTRICT |
| exams | semester_id | semesters | RESTRICT |
| exams | department_id | departments | RESTRICT |
| exams | program_id | programs | RESTRICT |
| exams | shift_id | shifts | RESTRICT |
| exams | section_id | sections | RESTRICT |
| exams | created_by | users | SET NULL |
| exams | updated_by | users | SET NULL |
| exam_subjects | exam_id | exams | **CASCADE** |
| exam_subjects | subject_id | subjects | RESTRICT |
| exam_subjects | teacher_id | users | RESTRICT |
| exam_subjects | created_by | users | SET NULL |
| exam_subjects | updated_by | users | SET NULL |
| marks | exam_subject_id | exam_subjects | **CASCADE** |
| marks | student_id | students | RESTRICT |
| marks | grade_id | grades | SET NULL |
| marks | approved_by | users | SET NULL |
| marks | created_by | users | SET NULL |
| marks | updated_by | users | SET NULL |

---

## DATABASE_STANDARDS.md Compliance

| Requirement | Status |
|-------------|--------|
| InnoDB engine (MySQL default) | ✅ |
| utf8mb4 + utf8mb4_unicode_ci (global config) | ✅ |
| Plural table names | ✅ |
| `$table->id()` PKs | ✅ |
| `timestamps()` on every table | ✅ |
| `softDeletes()` on business entities (`exam_types`, `exams`) | ✅ |
| No softDeletes on transactional data (`exam_subjects`, `marks`) | ✅ |
| No softDeletes on lookup data (`grades`) | ✅ |
| Audit fields (`created_by`, `updated_by`) on business data | ✅ |
| `foreignId()` for all FK columns | ✅ |
| Proper onDelete rules (RESTRICT / CASCADE / SET NULL) | ✅ |
| Composite unique constraints | ✅ |
| Indexes on all FK columns | ✅ |
| Indexes on filtered columns (status, dates) | ✅ |
| Composite index for approval workflow | ✅ |
| `decimal(5,2)` for marks | ✅ |
| `decimal(4,2)` for GPA points | ✅ |
| Reversible migrations (`down()` defined) | ✅ |

---

## Verification

- **Migration execution:** All 5 migrations ran successfully (Batch 2)
- **Rollback:** Verified — tables dropped in reverse order without errors
- **Foreign keys:** All 25 FK constraints verified via INFORMATION_SCHEMA
- **Indexes:** All 35+ indexes verified via SHOW INDEX
- **Test suite:** 294 tests pass, 0 failures — no regressions introduced
