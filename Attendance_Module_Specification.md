# Attendance_Module_Specification.md

Version: 1.0

Status: Approved

Module: Student Attendance

Project: Education ERP

---

# 1. Module Objective

Develop a modern, scalable, production-ready Student Attendance module for the Education ERP.

The module must allow teachers to take attendance quickly using AJAX auto-save without requiring page reloads.

The module must be independent, reusable, testable and scalable.

---

# 2. Scope

This module includes:

- Student Attendance
- Attendance Session Management
- Daily Attendance
- AJAX Auto Save
- Attendance Summary
- Search
- Bulk Attendance
- Attendance Print View

This module DOES NOT include:

- Teacher Attendance
- Employee Attendance
- Parent Notification
- SMS
- Email
- Attendance Approval
- Attendance Lock
- Reports
- Analytics
- PDF Export
- Excel Export

Those belong to future phases.

---

# 3. User Roles

Allowed

- Teacher
- Department Head
- Principal
- Admin

Not Allowed

- Student
- Parent

Authorization must use Policies/Permissions.

---

# 4. Attendance Workflow

Teacher Login

↓

Open Attendance Module

↓

Select

- Academic Year
- Semester
- Department
- Shift
- Group
- Section
- Subject
- Attendance Date

↓

Click

Load Students

↓

System loads enrolled students

↓

Teacher marks attendance

↓

Every click automatically saves using AJAX

↓

Summary updates immediately

↓

Attendance completed

No manual Save button is required for individual records.

---

# 5. Attendance Status

Supported

- Present (P)
- Absent (A)
- Late (L)
- Leave (LV)

Reserved for future

- Holiday (H)
- Official Duty (OD)

Every student must have exactly one status per attendance date.

---

# 6. Functional Requirements

The module shall:

- Create attendance session automatically when first attendance is saved
- Prevent duplicate attendance sessions
- Prevent duplicate attendance records
- Support auto-save
- Support updating existing attendance
- Support remarks
- Support attendance summary
- Support searching
- Support bulk actions
- Support responsive interface
- Support print-friendly layout

---

# 7. Attendance Session

Attendance Session uniquely represents one class attendance.

A session is identified by:

- Attendance Date
- Academic Year
- Semester
- Department
- Shift
- Group
- Section
- Subject

Only one attendance session may exist for the same combination.

---

# 8. Attendance Record

Each attendance record belongs to:

- One Attendance Session
- One Student

Each student can have only one attendance record in a session.

---

# 9. Database Requirements

## attendance_sessions

Columns

- id
- attendance_date
- academic_year_id
- semester_id
- department_id
- shift_id
- group_id
- section_id
- subject_id
- teacher_id
- total_students
- present_count
- absent_count
- late_count
- leave_count
- remarks
- status
- created_by
- updated_by
- timestamps

Constraints

Unique Composite Index

attendance_date

academic_year_id

semester_id

department_id

shift_id

group_id

section_id

subject_id

Foreign Keys

Academic Year

Semester

Department

Shift

Group

Section

Subject

Teacher

---

## attendance_records

Columns

- id
- attendance_session_id
- student_id
- attendance_status
- remark
- checked_at
- timestamps

Constraints

Unique Composite Index

attendance_session_id

student_id

Foreign Keys

attendance_session_id

student_id

---

# 10. Relationships

AttendanceSession

hasMany AttendanceRecords

AttendanceRecord

belongsTo AttendanceSession

AttendanceRecord

belongsTo Student

Student

hasMany AttendanceRecords

---

# 11. User Interface

## Header

Display

- Module Title
- Current Date
- Academic Session

---

## Filter Panel

Fields

- Academic Year
- Semester
- Department
- Shift
- Group
- Section
- Subject
- Attendance Date

Attendance Date must use Flatpickr.

Manual typing is not allowed.

---

## Student Loading

Students must NOT load automatically.

Workflow

Select Filters

↓

Load Students

↓

AJAX Request

↓

Student List

---

## Attendance Table

Columns

- Roll Number
- Student Photo
- Student Name
- Attendance Status
- Remark
- Save Status

Attendance Status must use radio buttons.

Only one option may be selected.

---

## Save Indicator

Each row displays

Saving...

↓

Saved

↓

Error

without refreshing the page.

---

## Live Summary

Always visible.

Display

- Present
- Absent
- Late
- Leave

Update instantly after every attendance change.

---

## Search

Support searching by

- Student Name
- Roll Number
- Student ID

Search must filter instantly without reloading.

---

## Bulk Actions

Support

- Mark All Present
- Mark All Absent
- Clear Attendance

Bulk operations must use AJAX.

---

## Responsive Design

Desktop

- Sticky Header
- Sticky Roll Column
- Sticky Student Column

Tablet

Responsive Table

Mobile

Student Card Layout

---

## Print

Provide a print-friendly daily/monthly attendance layout.

---

# 12. API Contract

## Load Students

POST

/attendance/load-students

Response

- Attendance Session
- Student List
- Existing Attendance

---

## Update Attendance

POST

/attendance/update

Payload

- attendance_session_id
- student_id
- attendance_status
- remark

Response

- Success
- Updated Summary
- Timestamp

---

## Load Session

GET

/attendance/session/{id}

Response

Session Details

Attendance Records

Summary

---

# 13. Performance Requirements

The module must:

- Never reload the page
- Update only one row per request
- Use minimal JSON payload
- Avoid N+1 queries
- Use eager loading
- Use pagination where appropriate
- Be optimized for classes containing 50–500 students
- Update summary without reloading the table

---

# 14. Concurrency Rules

If two users update the same attendance record:

- The latest valid update wins.
- No duplicate records may be created.
- Database constraints must guarantee uniqueness.
- Services must execute updates inside transactions where necessary.

---

# 15. Validation Rules

Required

- Academic Year
- Semester
- Department
- Shift
- Group
- Section
- Subject
- Attendance Date

Attendance Status

Must be one of

- P
- A
- L
- LV

Reject duplicate sessions.

Reject duplicate attendance records.

Reject invalid student IDs.

---

# 16. Security Requirements

Only authorized users may access attendance.

Permissions should protect:

- View Attendance
- Create Attendance
- Update Attendance

Every request must pass authorization checks.

CSRF protection is mandatory.

---

# 17. Non-Functional Requirements

The module must be:

- Production Ready
- Responsive
- Scalable
- Testable
- Maintainable
- Mobile Friendly

Architecture must follow:

Controller

↓

Service

↓

Repository Interface

↓

Repository

↓

Model

---

# 18. Future Enhancements

The architecture must support future implementation of:

- Monthly Attendance Report
- Student Attendance History
- Subject-wise Attendance
- Teacher-wise Attendance
- Attendance Analytics
- PDF Export
- Excel Export
- Parent Notification
- Attendance Approval Workflow
- Attendance Lock
- Holiday Calendar

No major database refactoring should be required.

---

# 19. Acceptance Criteria

The module is considered complete when:

- Attendance sessions are created correctly.
- Duplicate sessions are prevented.
- Duplicate attendance records are prevented.
- Attendance is auto-saved using AJAX.
- Summary updates instantly.
- Search works correctly.
- Bulk actions work correctly.
- Responsive layout works on desktop, tablet and mobile.
- Feature tests pass.
- No N+1 queries are detected.
- AGENTS.md architecture rules are fully respected.