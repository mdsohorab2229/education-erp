# Phase 08 — Examination Module

Status: Ready for Development

Framework
- Laravel 12
- PHP 8.3+
- MySQL 8+
- Bootstrap 5
- Alpine.js
- Axios
- SweetAlert2

---

# Objective

Implement a complete Examination Management module.

This phase covers only examination setup, marks entry, approval workflow,
and grade calculation.

❌ Result Publish is NOT part of this phase.

❌ Tabulation is NOT part of this phase.

❌ Report Card is NOT part of this phase.

These belong to Phase 09.

---

# Architecture

The module MUST follow AGENTS.md.

Controller
↓

Service

↓

Repository Interface

↓

Repository

↓

Model

↓

Database

Business logic is NOT allowed outside Service classes.

---

# Module Scope

## Database

Create the following tables.

exam_types

- id
- name
- code
- description
- status
- created_by
- updated_by
- timestamps
- softDeletes

---

exams

- id
- exam_type_id
- academic_year_id
- semester_id
- department_id
- program_id
- shift_id
- section_id
- title
- start_date
- end_date
- status
- created_by
- updated_by
- timestamps
- softDeletes

Indexes

- academic_year_id
- semester_id
- department_id
- section_id
- status

---

exam_subjects

- id
- exam_id
- subject_id
- teacher_id
- full_mark
- pass_mark
- practical_mark nullable
- viva_mark nullable
- created_by
- updated_by
- timestamps

Composite Unique

exam_id
+
subject_id

---

marks

- id
- exam_subject_id
- student_id
- obtained_mark
- practical_mark nullable
- viva_mark nullable
- total_mark
- grade_id nullable
- approval_status

pending

approved

rejected

- approved_by nullable
- approved_at nullable
- remark nullable
- created_by
- updated_by
- timestamps

Composite Unique

exam_subject_id
+
student_id

---

grades

- id
- grade_name
- grade_letter
- min_mark
- max_mark
- gpa_point
- remarks
- status
- timestamps

Unique

grade_name

Grade ranges must never overlap.

---

# Models

Create

ExamType

Exam

ExamSubject

Mark

Grade

Relationships only.

No business logic.

---

# Repository Layer

Create

ExamRepositoryInterface

ExamRepository

ExamSubjectRepositoryInterface

ExamSubjectRepository

MarkRepositoryInterface

MarkRepository

GradeRepositoryInterface

GradeRepository

Repositories contain ONLY database queries.

---

# Services

## MarksEntryService

Responsibilities

Bulk marks entry

Bulk update

Transaction

Summary

No duplicate marks

Upsert support

---

## MarksApprovalService

Responsibilities

Approve

Reject

Approval history

Department Head workflow

Prevent double approval

---

## GradeCalculationService

Responsibilities

Calculate grade

Calculate GPA

Read grade boundaries from grades table

No hardcoded grades

Support configurable grading

Boundary safe

Example

80

A+

79.99

A

74.99
A-

69.99
B+

---

# Validation

Create

StoreExamRequest

UpdateExamRequest

StoreMarksRequest

ApproveMarksRequest

Rules

Obtained mark

>=0

<= Full Mark

Practical mark

<= Practical Full Mark

Viva mark

<= Viva Full Mark

Reject invalid ranges.

---

# API Resources

Create

ExamResource

ExamSubjectResource

MarkResource

GradeResource

Return only required fields.

Never expose timestamps.

---

# Controllers

Thin Controllers only.

ExamController

MarksEntryController

MarksApprovalController

Controller responsibilities

Receive Request

Call ONE Service

Return Resource/View

Nothing else.

---

# Blade UI

Exam Setup

CRUD

Exam Subject Setup

Marks Entry

Spreadsheet style

Keyboard navigation

Arrow keys

Enter key

Tab key

Paste from Excel

Bulk Save

Autosave indicator

Approval Dashboard

Pending

Approved

Rejected

Print friendly

Responsive

Bootstrap 5

Alpine.js

Axios

SweetAlert2

---

# Feature Tests

Authentication

Authorization

CRUD

Validation

Approval workflow

Duplicate marks

Grade calculation

Bulk marks entry

Repository binding

Soft delete

---

# Unit Tests

GradeCalculationService

Boundary Tests

100

80

79.99

74.99

69.99

40

39.99


Ensure correct GPA.

---

# Factories

ExamFactory

ExamSubjectFactory

MarkFactory

GradeFactory

ExamTypeFactory

---

# Seeders

ExamDemoSeeder

ExamTestSeeder

GradeSeeder

DatabaseSeeder registration required.

---

# Sidebar

Must register

Examination

Exam Types

Exams

Marks Entry

Marks Approval

Only show by permissions.

---

# Permissions

exam-list

exam-create

exam-edit

exam-delete

marks-entry

marks-approve

grade-list

grade-edit

Seeder update required.

---

# Route Structure

admin/exams

admin/exam-types

admin/marks

admin/marks/approval

Follow existing route naming convention.

---

# Performance

Eager Loading

No N+1

Bulk Upsert

Pagination

Select only required columns

Repository caching where applicable

---

# Security

CSRF

Authorization

Mass Assignment protection

Validation

Repository only DB access

Service only business logic

---

# Development Order

Step 01

Database

↓

Step 02

Models

↓

Step 03

Repositories

↓

Step 04

Services

↓

Step 05

Form Requests

↓

Step 06

API Resources

↓

Step 07

Controllers

↓

Step 08

Routes

↓

Step 09

Blade UI

↓

Step 10

Factories

↓

Step 11

Seeders

↓

Step 12

Feature Tests

↓

Step 13

Unit Tests

↓

Sprint 1

Production Hardening

---

# Completion Checklist

□ Sidebar updated

□ Permissions seeded

□ Routes registered

□ Factory created

□ Seeder created

□ DatabaseSeeder updated

□ Feature Tests passing

□ Unit Tests passing

□ php artisan test passes

□ php artisan optimize passes

□ No AGENTS.md violations