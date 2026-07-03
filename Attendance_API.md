# Attendance_API.md

Version: 1.0

Status: Approved

Module: Student Attendance

---

# 1. Objective

This document defines the REST API specification for the Student Attendance module.

All endpoints must return JSON responses and follow the global `API_STANDARDS.md`.

---

# 2. Authentication

All endpoints require authentication.

Authentication

- Laravel Breeze Authentication
- Session Authentication (Web)
- CSRF Protection
- Role & Permission Middleware

Allowed Roles

- Admin
- Principal
- Department Head
- Teacher

Students and Parents cannot access these APIs.

---

# 3. Base URL

```
/attendance
```

---

# 4. API Endpoints

## Load Students

```
POST /attendance/load-students
```

Purpose

Load students based on selected filters and create/find an attendance session.

Request

```json
{
  "academic_year_id": 1,
  "semester_id": 1,
  "department_id": 1,
  "shift_id": 1,
  "group_id": 1,
  "section_id": 1,
  "subject_id": 1,
  "attendance_date": "2026-07-03"
}
```

Success Response

```json
{
  "success": true,
  "attendance_session_id": 15,
  "students": []
}
```

---

## Update Attendance

```
POST /attendance/update
```

Purpose

Update attendance for a single student using AJAX.

Request

```json
{
  "attendance_session_id": 15,
  "student_id": 1001,
  "attendance_status": "P",
  "remark": ""
}
```

Success Response

```json
{
  "success": true,
  "message": "Attendance updated successfully."
}
```

---

## Bulk Attendance

```
POST /attendance/bulk-update
```

Purpose

Mark multiple students as Present, Absent, Late or Leave.

Request

```json
{
  "attendance_session_id": 15,
  "student_ids": [1001,1002,1003],
  "attendance_status": "P"
}
```

Success Response

```json
{
  "success": true,
  "message": "Bulk attendance updated successfully."
}
```

---

## Get Attendance Session

```
GET /attendance/session/{id}
```

Purpose

Return attendance session details with summary.

---

## Monthly Attendance

```
GET /attendance/monthly
```

Purpose

Generate monthly attendance report.

Query Parameters

- section_id
- subject_id
- month
- year

---

# 5. Attendance Status

Supported Values

| Code | Meaning |
|------|----------|
| P | Present |
| A | Absent |
| L | Late |
| LV | Leave |

Future

- H (Holiday)
- OD (Official Duty)

---

# 6. Validation Rules

Required

- academic_year_id
- semester_id
- department_id
- shift_id
- section_id
- subject_id
- attendance_date

Attendance Update

- attendance_session_id
- student_id
- attendance_status

---

# 7. Business Rules

- One attendance session per class, subject and date.
- One attendance record per student in a session.
- Duplicate attendance is not allowed.
- Attendance updates must be transaction-safe.
- AJAX only (no full page reload).

---

# 8. Response Format

Success

```json
{
  "success": true,
  "message": "Success",
  "data": {}
}
```

Validation Error

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {}
}
```

Server Error

```json
{
  "success": false,
  "message": "Something went wrong."
}
```

---

# 9. HTTP Status Codes

| Code | Meaning |
|------|----------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Internal Server Error |

---

# 10. Performance Rules

- Use eager loading.
- Avoid N+1 queries.
- Update only one attendance row per AJAX request.
- Keep JSON payload minimal.
- Do not reload the page after update.

---

# 11. Security

- Authentication required.
- Authorization via Roles & Permissions.
- CSRF protection enabled.
- Validate all request data.
- Prevent duplicate attendance sessions.
- Prevent duplicate attendance records.

---

# 12. Testing Checklist

- Load students successfully.
- Create attendance session.
- Prevent duplicate session.
- Update attendance.
- Bulk attendance update.
- Validation errors.
- Unauthorized access.
- Forbidden role access.
- JSON response format.
- Performance under large class size.

---

# 13. Definition of Done

The Attendance API is considered complete when:

- All endpoints return valid JSON.
- Validation is enforced.
- Duplicate sessions are prevented.
- Duplicate records are prevented.
- All APIs follow API_STANDARDS.md.
- Feature tests pass successfully.