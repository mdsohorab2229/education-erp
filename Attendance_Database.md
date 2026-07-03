# Attendance_Database.md

Version: 1.0

Status: Approved

Module: Student Attendance

Project: Education ERP

Dependencies

- AGENTS.md
- DATABASE_STANDARDS.md
- API_STANDARDS.md
- UI_STANDARDS.md
- Attendance_Module_Specification.md

---

# Part 1 — Module Overview

## 1. Purpose

The Attendance Module is responsible for recording, updating, tracking and reporting student attendance on a daily basis.

The module is designed as an independent ERP module and must be reusable across schools, colleges and universities.

The attendance workflow is optimized for fast classroom usage through AJAX-based auto-save without requiring full page refreshes.

---

## 2. Objectives

The module shall:

- Record daily attendance
- Prevent duplicate attendance
- Support subject-wise attendance
- Support section-wise attendance
- Support teacher-wise attendance
- Maintain attendance history
- Support future reporting
- Support future mobile applications
- Support REST API
- Support future analytics

---

## 3. Design Goals

The database design must be

- Normalized
- Scalable
- Transaction Safe
- High Performance
- Future Ready
- Multi Academic Session Ready
- Mobile Ready
- Reporting Ready

---

## 4. Module Scope

This module includes

- Attendance Session
- Attendance Record

Future modules

NOT INCLUDED

- Parent Notification
- Attendance Approval
- Holiday Calendar
- Attendance Lock
- Attendance Analytics
- Attendance Report
- Attendance Dashboard
- Attendance Export

---

# Database Architecture

Attendance consists of two primary tables.

```
Attendance Session

↓

Attendance Records
```

Attendance Session

Stores

One attendance event for one class on one day.

Attendance Record

Stores

Attendance of every student.

---

# High Level Architecture

```
Academic Year
        │
Semester
        │
Department
        │
Shift
        │
Group
        │
Section
        │
Subject
        │
Attendance Session
        │
Attendance Records
        │
Student
```

---

# ERD (Entity Relationship Diagram)

```
AcademicYear
      │
      │
Semester
      │
      │
Department
      │
      │
Shift
      │
      │
Group
      │
      │
Section
      │
      │
Subject
      │
      │
Teacher
      │
      │
AttendanceSession
      │
      ├───────────────┐
      │               │
      │               │
AttendanceRecord      │
      │               │
      │               │
Student───────────────┘
```

---

# Relationship Overview

AttendanceSession

belongsTo

AcademicYear

AttendanceSession

belongsTo

Semester

AttendanceSession

belongsTo

Department

AttendanceSession

belongsTo

Shift

AttendanceSession

belongsTo

Group

AttendanceSession

belongsTo

Section

AttendanceSession

belongsTo

Subject

AttendanceSession

belongsTo

Teacher

AttendanceSession

hasMany

AttendanceRecord

AttendanceRecord

belongsTo

AttendanceSession

AttendanceRecord

belongsTo

Student

---

# Relationship Summary

AcademicYear

1

↓

Many

AttendanceSession

Semester

1

↓

Many

AttendanceSession

Department

1

↓

Many

AttendanceSession

Section

1

↓

Many

AttendanceSession

AttendanceSession

1

↓

Many

AttendanceRecord

Student

1

↓

Many

AttendanceRecord

---

# Business Rules

## Rule 1

One Attendance Session

per

Academic Year

Semester

Department

Shift

Group

Section

Subject

Attendance Date

Only one attendance session may exist.

Duplicate sessions are prohibited.

---

## Rule 2

One student

can have

only one attendance record

inside

one attendance session.

Duplicate attendance records are prohibited.

---

## Rule 3

Attendance cannot exist

without

Attendance Session.

---

## Rule 4

Attendance Session cannot exist

without

Teacher.

---

## Rule 5

Attendance Session cannot exist

without

Subject.

---

## Rule 6

Attendance Date

is mandatory.

---

## Rule 7

Attendance Status

Allowed values

Present

Absent

Late

Leave

Future

Holiday

Official Duty

---

## Rule 8

Attendance must support

AJAX Auto Save.

Each request

updates

one student only.

---

## Rule 9

