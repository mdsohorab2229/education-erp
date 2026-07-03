# DATABASE_STANDARDS.md

Version: 1.0

Status: Approved

Project: Education ERP

Applies To:

- All Database Migrations
- All Eloquent Models
- All Repositories
- All Future Modules

This document is the single source of truth for all database design decisions.

---

# 1. Database Engine

Database

MySQL 8+

Storage Engine

InnoDB

Character Set

utf8mb4

Collation

utf8mb4_unicode_ci

Timezone

UTC (store)

Display according to application timezone.

---

# 2. Naming Convention

## Tables

Plural

Examples

students

departments

attendance_sessions

attendance_records

student_documents

Never use singular table names.

---

## Primary Key

Every table

```php
$id();
```

Laravel default BIGINT UNSIGNED.

---

## Foreign Keys

Always

```
xxx_id
```

Examples

student_id

department_id

section_id

teacher_id

attendance_session_id

Never use

dept

stud

sec

or abbreviations.

---

## Pivot Tables

Alphabetical order.

Example

permission_role

role_user

student_subject

teacher_subject

No additional business logic in pivot tables unless necessary.

---

# 3. Standard Columns

Every table must contain

```php
$table->id();

$table->timestamps();
```

---

Audit columns (when applicable)

```php
$table->foreignId('created_by')
      ->nullable()
      ->constrained('users')
      ->nullOnDelete();

$table->foreignId('updated_by')
      ->nullable()
      ->constrained('users')
      ->nullOnDelete();
```

---

Soft Delete

When records should not be permanently removed

```php
$table->softDeletes();
```

Examples

Students

Teachers

Departments

Subjects

Attendance Sessions

NOT required for

Pivot tables

Temporary tables

Logs (unless specified)

---

# 4. Foreign Key Rules

Always use

```php
foreignId()
```

Never use

unsignedBigInteger()

unless absolutely necessary.

Example

```php
$table->foreignId('student_id')
      ->constrained()
      ->cascadeOnUpdate()
      ->restrictOnDelete();
```

Delete Rules

Master Data

Restrict Delete

Transaction Data

Restrict Delete

Child Records

Cascade only where business rules allow.

Never orphan records.

---

# 5. Index Rules

Always index

Foreign Keys

Frequently searched columns

Date columns used in filters

Status columns

Composite search columns

Examples

```php
$table->index('status');

$table->index('department_id');

$table->index('section_id');

$table->index('attendance_date');
```

---

Composite Index

Example

```php
$table->unique([
    'attendance_date',
    'academic_year_id',
    'semester_id',
    'department_id',
    'shift_id',
    'group_id',
    'section_id',
    'subject_id'
]);
```

Use composite indexes whenever uniqueness depends on multiple fields.

---

# 6. Unique Constraints

Examples

Academic Year

name

code

Department

code

Student

admission_no

roll_no (within applicable scope)

Attendance

attendance_session_id

student_id

Never rely only on application validation.

Database constraints are mandatory.

---

# 7. Status Fields

Use ENUM only when values are stable.

Example

attendance_status

```
P
A
L
LV
```

For flexible statuses, use VARCHAR with validation in the application.

---

# 8. Boolean Fields

Use

```php
$table->boolean('is_active')->default(true);
```

Examples

is_current

is_active

is_locked

Avoid tinyInteger for boolean semantics.

---

# 9. Date & Time Fields

Use

```php
date()
```

for calendar dates.

Use

```php
dateTime()
```

for timestamps.

Examples

attendance_date

exam_date

checked_at

---

# 10. Decimal Fields

Financial values

```php
decimal(12,2)
```

Percentages

```php
decimal(5,2)
```

Never use FLOAT or DOUBLE for monetary values.

---

# 11. File Storage

Store only metadata in the database.

Examples

file_name

original_name

mime_type

file_size

disk

path

Never store binary files in database tables.

---

# 12. JSON Columns

Use JSON only for truly dynamic data.

Avoid replacing relational design with JSON.

---

# 13. Nullable Policy

Allow NULL only when business logic permits.

Avoid unnecessary nullable columns.

Every nullable field should have a documented reason.

---

# 14. Soft Delete Policy

Use SoftDeletes for business entities.

Examples

Students

Teachers

Subjects

Departments

Programs

Sections

Do not soft delete transactional integrity records unless explicitly required.

---

# 15. Audit Policy

Track creator and updater whenever business data is involved.

Fields

created_by

updated_by

Future modules may include:

deleted_by

approved_by

verified_by

---

# 16. Relationship Rules

One-to-One

Guardian

↓

Student

One-to-Many

Department

↓

Programs

Program

↓

Sections

Attendance Session

↓

Attendance Records

Many-to-Many

Teacher

↓

Subjects

Student

↓

Subjects (if elective)

Always define inverse relationships.

---

# 17. Migration Rules

Every migration must

- Define foreign keys
- Define indexes
- Define unique constraints
- Use proper column types
- Be reversible
- Implement down() correctly

Never disable foreign key checks unless absolutely necessary.

---

# 18. Performance Standards

Avoid N+1 queries.

Always use eager loading where appropriate.

Index every frequently filtered column.

Never use SELECT * unnecessarily.

Use pagination for large datasets.

Never use Model::all() for production lists.

---

# 19. Concurrency Rules

Critical write operations must use transactions.

Examples

Student Admission

Attendance Save

Fee Collection

Exam Result Publish

Database constraints must prevent duplicate records.

---

# 20. Seeder Standards

Every module must include

- Factory
- Seeder

Seeders should

- Respect foreign keys
- Generate realistic data
- Avoid duplicate unique values
- Be safe to run multiple times where practical

---

# 21. Future Scalability

Database design must support future implementation of

- Multi Campus
- Multi Academic Session
- Parent Portal
- Mobile App
- REST API
- Attendance Analytics
- Report Builder
- Audit Logs
- Background Jobs

Without major schema redesign.

---

# 22. Prohibited Practices

❌ No missing foreign keys

❌ No orphan records

❌ No Model::all() in production logic

❌ No storing files in database

❌ No duplicate indexes

❌ No disabling constraints without reason

❌ No business logic in migrations

❌ No inconsistent naming

❌ No nullable foreign keys unless justified

---

# 23. Acceptance Checklist

Before approving any migration, verify:

- Table names follow convention
- Primary key exists
- Foreign keys exist
- Indexes added
- Composite indexes added where needed
- Unique constraints defined
- SoftDeletes added where required
- Audit fields added where required
- Migration rollback works
- Seeder executes successfully
- Feature tests pass

Only after all checks pass should the database schema be considered production-ready.