# Attendance_UI.md

Version: 1.0

Status: Approved

Module: Student Attendance

---

# 1. Objective

Define the user interface specification for the Student Attendance Module.

The UI must be responsive, AJAX-based, and optimized for fast classroom attendance.

---

# 2. Technology Stack

- Bootstrap 5
- Bootstrap Icons
- Alpine.js
- Axios
- SweetAlert2
- Flatpickr
- Tom Select
- DataTables

Must follow

- UI_STANDARDS.md
- API_STANDARDS.md

---

# 3. Page Layout

```
Page Header

↓

Filter Card

↓

Summary Card

↓

Attendance Table

↓

Pagination (History Pages Only)
```

No page reload should occur after attendance updates.

---

# 4. Page Header

Display

- Page Title
- Current Date
- Academic Session
- Breadcrumb

Example

```
Dashboard

>

Attendance

>

Take Attendance
```

---

# 5. Filter Card

Fields

- Academic Year
- Semester
- Department
- Shift
- Group
- Section
- Subject
- Attendance Date

Attendance Date

- Flatpickr
- Manual typing disabled

Buttons

- Load Students
- Reset

Students must not load automatically.

---

# 6. Summary Card

Always visible.

Display

- Total Students
- Present
- Absent
- Late
- Leave

Values update instantly after every attendance change.

---

# 7. Search

Support search by

- Student Name
- Roll Number
- Student ID

Search should update instantly without page reload.

---

# 8. Attendance Table

Columns

- Roll Number
- Photo
- Student ID
- Student Name
- Present
- Absent
- Late
- Leave
- Remark
- Save Status

Attendance status must use radio buttons.

Only one status can be selected.

---

# 9. Save Status

Each row should display

- Saving...
- Saved
- Error

Status updates automatically after every AJAX request.

---

# 10. Attendance Actions

Single Student

- Present
- Absent
- Late
- Leave

Bulk Actions

- Mark All Present
- Mark All Absent
- Mark All Late
- Mark All Leave
- Clear Attendance

Bulk actions must also use AJAX.

---

# 11. Remark

Each student has an optional remark field.

Changes should be saved together with attendance status or via AJAX on blur.

---

# 12. Notifications

Use SweetAlert2 Toast.

Show notifications for

- Attendance Saved
- Bulk Update
- Validation Error
- Server Error

Do not use browser alert().

---

# 13. Loading State

Show loading spinner when

- Loading students
- Saving attendance
- Bulk update

Disable related buttons during processing.

---

# 14. Empty State

If no students are found

Display

- Icon
- "No Students Found"
- Instruction to change filters

---

# 15. Responsive Design

Desktop

- Sticky table header
- Sticky Roll Number
- Sticky Student Name

Tablet

- Responsive table
- Horizontal scroll

Mobile

- Student Card Layout
- Large touch buttons
- One student per card

---

# 16. Print View

Provide print-friendly monthly attendance.

Hide

- Sidebar
- Navbar
- Action buttons

Show only report content.

---

# 17. Accessibility

Support

- Keyboard navigation
- Screen readers
- Focus states
- Color contrast

Do not rely only on colors to indicate attendance status.

---

# 18. Performance Rules

- No page reload
- One AJAX request per student update
- Minimal JSON payload
- Use eager loading
- Avoid unnecessary DOM updates

Support classes with up to 500 students.

---

# 19. Acceptance Checklist

Before approval verify

✓ Filter loads students correctly

✓ Attendance updates via AJAX

✓ Summary updates instantly

✓ Save status displayed

✓ Bulk actions work

✓ Search works

✓ Responsive on desktop/tablet/mobile

✓ Print view works

✓ No page reload

✓ SweetAlert2 notifications work

✓ Flatpickr works

✓ Tom Select works

✓ UI follows UI_STANDARDS.md

---

# 20. Definition of Done

The Attendance UI is complete when

- Teachers can take attendance without page reload.
- Each attendance update is saved automatically.
- Summary updates in real time.
- Bulk actions function correctly.
- UI is responsive and accessible.
- Performance requirements are met.
- All components follow the project's UI standards.