Bulk Attendance

is only a shortcut.

Internally

it performs

multiple single-row updates.

---

## Rule 10

Deleting Attendance Session

must also delete

Attendance Records.

---

## Rule 11

Student deletion

must never automatically delete attendance history.

Attendance history is preserved.

---

## Rule 12

Attendance records are immutable historical data.

Updates modify status only.

Physical deletion should be avoided unless explicitly authorized.

---

# Data Integrity Rules

The database must enforce

Foreign Keys

Unique Constraints

Composite Unique Constraints

Indexes

Never rely only on Laravel validation.

---

# Transaction Rules

The following operations must execute inside a database transaction:

- Attendance Session Creation
- First Attendance Record Creation
- Bulk Attendance Update
- Attendance Import

Single-row attendance updates may use atomic upsert operations where appropriate.

---

# Performance Strategy

The module is designed for classes containing

50

↓

500

students.

Requirements

- Eager Loading
- Indexed Foreign Keys
- Composite Unique Indexes
- Minimal Payload AJAX
- Single Row Update
- No Full Page Reload

---

# Future Scalability

The database design supports future implementation of

- Monthly Attendance
- Student Attendance History
- Teacher Attendance Summary
- Subject Attendance Report
- Department Attendance Report
- Attendance Analytics
- Attendance Heatmap
- Attendance Approval Workflow
- Attendance Lock
- Holiday Calendar
- Parent Notification
- PDF Export
- Excel Export
- Mobile Application
- REST API

without requiring schema redesign.

---

# Database Design Principles

The Attendance database follows

- Third Normal Form (3NF)
- Repository Pattern Compatibility
- Service Layer Compatibility
- REST API Compatibility
- AJAX-first Architecture
- Future Multi-Campus Compatibility

---

# Acceptance Criteria

The database architecture is considered complete only if:

- Attendance Session uniquely identifies a class, subject and date.
- Attendance Record uniquely identifies a student within a session.
- All foreign keys are enforced.
- Duplicate sessions are impossible.
- Duplicate attendance records are impossible.
- Historical data integrity is maintained.
- Database design supports future reporting and analytics.
- Schema follows DATABASE_STANDARDS.md.

# Attendance_Database.md

---

# Part 2A — attendance_sessions Table Specification

## Table Name

attendance_sessions

---

# Purpose

The attendance_sessions table represents a single attendance event for one class on one attendance date.

A session uniquely identifies

- Academic Year
- Semester
- Department
- Shift
- Group
- Section
- Subject
- Teacher
- Attendance Date

Every attendance record belongs to exactly one attendance session.

---

# Table Type

Master Transaction Table

Contains

- Session Metadata
- Attendance Summary
- Attendance Statistics

Does NOT contain

Individual Student Attendance

Student attendance is stored in attendance_records.

---

# Primary Key

```php
id
```

Type

BIGINT UNSIGNED

Auto Increment

YES

Primary Key

YES

---

# Columns

| Column | Datatype | Nullable | Default | Index | FK |
|----------|----------|----------|----------|--------|----|
| id | BIGINT UNSIGNED | No | Auto | PK | No |
| academic_year_id | BIGINT UNSIGNED | No | — | Yes | Yes |
| semester_id | BIGINT UNSIGNED | No | — | Yes | Yes |
| department_id | BIGINT UNSIGNED | No | — | Yes | Yes |
| shift_id | BIGINT UNSIGNED | No | — | Yes | Yes |
| group_id | BIGINT UNSIGNED | Yes | NULL | Yes | Yes |
| section_id | BIGINT UNSIGNED | No | — | Yes | Yes |
| subject_id | BIGINT UNSIGNED | No | — | Yes | Yes |
| teacher_id | BIGINT UNSIGNED | No | — | Yes | Yes |
| attendance_date | DATE | No | — | Yes | No |
| total_students | INTEGER UNSIGNED | No | 0 | No | No |
| present_count | INTEGER UNSIGNED | No | 0 | No | No |
| absent_count | INTEGER UNSIGNED | No | 0 | No | No |
| late_count | INTEGER UNSIGNED | No | 0 | No | No |
| leave_count | INTEGER UNSIGNED | No | 0 | No | No |
| remarks | TEXT | Yes | NULL | No | No |
| status | ENUM | No | open | Yes | No |
| created_by | BIGINT UNSIGNED | Yes | NULL | Yes | Yes |
| updated_by | BIGINT UNSIGNED | Yes | NULL | Yes | Yes |
| created_at | TIMESTAMP | No | Current | No | No |
| updated_at | TIMESTAMP | No | Current | No | No |

