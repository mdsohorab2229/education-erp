# Phase_05_Attendance_Implementation_Prompt.md

# Phase 05 — Student Attendance Module Implementation

Version: 1.0

Status: Approved

Framework

- Laravel 12+
- PHP 8.3+
- MySQL 8+

---

# IMPORTANT

Before generating ANY code you MUST read and follow ALL project documentation.

Read these files in the following order.

1.

AGENTS.md

2.

DATABASE_STANDARDS.md

3.

API_STANDARDS.md

4.

UI_STANDARDS.md

5.

Attendance_Module_Specification.md

6.

Attendance_Database.md

7.

Attendance_API.md

8.

Attendance_UI.md

9.

Attendance_Test_Cases.md

All documents are mandatory.

Never ignore any rule.

---

# Goal

Implement the complete Student Attendance module.

The module must be

- Production Ready
- Scalable
- Maintainable
- AJAX Based
- Mobile Friendly

The implementation must follow Repository-Service Architecture.

---

# Architecture Rules

Strictly follow

Controller

↓

Service

↓

Repository Interface

↓

Repository Implementation

↓

Eloquent Model

↓

Database

Controllers must remain thin.

Business logic belongs only inside Services.

Repositories are the only layer allowed to access Eloquent.

Never place business logic inside

- Controllers
- Models
- Blade Views

---

# Coding Standards

Must follow

- SOLID
- DRY
- KISS
- PSR-12
- Strict Types

Every PHP class must use

```php
declare(strict_types=1);
```

Use constructor dependency injection.

Avoid static calls unless required by Laravel.

---

# Module Scope

Only implement

Student Attendance Module.

Do NOT implement

- Parent Notification
- SMS
- Email
- Attendance Approval
- Holiday Calendar
- Monthly Reports
- Analytics
- Teacher Module
- Student Module
- Result Module

Anything outside Attendance is out of scope.

---

# Database

Create only

attendance_sessions

attendance_records

Follow

Attendance_Database.md

Requirements

- Foreign Keys
- Composite Unique Keys
- Indexes
- Audit Fields
- Timestamps

---

# Models

Create

AttendanceSession

AttendanceRecord

Define all relationships.

Use eager loading where appropriate.

---

# Repository Layer

Create

AttendanceRepositoryInterface

AttendanceRepository

Responsibilities

- Load students
- Find or create session
- Save attendance
- Update attendance
- Bulk update
- Load attendance history

Repositories must never contain business rules.

---

# Service Layer

Create

LoadStudentsForAttendanceService

Responsibilities

- Validate filters
- Find or create attendance session
- Load enrolled students
- Load existing attendance records

Create

SaveAttendanceService

Responsibilities

- Save single attendance
- Update summary counts
- Prevent duplicates
- Transaction handling

Create

BulkAttendanceService

Responsibilities

- Mark All Present
- Mark All Absent
- Mark All Late
- Mark All Leave
- Clear Attendance

Use DB::transaction()

where multiple records are updated.

---

# Form Requests

Create

AttendanceFilterRequest

AttendanceUpdateRequest

AttendanceBulkUpdateRequest

Validation rules must follow

Attendance_API.md

---

# API Endpoints

Implement

POST

/attendance/load-students

POST

/attendance/update

POST

/attendance/bulk-update

GET

/attendance/session/{id}

Return

JSON only.

Follow

API_STANDARDS.md

---

# API Response

Always return

```json
{
    "success": true,
    "message": "",
    "data": {}
}
```

Validation

422

Authorization

403

Unauthenticated

401

---

# Blade UI

Follow

Attendance_UI.md

Create

Attendance Index Page

Include

Header

Filter Card

Summary Card

Attendance Table

Bulk Action Bar

Search

Loading Spinner

Toast Notification

Use

Bootstrap 5

Alpine.js

Axios

SweetAlert2

Flatpickr

Tom Select

No Livewire.

No jQuery AJAX.

Only Axios.

---

# Attendance Table

Columns

Roll Number

Photo

Student ID

Student Name

Present

Absent

Late

Leave

Remark

Save Status

Attendance Status

must use

Radio Buttons.

---

# AJAX Rules

Teacher clicks

↓

Present

↓

Axios Request

↓

Database Update

↓

Summary Update

↓

Saved

No page reload.

Update one student only.

Minimal payload.

---

# Performance Rules

Never

SELECT *

Never

N+1 Queries

Always

Eager Load

Always

Pagination

Only update

one attendance record

per request.

Target

Support

500 Students

per class.

---

# Security

Use

Authentication

Authorization

Role Permission Middleware

Validate all inputs.

Prevent duplicate sessions.

Prevent duplicate attendance records.

---

# Feature Tests

Implement all test cases defined in

Attendance_Test_Cases.md

Including

Authentication

Authorization

Validation

Session Creation

Duplicate Prevention

Attendance Update

Bulk Update

AJAX

Database Constraints

Performance

---

# Generation Order

Generate files in this order

1.

Migration

2.

Models

3.

Repository Interface

4.

Repository Implementation

5.

Services

6.

Form Requests

7.

API Resources

8.

Controllers

9.

Routes

10.

Blade Views

11.

Factories

12.

Seeders

13.

Feature Tests

Never skip steps.

---

# Scope Rules

Never modify unrelated modules.

Never refactor existing code.

Never rename existing classes.

Never update global configuration

unless absolutely required.

Only create files required for Attendance.

---

# Completion Checklist

Before finishing

Verify

✓ Migrations run successfully

✓ Foreign keys work

✓ Composite indexes exist

✓ Repositories implemented

✓ Services implemented

✓ Controllers remain thin

✓ Validation works

✓ AJAX works

✓ Summary updates

✓ Bulk actions work

✓ No page reload

✓ Feature tests pass

✓ No N+1 queries

✓ Code follows AGENTS.md

---

# Final Output

After implementation provide

1.

Created Files

2.

Modified Files

3.

Database Changes

4.

Routes Added

5.

Feature Test Result

6.

Migration Result

7.

Performance Notes

8.

Known Limitations (if any)

Then STOP.

Do not start another module.