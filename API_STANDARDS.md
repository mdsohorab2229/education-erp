# API_STANDARDS.md

Version: 1.0

Status: Approved

Project: Education ERP

Applies To:

- Web API
- AJAX API
- Mobile API
- Future Public API

This document defines the standard API structure used across the entire ERP.

---

# 1. General Principles

All APIs must be:

- RESTful
- Stateless
- Predictable
- Versioned
- Consistent
- Secure
- JSON based

Every API must follow the same response structure.

Never return inconsistent JSON.

---

# 2. API Versioning

Every API must be versioned.

Example

/api/v1/...

Future

/api/v2/...

Never remove old versions immediately.

Deprecate before removal.

---

# 3. Content Type

Request

Content-Type

application/json

Response

application/json

---

# 4. Naming Convention

Endpoints

Use lowercase.

Use hyphen.

Correct

/api/v1/attendance/load-students

/api/v1/student-promotions

Incorrect

/api/v1/loadStudents

/api/v1/StudentList

---

# 5. HTTP Methods

GET

Retrieve resources

POST

Create resource

PUT

Replace resource

PATCH

Partial update

DELETE

Delete resource

Never use GET to modify data.

---

# 6. Success Response Structure

Every successful response must follow this structure.

```json
{
    "success": true,
    "message": "Operation completed successfully.",
    "data": {},
    "meta": {},
    "errors": null
}
```

---

# 7. Collection Response

```json
{
    "success": true,
    "message": "Students loaded successfully.",
    "data": [],
    "meta": {
        "total": 100
    },
    "errors": null
}
```

---

# 8. Error Response Structure

All errors must follow one format.

```json
{
    "success": false,
    "message": "Request failed.",
    "data": null,
    "errors": {}
}
```

Never return HTML error pages for AJAX requests.

---

# 9. Validation Error Format

HTTP

422

```json
{
    "success": false,
    "message": "Validation failed.",
    "data": null,
    "errors": {
        "name": [
            "The name field is required."
        ],
        "department_id": [
            "Department is required."
        ]
    }
}
```

---

# 10. Unauthorized Response

HTTP

401

```json
{
    "success": false,
    "message": "Unauthenticated.",
    "data": null,
    "errors": null
}
```

---

# 11. Forbidden Response

HTTP

403

```json
{
    "success": false,
    "message": "You do not have permission to perform this action.",
    "data": null,
    "errors": null
}
```

---

# 12. Not Found

HTTP

404

```json
{
    "success": false,
    "message": "Resource not found.",
    "data": null,
    "errors": null
}
```

---

# 13. Server Error

HTTP

500

```json
{
    "success": false,
    "message": "Internal server error.",
    "data": null,
    "errors": null
}
```

Never expose exception messages in production.

---

# 14. Pagination Response

All listing APIs must use pagination.

Example

```json
{
    "success": true,
    "message": "Students retrieved successfully.",
    "data": [],
    "meta": {
        "current_page": 1,
        "last_page": 20,
        "per_page": 25,
        "total": 500,
        "from": 1,
        "to": 25
    },
    "errors": null
}
```

Never return thousands of records in one response.

---

# 15. Empty Response

```json
{
    "success": true,
    "message": "No records found.",
    "data": [],
    "meta": {},
    "errors": null
}
```

---

# 16. Date Format

Date

YYYY-MM-DD

Example

2026-07-03

DateTime

ISO 8601

Example

2026-07-03T08:30:15Z

Never return localized date strings.

---

# 17. Boolean Format

Use

true

false

Never use

1

0

Yes

No

---

# 18. Status Codes

200

OK

201

Created

204

No Content

400

Bad Request

401

Unauthorized

403

Forbidden

404

Not Found

409

Conflict

422

Validation Error

429

Too Many Requests

500

Server Error

---

# 19. Authentication

Protected APIs require authentication.

Preferred

Laravel Sanctum

Future

JWT

Unauthenticated users must receive HTTP 401.

---

# 20. Authorization

Every protected endpoint must verify permission.

Example

attendance.view

attendance.create

attendance.update

student.view

student.create

Never rely only on frontend restrictions.

---

# 21. API Resources

All JSON responses must use Laravel API Resources.

Never return raw Eloquent models.

Example

StudentResource

AttendanceResource

DepartmentResource

---

# 22. Filtering

Support query parameters.

Example

/students

?page=1

&per_page=25

&department_id=2

&status=active

Ignore unknown filters.

---

# 23. Sorting

Support

sort_by

sort_direction

Example

sort_by=name

sort_direction=asc

Allowed

asc

desc

---

# 24. Searching

Support

search

Example

search=Rahim

Search must be case insensitive.

---

# 25. File Upload Response

```json
{
    "success": true,
    "message": "File uploaded successfully.",
    "data": {
        "path": "...",
        "name": "...",
        "size": 102400
    },
    "errors": null
}
```

---

# 26. AJAX Standards

Every AJAX endpoint must

Return JSON

Never redirect

Never return HTML

Return meaningful HTTP status

Keep payload minimal

---

# 27. Attendance API Rules

Attendance update

One request

↓

One student

Payload

```json
{
    "attendance_session_id": 12,
    "student_id": 5,
    "attendance_status": "P",
    "remark": ""
}
```

Response

```json
{
    "success": true,
    "message": "Attendance updated successfully.",
    "data": {
        "status": "P",
        "checked_at": "2026-07-03T08:45:20Z"
    },
    "errors": null
}
```

Never update the whole attendance table in one request unless using the dedicated bulk endpoint.

---

# 28. Performance Rules

Never return unnecessary columns.

Always eager load relationships.

Paginate large datasets.

Compress payload.

Avoid nested resources unless required.

---

# 29. Security Rules

Validate every request.

Authorize every action.

Use CSRF for web requests.

Escape user input.

Never expose stack traces.

Never expose SQL errors.

---

# 30. Logging

Log

Authentication failures

Authorization failures

Unexpected server errors

Critical business operations

Never log passwords or sensitive tokens.

---

# 31. Backward Compatibility

Do not change existing response structures in a released API version.

Breaking changes require a new API version.

---

# 32. Acceptance Checklist

Before approving any API:

- Uses RESTful principles
- Versioned endpoint
- Returns JSON only
- Uses API Resources
- Consistent response structure
- Proper HTTP status codes
- Validation implemented
- Authorization enforced
- Pagination supported
- Filtering supported
- Search supported
- Feature tests pass
- No sensitive data exposed

Only after all checks pass should the API be considered production-ready.