---

# Column Details

## academic_year_id

Purpose

Identify Academic Session.

Datatype

BIGINT UNSIGNED

Constraint

Required

Foreign Key

academic_years.id

Delete Rule

RESTRICT

---

## semester_id

Purpose

Semester

Datatype

BIGINT UNSIGNED

Required

Foreign Key

semesters.id

Delete Rule

RESTRICT

---

## department_id

Purpose

Department

Foreign Key

departments.id

Required

Delete Rule

RESTRICT

---

## shift_id

Purpose

Morning

Day

Evening

Foreign Key

shifts.id

Required

---

## group_id

Purpose

Science

Commerce

Arts

Nullable

YES

Reason

Some institutions do not use Groups.

---

## section_id

Purpose

Class Section

Required

Foreign Key

sections.id

---

## subject_id

Purpose

Attendance Subject

Required

Foreign Key

subjects.id

---

## teacher_id

Purpose

Teacher taking attendance.

Required

Foreign Key

users.id

Reason

Teacher is authenticated User.

---

## attendance_date

Purpose

Attendance Date

Datatype

DATE

Format

YYYY-MM-DD

Never store datetime.

---

## total_students

Purpose

Number of loaded students.

Automatically calculated.

Never manually edited.

---

## present_count

Purpose

Summary

Auto Updated

---

## absent_count

Purpose

Summary

Auto Updated

---

## late_count

Purpose

Summary

Auto Updated

---

## leave_count

Purpose

Summary

Auto Updated

---

## remarks

Purpose

Optional session remarks.

Example

Holiday Adjustment

Practical Class

Special Event

---

## status

Allowed Values

open

completed

locked

cancelled

Default

open

Meaning

open

Teacher may edit attendance.

completed

Attendance finished.

locked

No further editing.

cancelled

Invalid session.

Future Ready

Approval Workflow

Attendance Lock

---

## created_by

Foreign Key

users.id

Nullable

YES

---

## updated_by

Foreign Key

users.id

Nullable

YES

---

# Composite Unique Constraint

The following combination must be unique.

academic_year_id

semester_id

department_id

shift_id

group_id

section_id

subject_id

attendance_date

Reason

Prevent duplicate attendance sessions.

Database must enforce this.

Never rely only on Laravel validation.

---

# Indexes

Single Indexes

academic_year_id

semester_id

department_id

shift_id

group_id

section_id

subject_id

teacher_id

attendance_date

status

created_by

updated_by

---

# Composite Index

Recommended

(attendance_date, section_id)

Reason

Monthly Reports

Daily Attendance

Fast Searching

Recommended

(subject_id, attendance_date)

Reason

Subject Wise Reports

Recommended

(teacher_id, attendance_date)

Reason

Teacher History

---

# Foreign Keys

| Column | References | Delete | Update |
|----------|------------|---------|---------|
| academic_year_id | academic_years.id | RESTRICT | CASCADE |
| semester_id | semesters.id | RESTRICT | CASCADE |
| department_id | departments.id | RESTRICT | CASCADE |
| shift_id | shifts.id | RESTRICT | CASCADE |
| group_id | groups.id | SET NULL | CASCADE |
| section_id | sections.id | RESTRICT | CASCADE |
| subject_id | subjects.id | RESTRICT | CASCADE |
| teacher_id | users.id | RESTRICT | CASCADE |
| created_by | users.id | SET NULL | CASCADE |
| updated_by | users.id | SET NULL | CASCADE |

---

# Delete Policy

Attendance Session

↓

Attendance Records

Cascade Delete

Academic Master Data

↓

Attendance Session

Restrict Delete

Teacher

↓

Attendance Session

Restrict Delete

Reason

