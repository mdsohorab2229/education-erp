# Attendance_Test_Cases.md

Version: 1.0

Status: Approved

Module: Student Attendance

---

# 1. Objective

This document defines the functional test cases for the Student Attendance Module.

Every feature must pass all test cases before being considered production-ready.

---

# 2. Scope

Covered

- Attendance Session
- Attendance Record
- AJAX APIs
- Validation
- Authorization
- UI Workflow
- Performance

Not Covered

- Parent Notification
- Attendance Approval
- Holiday Calendar
- Analytics
- PDF / Excel Export

---

# 3. Authentication Tests

### TC-001

Guest user cannot access Attendance page.

Expected

- Redirect to Login

---

### TC-002

Guest user cannot call Attendance API.

Expected

- HTTP 401

---

# 4. Authorization Tests

### TC-003

Admin can access Attendance Module.

Expected

- Success

---

### TC-004

Teacher can access Attendance Module.

Expected

- Success

---

### TC-005

Department Head can access Attendance Module.

Expected

- Success

---

### TC-006

Student cannot access Attendance Module.

Expected

- HTTP 403

---

### TC-007

Parent cannot access Attendance Module.

Expected

- HTTP 403

---

# 5. Attendance Session Tests

### TC-008

Create Attendance Session

Expected

- Session created successfully

---

### TC-009

Duplicate Attendance Session

Expected

- Validation failed
- Duplicate session not created

---

### TC-010

Load Existing Attendance Session

Expected

- Existing session returned

---

# 6. Student Loading Tests

### TC-011

Load students using valid filters.

Expected

- Student list returned

---

### TC-012

Load students with invalid filters.

Expected

- Validation error

---

### TC-013

Load students when no students exist.

Expected

- Empty list

---

# 7. Attendance Update Tests

### TC-014

Mark Present.

Expected

- Record saved

---

### TC-015

Mark Absent.

Expected

- Record updated

---

### TC-016

Mark Late.

Expected

- Record updated

---

### TC-017

Mark Leave.

Expected

- Record updated

---

### TC-018

Update remark.

Expected

- Remark updated

---

### TC-019

Duplicate attendance record.

Expected

- Existing record updated
- No duplicate row created

---

# 8. Bulk Attendance Tests

### TC-020

Mark All Present.

Expected

- All records updated

---

### TC-021

Mark All Absent.

Expected

- All records updated

---

### TC-022

Mark All Late.

Expected

- All records updated

---

### TC-023

Clear Attendance.

Expected

- Attendance cleared

---

# 9. Validation Tests

### TC-024

Attendance Date missing.

Expected

- Validation error

---

### TC-025

Section missing.

Expected

- Validation error

---

### TC-026

Subject missing.

Expected

- Validation error

---

### TC-027

Invalid Attendance Status.

Expected

- Validation error

---

### TC-028

Invalid Student ID.

Expected

- Validation error

---

# 10. API Tests

### TC-029

Load Students API.

Expected

- HTTP 200

---

### TC-030

Update Attendance API.

Expected

- HTTP 200

---

### TC-031

Invalid Payload.

Expected

- HTTP 422

---

### TC-032

Unauthorized API Call.

Expected

- HTTP 401

---

### TC-033

Forbidden Role.

Expected

- HTTP 403

---

# 11. UI Tests

### TC-034

Load Students button.

Expected

- Table populated

---

### TC-035

Attendance updates without page reload.

Expected

- AJAX success

---

### TC-036

Summary updates instantly.

Expected

- Counts updated

---

### TC-037

Search by Name.

Expected

- Filter works

---

### TC-038

Search by Roll.

Expected

- Filter works

---

### TC-039

Search by Student ID.

Expected

- Filter works

---

### TC-040

Bulk Action buttons.

Expected

- Work correctly

---

### TC-041

Save Status indicator.

Expected

- Saving...
- Saved

---

# 12. Print Tests

### TC-042

Monthly Print View.

Expected

- Print layout displayed

---

# 13. Performance Tests

### TC-043

Load 100 students.

Expected

- < 1 second

---

### TC-044

Single attendance update.

Expected

- < 100 ms

---

### TC-045

Bulk attendance update.

Expected

- < 3 seconds

---

### TC-046

No N+1 query.

Expected

- Eager loading confirmed

---

# 14. Database Tests

### TC-047

Composite Unique Constraint.

Expected

- Duplicate session prevented

---

### TC-048

Attendance Record Unique Constraint.

Expected

- Duplicate record prevented

---

### TC-049

Foreign Key Constraints.

Expected

- Valid references only

---

### TC-050

Cascade Rules.

Expected

- Behave as designed

---

# 15. Regression Tests

Verify after every update

- Session creation
- Student loading
- Attendance update
- Bulk update
- Search
- Summary
- API response
- Permissions
- Validation

---

# 16. Feature Test Files

Recommended

```
tests/Feature/Attendance/

AttendanceSessionTest.php

AttendanceRecordTest.php

AttendanceApiTest.php

AttendanceAuthorizationTest.php

AttendanceValidationTest.php

AttendanceBulkActionTest.php
```

---

# 17. Acceptance Criteria

The Attendance Module is considered complete only if

✓ All Feature Tests pass

✓ All Validation Tests pass

✓ Authorization works correctly

✓ No duplicate attendance session

✓ No duplicate attendance record

✓ AJAX updates work

✓ Summary updates correctly

✓ Performance targets are achieved

✓ No N+1 queries

✓ Database constraints are enforced

✓ All tests pass successfully