Attendance history must never become orphaned.

---

# Business Validation Rules

Attendance Date

Required

Must not be null.

Subject

Required.

Section

Required.

Teacher

Required.

Academic Year

Required.

Duplicate Sessions

Not Allowed.

---

# Performance Considerations

Expected Records

100,000+

Indexes are mandatory.

Composite indexes are mandatory.

Attendance Summary fields avoid COUNT() queries.

Only minimal columns should be selected during listing.

---

# Repository Expectations

Repository must support

- Find session by filters
- Create session
- Update summary counts
- Lock session
- Check duplicate session
- Load session with eager-loaded relationships

Never perform business logic in the repository.

---

# Service Layer Expectations

Service will

- Create attendance session
- Validate duplicate session
- Update summary counts
- Lock attendance
- Complete attendance
- Handle transactions

---

# Seeder Expectations

Factory

AttendanceSessionFactory

Seeder

AttendanceSessionSeeder

Generate

Random sessions

Random summary counts

Valid foreign keys

No duplicate composite keys.

---

# Acceptance Checklist

✓ Proper datatypes

✓ Foreign keys defined

✓ Composite unique constraint

✓ Required indexes

✓ Cascade rules

✓ Summary fields

✓ Audit fields

✓ Repository compatible

✓ Service compatible

✓ Future analytics ready

---

# Part 2B-1 — attendance_sessions Migration Blueprint

---

# Purpose

This section defines the implementation standard for the
`attendance_sessions` table migration.

Every migration must strictly follow

- DATABASE_STANDARDS.md
- AGENTS.md
- Laravel 12 Best Practices
- MySQL 8 Best Practices

Migration files must be deterministic, repeatable and production-safe.

---

# Migration Naming Convention

```
create_attendance_sessions_table
```

Example

```
2026_01_10_000001_create_attendance_sessions_table.php
```

Migration filenames must be timestamp ordered.

---

# Storage Engine

Use

```
InnoDB
```

Reason

- Foreign Key Support
- Transaction Support
- Row Level Locking
- Better Concurrency

---

# Character Set

```
utf8mb4
```

---

# Collation

```
utf8mb4_unicode_ci
```

---

# Primary Key

Definition

```
id
```

Datatype

```
BIGINT UNSIGNED
```

Laravel

```php
$table->id();
```

Rules

- Auto Increment
- Clustered Primary Key
- Never use UUID for this table unless project-wide standard changes.

---

# Audit Columns

Required

```php
$table->foreignId('created_by')
      ->nullable();

$table->foreignId('updated_by')
      ->nullable();

$table->timestamps();
```

Purpose

Track who created and modified the attendance session.

---

# Foreign Key Definitions

Every relationship must use explicit foreign keys.

Avoid nullable foreign keys unless the business rule requires it.

---

## academic_year_id

Laravel

```php
$table->foreignId('academic_year_id')
      ->constrained('academic_years')
      ->restrictOnDelete()
      ->cascadeOnUpdate();
```

---

## semester_id

```php
$table->foreignId('semester_id')
      ->constrained('semesters')
      ->restrictOnDelete()
      ->cascadeOnUpdate();
```

---

## department_id

```php
$table->foreignId('department_id')
      ->constrained('departments')
      ->restrictOnDelete()
      ->cascadeOnUpdate();
```

---

## shift_id

```php
$table->foreignId('shift_id')
      ->constrained('shifts')
      ->restrictOnDelete()
      ->cascadeOnUpdate();
```

---

## group_id

Nullable

Reason

Some institutions may not use academic groups.

Laravel

```php
$table->foreignId('group_id')
      ->nullable()
      ->constrained('groups')
      ->nullOnDelete()
      ->cascadeOnUpdate();
```

---

## section_id

```php
$table->foreignId('section_id')
      ->constrained('sections')
      ->restrictOnDelete()
      ->cascadeOnUpdate();
```

---

## subject_id

```php
$table->foreignId('subject_id')
      ->constrained('subjects')
      ->restrictOnDelete()
      ->cascadeOnUpdate();
```

---

## teacher_id

Teacher references authenticated users.

```php
$table->foreignId('teacher_id')
      ->constrained('users')
      ->restrictOnDelete()
      ->cascadeOnUpdate();
```

---

## created_by

```php
$table->foreignId('created_by')
      ->nullable()
      ->constrained('users')
      ->nullOnDelete()
      ->cascadeOnUpdate();
```

---

## updated_by

```php
$table->foreignId('updated_by')
      ->nullable()
      ->constrained('users')
      ->nullOnDelete()
      ->cascadeOnUpdate();
```

---

# Composite Unique Index

The database must guarantee that only one attendance session
exists for the same class, subject and date.

Fields

- academic_year_id
- semester_id
- department_id
- shift_id
- group_id
- section_id
- subject_id
- attendance_date

Laravel

```php
$table->unique([
    'academic_year_id',
    'semester_id',
    'department_id',
    'shift_id',
    'group_id',
    'section_id',
    'subject_id',
    'attendance_date'
], 'attendance_session_unique');
```

Reason

Prevent duplicate attendance sessions.

Never rely only on validation.

Database must enforce uniqueness.

---

# Secondary Index Strategy

Indexes are required to support high-performance queries.

---

## Attendance Date

```php
$table->index('attendance_date');
```

Purpose

- Daily attendance
- Calendar
- Reports

---

## Teacher

```php
$table->index('teacher_id');
```

Purpose

Teacher history

Teacher dashboard

Teacher workload

---

## Subject

```php
$table->index('subject_id');
```

Purpose

Subject reports

Subject analytics

---

## Section

```php
$table->index('section_id');
```

Purpose

Student attendance

Class attendance

---

## Department

```php
$table->index('department_id');
```

Purpose

Department reports

---

## Semester

```php
$table->index('semester_id');
```

Purpose

Semester reporting

---

## Academic Year

```php
$table->index('academic_year_id');
```

Purpose

Historical reporting

---

## Status

```php
$table->index('status');
```

Purpose

Filter

Open

Completed

Locked

Cancelled

---

# Composite Performance Indexes

Monthly Attendance

```php
$table->index([
    'attendance_date',
    'section_id'
], 'attendance_monthly_idx');
```

Purpose

Monthly attendance reports

---

Teacher Report

```php
$table->index([
    'teacher_id',
    'attendance_date'
], 'attendance_teacher_idx');
```

Purpose

Teacher performance

Attendance history

---

Subject Report

```php
$table->index([
    'subject_id',
    'attendance_date'
], 'attendance_subject_idx');
```

Purpose

Subject analytics

---

Department Report

```php
$table->index([
    'department_id',
    'attendance_date'
], 'attendance_department_idx');
```

Purpose

Department-wise reporting

---

# Migration Rules

Every migration must

✓ use foreignId()

✓ define explicit FK actions

✓ define indexes

✓ define composite unique keys

✓ use timestamps()

✓ include created_by

✓ include updated_by

✓ avoid nullable fields unless required

✓ use InnoDB

✓ use utf8mb4

✓ be fully reversible

---

# Definition of Done

The migration blueprint is considered complete only if

- Primary key exists.
- All foreign keys are enforced.
- Composite unique index prevents duplicate sessions.
- Performance indexes are created.
- Audit fields exist.
- Delete rules follow business requirements.
- Migration complies with DATABASE_STANDARDS.md.

---

# Part 2B-2A-1

# Query Optimization Strategy

Version: 1.0

Applies To

- Attendance Session
- Attendance Record
- Repository Layer
- Service Layer
- Reports
- Dashboard
- AJAX API

---

# Objective

The Attendance module is expected to handle

50

↓

500

students per class

while maintaining

- Fast Page Load
- Fast AJAX Save
- Minimal Database Load
- Low Memory Usage
- Low Network Payload

The database must be optimized from the beginning.

Never optimize only after the database becomes slow.

---

# Performance Targets

Attendance Page

Load Time

< 1 Second

Load Students API

< 300 ms

Single Attendance Save

< 100 ms

Bulk Attendance

< 3 Seconds

Attendance Summary

< 100 ms

Monthly Report

< 2 Seconds

---

# Optimization Principles

Always

✓ Select only required columns

✓ Use indexes

✓ Use eager loading

✓ Use pagination

✓ Cache master data

✓ Use transactions correctly

✓ Keep payload minimal

Never

✗ SELECT *

✗ Full Table Scan

✗ N+1 Query

✗ Duplicate queries

✗ Large JSON responses

✗ Unnecessary JOINs

---

# Repository Query Rules

Repositories are the only layer allowed to execute database queries.

Repository methods must

Return DTOs or Collections

Avoid duplicated queries

Avoid loading unused relationships

Never execute business logic

---

# Attendance Session Loading

When loading an attendance session

Always eager load

Academic Year

Semester

Department

Section

Subject

Teacher

Example

```
AttendanceSession

↓

Academic Year

↓

Department

↓

Section

↓

Subject

↓

Teacher
```

Never lazy load relationships inside loops.

---

# Student Loading Strategy

Student list must be loaded

Only

after

teacher clicks

Load Students

Never auto-load students when the page opens.

Reason

Avoid unnecessary database load.

---

# Attendance Record Loading

Load attendance records

using

Session ID

plus

Student IDs

Never execute

one query

per student.

Wrong

```
Student Loop

↓

Attendance Query

↓

Student Loop

↓

Attendance Query
```

Correct

```
Attendance Session

↓

Single Attendance Query

↓

Map Result

↓

Display
```

---

# Summary Calculation

Never calculate

Present Count

Absent Count

Late Count

Leave Count

using

COUNT()

every page refresh.

Instead

Store summary values

inside

attendance_sessions

Fields

present_count

absent_count

late_count

leave_count

Update automatically

after every attendance save.

Reason

Constant time lookup.

---

# AJAX Update Strategy

Each request

must update

one student only.

Payload

```
attendance_session_id

student_id

attendance_status

remark
```

Never send

the entire attendance table.

---

# Bulk Attendance Strategy

Bulk Present

Bulk Absent

Bulk Clear

should internally

execute

multiple

single-row

updates.

Never send

500 students

inside

one massive payload.

---

# Search Optimization

Searching

must support

Roll Number

Student Name

Student ID

Search should use indexed fields where possible.

---

# Filtering Strategy

Attendance page

filters

Academic Year

Semester

Department

Shift

Group

Section

Subject

Date

must execute

one optimized query.

Avoid multiple dependent queries.

---

# Eager Loading Rules

Always eager load

Student Photo

Guardian (only if required)

Section

Department

Program

Subject

Teacher

Avoid

Lazy Loading

inside Blade Views.

---

# Pagination Rules

Attendance History

Monthly Reports

Teacher Reports

Student Reports

must always use pagination.

Never return

thousands

of rows.

---

# Index Usage

Every WHERE clause

should match

an existing index.

Examples

attendance_date

teacher_id

section_id

subject_id

department_id

Composite indexes

must support

reports.

---

# Query Count Target

Attendance Page

Maximum Queries

Recommended

< 10

Excellent

< 5

Never

100+

queries.

---

# Database Transactions

Use transactions only for

Attendance Session Creation

Bulk Attendance

Attendance Import

Do not wrap simple read queries

inside transactions.

---

# Caching Strategy

Cache

Academic Years

Departments

Sections

Subjects

Teachers

Shifts

Groups

These change infrequently.

Attendance records

must never be cached

during active attendance.

---

# EXPLAIN Strategy

Every complex query

must be reviewed using

MySQL

EXPLAIN

before deployment.

Purpose

Verify

- Index Usage
- Join Strategy
- Scan Type
- Estimated Rows

Never deploy a report query

without reviewing its execution plan.

---

# EXPLAIN Checklist

Target

```
type

ref

range

const

eq_ref
```

Avoid

```
ALL
```

Reason

ALL

means

Full Table Scan.

---

# Key Column

EXPLAIN

should display

```
key

attendance_session_unique

OR

attendance_monthly_idx

OR

attendance_teacher_idx
```

If

key

is

NULL

the query is not using an index.

---

# Rows Examined

Goal

Minimal rows.

Good

50

Better

10

Excellent

1

Avoid

100000+

rows examined.

---

# Extra Column

Preferred

Using Index

Using Where

Avoid

Using Temporary

Using Filesort

Reason

Temporary tables

and

filesorts

slow down reports.

---

# JOIN Strategy

JOIN only required tables.

Preferred order

Attendance Session

↓

Attendance Record

↓

Student

↓

Section

↓

Department

Avoid joining unrelated master tables.

---

# Production Review

Before every release

review

✓ Slow Query Log

✓ EXPLAIN Output

✓ Query Count

✓ Query Time

✓ Duplicate Queries

✓ N+1 Queries

✓ Missing Indexes

---

# Definition of Done

The query optimization strategy is complete only if

✓ No N+1 queries exist.

✓ Every report uses indexes.

✓ EXPLAIN confirms index usage.

✓ Attendance save updates one row only.

✓ AJAX payload is minimal.

✓ Student loading uses eager loading.

✓ Summary values avoid COUNT() on every request.

✓ Repository queries are reusable and optimized.

✓ Attendance module can support 500 students per class with acceptable performance.

---

# Part 2B-2A-2

# Covering Index Recommendations

## Objective

Design indexes to minimize table scans and improve query performance for attendance operations.

---

## Recommended Single Indexes

| Column | Purpose |
|----------|----------|
| attendance_date | Daily attendance search |
| teacher_id | Teacher-wise attendance |
| section_id | Section-wise attendance |
| subject_id | Subject-wise attendance |
| department_id | Department reports |
| semester_id | Semester reports |
| academic_year_id | Academic year reports |
| status | Open/Completed/Locked sessions |

---

## Recommended Composite Indexes

### Monthly Attendance

```text
(attendance_date, section_id)
```

Purpose

- Monthly attendance report
- Daily attendance lookup

---

### Teacher Attendance

```text
(teacher_id, attendance_date)
```

Purpose

- Teacher attendance history
- Teacher dashboard

---

### Subject Attendance

```text
(subject_id, attendance_date)
```

Purpose

- Subject-wise reports
- Subject analytics

---

### Department Attendance

```text
(department_id, attendance_date)
```

Purpose

- Department reports
- Department analytics

---

## Unique Composite Index

The following fields must always remain unique.

```text
academic_year_id
semester_id
department_id
shift_id
group_id
section_id
subject_id
attendance_date
```

Purpose

Prevent duplicate attendance sessions.

---

## Index Guidelines

Always

- Create indexes on searchable columns.
- Create composite indexes for frequently used filters.
- Keep indexes aligned with report queries.
- Review indexes whenever new reports are added.

Avoid

- Duplicate indexes
- Unused indexes
- Indexing very large text columns

---

# MySQL Performance Best Practices

## Storage Engine

Use

```
InnoDB
```

Reason

- Transaction support
- Foreign key support
- Row-level locking
- Better concurrency

---

## Character Set

```
utf8mb4
```

---

## Collation

```
utf8mb4_unicode_ci
```

---

## Query Best Practices

Always

- Select only required columns.
- Use eager loading where appropriate.
- Use indexed columns in WHERE clauses.
- Use pagination for large datasets.

Avoid

- `SELECT *`
- Full table scans
- N+1 queries
- Unnecessary JOINs

---

## Transactions

Use transactions only for

- Attendance session creation
- Bulk attendance updates
- Attendance import

Avoid wrapping simple read operations in transactions.

---

## Pagination

Always paginate

- Attendance history
- Reports
- Session lists

Recommended page sizes

- 25
- 50
- 100

---

## Data Integrity

Enforce integrity using

- Foreign keys
- Unique constraints
- Composite unique indexes

Do not rely only on application-level validation.

---

## Performance Checklist

Before deployment, verify that:

- Required indexes are created.
- Composite unique index exists.
- No duplicate attendance session can be created.
- Queries use indexed columns.
- Attendance updates only one record per request.
- AJAX payload is minimal.
- No unnecessary database queries are executed.

---

# Acceptance Criteria

The database design is considered optimized when:

- Attendance session lookup is fast.
- Attendance save updates only one row.
- Duplicate sessions are prevented.
- Duplicate attendance records are prevented.
- Reports use indexed fields.
- Database structure supports future reporting and analytics.