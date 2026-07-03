# UI_STANDARDS.md

Version: 1.0

Status: Approved

Project: Education ERP

Applies To

- Dashboard
- Student Module
- Teacher Module
- Attendance Module
- Examination Module
- Finance Module
- HR Module
- Library Module
- Inventory Module
- Parent Portal

---

# 1. Objective

This document defines the UI/UX standards for the entire Education ERP.

Every module must follow these standards to maintain a consistent, modern, responsive and production-ready user interface.

These standards are mandatory.

---

# 2. Design Principles

Every page should be

- Clean
- Simple
- Fast
- Responsive
- Accessible
- Reusable
- Consistent

Follow

- KISS
- DRY
- Mobile First
- Component Based Design

---

# 3. Technology Stack

Frontend

- Bootstrap 5.x
- Bootstrap Icons
- Alpine.js
- Axios
- Flatpickr
- Tom Select
- SweetAlert2
- DataTables

Do not use jQuery except where required by DataTables.

---

# 4. Overall Layout

Every authenticated page should follow this layout.

```
+------------------------------------------------------+

Top Navbar

+-----------+------------------------------------------+

Sidebar | Page Header

|------------------------------------------|

| |

| Page Content |

| |

+-----------+------------------------------------------+

Footer

+------------------------------------------------------+
```

The layout must be consistent across all modules.

---

# 5. Page Container

Every page must use Bootstrap Container Fluid.

Example

```html
<div class="container-fluid py-4">
```

Avoid nested containers.

---

# 6. Grid System

Always use Bootstrap Grid.

Example

```html
row

↓

col-lg-4

↓

card
```

Recommended breakpoints

| Device | Grid |
|----------|------|
| Mobile | col-12 |
| Tablet | col-md-6 |
| Laptop | col-lg-4 |
| Desktop | col-xl-3 |

Never use fixed widths.

---

# 7. Spacing Rules

Use Bootstrap spacing utilities.

Preferred

```
mb-3

mb-4

p-3

p-4

px-3

py-2

g-3

g-4
```

Avoid custom margin values.

---

# 8. Typography

Primary Font

Inter

Fallback

System UI

Sizes

| Element | Size |
|---------|------|
| Page Title | 28px |
| Section Title | 22px |
| Card Title | 18px |
| Body | 14px |
| Table | 14px |
| Small Text | 12px |

Avoid inline font sizes.

---

# 9. Page Header

Every page should contain

Page Title

Breadcrumb

Optional Action Button

Example

```
Student Management

Dashboard / Students

[ Add Student ]
```

---

# 10. Breadcrumb

Every page except Dashboard should include breadcrumbs.

Example

```
Dashboard

↓

Students

↓

Admission
```

---

# 11. Card Standards

Cards are the primary container.

Structure

```
Card

↓

Card Header

↓

Card Body

↓

Card Footer (Optional)
```

Example

```
+-------------------------------+

Student Information

-------------------------------

Card Body

-------------------------------

Footer

+-------------------------------+
```

Rules

Use shadow-sm

Rounded corners

Header background should remain light

No excessive borders

---

# 12. Card Header

Should contain

Title

Optional Badge

Optional Action Button

Example

```
Students

(120)

+ Add
```

---

# 13. Dashboard Cards

Dashboard cards should display

Icon

Title

Count

Trend (optional)

Example

```
👨‍🎓

Students

1,250
```

---

# 14. Form Layout

Every form must follow

```
Card

↓

Section

↓

Row

↓

Form Group
```

Never create long continuous forms.

---

# 15. Form Sections

Large forms should be divided.

Example

Personal Information

Guardian Information

Academic Information

Documents

---

# 16. Labels

Every input requires a label.

Required fields

```
Student Name *

Department *

Section *
```

Never rely only on placeholders.

---

# 17. Input Fields

Use Bootstrap Form Control.

Example

```
Text

Email

Password

Number

Date
```

All inputs should have

100% width

---

# 18. Select Fields

Use

Tom Select

for searchable dropdowns.

Normal Bootstrap Select

for small lists.

---

# 19. Date Picker

Always use Flatpickr.

Never use browser default date picker.

Manual typing should be disabled where business rules require it.

---

# 20. Textarea

Use for

Remarks

Description

Notes

Minimum height

120px

---

# 21. Validation

Display validation under the input.

Example

```
Student Name

____________

Name is required.
```

Never use alert().

---

# 22. Required Fields

Indicate with

Red *

Example

```
Email *

Department *
```

---

# 23. Readonly Fields

Readonly fields

Gray background

Cursor disabled

---

# 24. Disabled Fields

Use disabled only when user interaction is not allowed.

---

# 25. Form Buttons

Primary Action

Right aligned

Example

Cancel

Save

Update

Back

Never use more than one primary button in the same form.

---

# 26. Multi-Step Forms

Large forms should use

Progress Indicator

or

Wizard Layout

Recommended for

Student Admission

Employee Registration

---

# 27. Loading State

Every form submit should show

Loading Spinner

Disable submit button

Prevent duplicate clicks

---

# 28. Empty State

If no data exists

Display

Illustration/Icon

Message

Action Button

Example

"No Students Found"

[ Add Student ]

---

# 29. Accessibility

Every form element must have

Associated Label

Keyboard Navigation

Visible Focus State

Sufficient Color Contrast

---

# 30. Acceptance Checklist

Before approving any UI:

✓ Bootstrap Grid Used

✓ Responsive Layout

✓ Card Structure Followed

✓ Labels Present

✓ Required Fields Marked

✓ Validation Messages Visible

✓ Flatpickr Used

✓ Tom Select Used Where Needed

✓ Loading State Implemented

✓ Empty State Implemented

✓ Accessible

Only after all checks pass should the UI be considered production-ready.

---

# 31. Table Standards

Tables are the primary data presentation component throughout the ERP.

All modules must follow the same table design.

Examples

- Students
- Teachers
- Departments
- Subjects
- Attendance
- Results
- Fee Collection

Never use inconsistent table layouts.

---

# 32. Table Layout

Recommended structure

```

+----------------------------------------------------------------------------------+

Table Header

-----------------------------------------------------------------------------------

Search | Filters | Export | Refresh

-----------------------------------------------------------------------------------

Table

-----------------------------------------------------------------------------------

Pagination

+----------------------------------------------------------------------------------+

```

---

# 33. Table Design Rules

Every table must

- Use Bootstrap Table
- Use hover effect
- Use striped rows where appropriate
- Use responsive wrapper
- Support keyboard navigation

Example

```html
<div class="table-responsive">
    <table class="table table-hover align-middle">
    </table>
</div>
```

Never use fixed width tables.

---

# 34. Table Header

Headers should use

- Medium font weight
- Sticky header for long tables
- Light background

Attendance module

Sticky Header

Sticky Roll Column

Sticky Student Name

---

# 35. Table Columns

Columns should remain consistent.

Example

Student List

Roll

Photo

Student ID

Student Name

Department

Section

Status

Action

Never place Action before primary data.

---

# 36. Table Actions

Actions should always appear on the right.

Preferred order

View

Edit

Delete

Never mix action order across modules.

---

# 37. Table Row Height

Recommended

48px–56px

Never create cramped rows.

---

# 38. Long Text

Use ellipsis.

Example

```

This is a very long stud...

```

Show full text on hover using tooltip.

---

# 39. Empty Tables

Display

Icon

Message

Action Button

Example

```

📄

No records found.

[ Add New ]

```

---

# 40. Responsive Tables

Desktop

Scrollable

Tablet

Horizontal scroll

Mobile

Card View (recommended)

Attendance Module

Desktop

Sticky columns

Mobile

Student Cards

---

# 41. DataTables Standards

Default Features

- Search
- Pagination
- Sorting
- Responsive
- Export
- Column Visibility

Do not enable unnecessary features.

---

# 42. Default Page Size

25

Allow

10

25

50

100

Never load thousands of rows at once.

---

# 43. Server Side Processing

Required for

- Attendance History
- Student List
- Teachers
- Results
- Fees

Use AJAX.

Never load huge datasets into the browser.

---

# 44. Search Rules

Search should support

Student Name

Student ID

Roll Number

Email

Phone

Search should be case insensitive.

---

# 45. Filter Rules

Place filters above the table.

Use

Bootstrap Row

Tom Select

Flatpickr

Never hide important filters inside modals.

---

# 46. Export

Supported

Excel

PDF

CSV

Print

Never export hidden columns.

---

# 47. Pagination

Bottom Right

Example

Previous

1

2

3

Next

Display

Showing 1–25 of 600

---

# 48. Buttons

Bootstrap Buttons only.

Primary

```
btn-primary
```

Secondary

```
btn-secondary
```

Success

```
btn-success
```

Danger

```
btn-danger
```

Warning

```
btn-warning
```

Info

```
btn-info
```

Light

```
btn-light
```

Dark

```
btn-dark
```

---

# 49. Button Sizes

Normal

```
btn
```

Small

```
btn-sm
```

Large

```
btn-lg
```

Avoid custom button sizes.

---

# 50. Button Icons

Every action button should contain an icon.

Examples

View

Eye Icon

Edit

Pencil Icon

Delete

Trash Icon

Save

Disk Icon

Print

Printer Icon

Export

Download Icon

---

# 51. Primary Button Rules

Every page should have only one primary action.

Examples

Add Student

Save Attendance

Publish Result

Avoid multiple blue buttons.

---

# 52. Action Buttons

Recommended order

View

Edit

Delete

Print

History

Never change this order.

---

# 53. Bulk Action Buttons

Display only after selecting records.

Examples

Delete Selected

Export Selected

Assign Selected

Mark Present

Mark Absent

---

# 54. Icon Standards

Library

Bootstrap Icons

Examples

```
bi-house

bi-person

bi-book

bi-pencil

bi-trash

bi-search

bi-printer

bi-download

bi-upload

bi-check-circle

bi-x-circle
```

Do not mix multiple icon libraries.

---

# 55. Badge Standards

Use badges for status.

Primary

Information

Success

Active

Danger

Inactive

Warning

Pending

Secondary

Draft

Example

```
Active

Inactive

Pending
```

---

# 56. Attendance Badge Colors

Present

Green

Absent

Red

Late

Yellow

Leave

Blue

Future

Holiday

Gray

Official Duty

Purple

---

# 57. Status Labels

Never display raw database values.

Display

Present

Instead of

P

Display

Absent

Instead of

A

---

# 58. Alerts

Use Bootstrap Alerts only.

Success

Green

Warning

Yellow

Danger

Red

Info

Blue

Never use JavaScript alert().

---

# 59. Toast Notifications

Use

SweetAlert2 Toast

Position

Top Right

Auto Close

3 Seconds

Show for

Create

Update

Delete

Attendance Saved

File Uploaded

---

# 60. Confirmation Dialog

Always confirm

Delete

Bulk Delete

Reset

Publish

Approve

Never confirm

Simple View

Search

Pagination

---

# 61. Loading Indicators

Use spinner during

AJAX

File Upload

Attendance Save

Export

Disable buttons while loading.

---

# 62. Tooltips

Use Bootstrap Tooltip.

Examples

Edit

Delete

Print

Download

Never rely only on icons.

---

# 63. Dropdown Menus

Use dropdown for

More Actions

Export

Settings

Avoid overcrowding action columns.

---

# 64. Print View

Print layout should

Hide Sidebar

Hide Navbar

Hide Action Buttons

Optimize tables

Use A4 portrait or landscape depending on report.

---

# 65. Acceptance Checklist

Before approving any table or action UI

✓ Bootstrap Table Used

✓ Responsive Wrapper Present

✓ DataTables Configured

✓ Search Enabled

✓ Pagination Enabled

✓ Export Available (where required)

✓ Icons Consistent

✓ Buttons Follow Standard

✓ Status Badges Correct

✓ SweetAlert2 Used

✓ No JavaScript alert()

✓ Confirmation Before Destructive Actions

✓ Mobile Friendly

Only after all checks pass should the UI be considered production-ready.

---

# 66. Color System

A consistent color palette must be used across the ERP.

Never use random colors.

Primary

Bootstrap Primary

```
#0d6efd
```

Success

```
#198754
```

Danger

```
#dc3545
```

Warning

```
#ffc107
```

Info

```
#0dcaf0
```

Secondary

```
#6c757d
```

Light

```
#f8f9fa
```

Dark

```
#212529
```

White

```
#ffffff
```

Body Background

```
#f5f7fb
```

Border

```
#dee2e6
```

---

# 67. Attendance Status Colors

Attendance Status must remain consistent throughout the ERP.

Present

Green

Absent

Red

Late

Yellow

Leave

Blue

Holiday

Gray

Official Duty

Purple

Never change these colors in individual modules.

---

# 68. Text Colors

Primary Text

Dark

Secondary Text

Gray

Muted Text

Bootstrap text-muted

Danger Text

Bootstrap text-danger

Never use inline color styles.

---

# 69. Border Radius

Use Bootstrap defaults.

Cards

```
rounded-3
```

Buttons

```
rounded-2
```

Inputs

```
rounded-2
```

Avoid excessive rounded corners.

---

# 70. Shadow Standards

Cards

```
shadow-sm
```

Dropdown

```
shadow
```

Modals

```
shadow-lg
```

Avoid heavy shadows.

---

# 71. Responsive Design

The application must follow Mobile First design.

Supported Devices

Desktop

Laptop

Tablet

Mobile

Never design Desktop only.

---

# 72. Bootstrap Breakpoints

Extra Small

<576px

Small

≥576px

Medium

≥768px

Large

≥992px

Extra Large

≥1200px

Extra Extra Large

≥1400px

Use Bootstrap Grid only.

---

# 73. Desktop Rules

Sidebar visible

Sticky Header

Large tables

Hover effects

Keyboard shortcuts

Attendance

Sticky Roll

Sticky Name

Sticky Header

---

# 74. Tablet Rules

Sidebar collapsible

Cards stack

Tables scroll

Buttons resize

Avoid horizontal overflow.

---

# 75. Mobile Rules

Sidebar becomes Offcanvas

Cards become full width

Tables become Card Layout

Large buttons

Large touch targets

Minimum touch area

44px

---

# 76. Form Responsiveness

Desktop

2–4 columns

Tablet

2 columns

Mobile

1 column

Never create horizontal scrolling forms.

---

# 77. DataTable Responsiveness

Desktop

Full DataTable

Tablet

Horizontal Scroll

Mobile

Card Layout

Attendance module must switch automatically.

---

# 78. Navigation

Desktop

Sidebar Expanded

Tablet

Collapsible Sidebar

Mobile

Offcanvas Navigation

---

# 79. Dark Mode Ready

The UI must be designed to support Dark Mode in future.

Never hardcode colors.

Prefer Bootstrap CSS Variables.

Example

```
var(--bs-body-bg)

var(--bs-body-color)
```

---

# 80. Dark Mode Components

Support

Cards

Forms

Buttons

Tables

Dropdowns

Navbar

Sidebar

Badges

Alerts

Avoid images with fixed white backgrounds.

---

# 81. Flatpickr Standards

Use Flatpickr for all date inputs.

Configuration

- Calendar Icon
- Disable manual typing (where required)
- Clear Button
- Today Highlight
- Mobile Friendly

Date Format

```
Y-m-d
```

Examples

Attendance Date

Admission Date

Joining Date

Exam Date

Never use browser default date picker.

---

# 82. Tom Select Standards

Use Tom Select for

Department

Section

Subject

Teacher

Student

Configuration

- Search Enabled
- Clear Button
- Placeholder
- Keyboard Navigation

Do not use Tom Select for lists with fewer than 10 items unless consistency is preferred.

---

# 83. SweetAlert2 Standards

SweetAlert2 is the standard notification library.

Use Toast

Create

Update

Delete

Attendance Saved

File Upload

Use Modal

Delete Confirmation

Publish

Approve

Reset

Never use JavaScript alert() or confirm().

---

# 84. Toast Standards

Position

Top End

Timer

3000 ms

Progress Bar

Enabled

Pause On Hover

Enabled

Examples

Student Saved

Attendance Updated

Record Deleted

---

# 85. Modal Standards

Bootstrap Modal

Large Forms

Confirmation Dialog

Preview

Do not place long multi-step forms inside modals.

---

# 86. Loading Indicators

Show loading state during

AJAX

File Upload

Attendance Save

Export

Import

Use Bootstrap Spinner.

Disable related buttons while loading.

---

# 87. Alpine.js Standards

Use Alpine.js for lightweight interactivity.

Recommended Uses

- Toggle panels
- Multi-step forms
- Attendance UI
- Filters
- Live counters
- Tabs
- Modals
- Expand/Collapse

Avoid business logic inside Alpine components.

Business logic belongs to the backend.

---

# 88. Axios Standards

Axios is the standard HTTP client.

Rules

Use JSON

Handle errors globally

Show loading indicators

Use CSRF token

Retry only when appropriate

Never reload the page after successful AJAX operations unless explicitly required.

---

# 89. AJAX UX Standards

Every AJAX request should

Show loading

Disable repeated actions

Display success or error toast

Update only affected UI

Never refresh the entire page unnecessarily.

---

# 90. Accessibility

All UI components must support

Keyboard Navigation

Focus States

Screen Readers

Sufficient Contrast

ARIA Labels where appropriate

Never rely solely on color to convey meaning.

---

# 91. Performance Rules

Minimize DOM updates

Lazy load large datasets

Use pagination

Use eager loading

Avoid unnecessary AJAX requests

Compress assets

Cache static resources

Attendance updates must send only the minimal payload.

---

# 92. Browser Support

Supported

Google Chrome

Microsoft Edge

Mozilla Firefox

Safari

Latest stable versions.

Internet Explorer is not supported.

---

# 93. Print Standards

Hide

Sidebar

Navbar

Buttons

Search

Filters

Print only relevant content.

Support A4 Portrait and Landscape where applicable.

---

# 94. Reusable Components

Use Blade Components whenever possible.

Examples

- Page Header
- Card
- Form Group
- Input
- Select
- Button
- Badge
- Modal
- Alert
- Empty State
- Loader

Avoid duplicated HTML.

---

# 95. Component Naming

Examples

x-page-header

x-card

x-form-input

x-form-select

x-alert

x-empty-state

x-loader

Use consistent naming across the ERP.

---

# 96. UI Consistency

Every module must look and behave consistently.

Student Module

Attendance Module

Teacher Module

Fees Module

HR Module

Reports

Should share the same design language.

---

# 97. Future Ready

The UI architecture must support future enhancements.

- Dark Mode
- RTL Languages
- Mobile App Design Consistency
- Theme Switching
- Dashboard Widgets
- Accessibility Improvements

Without major redesign.

---

# 98. Prohibited Practices

❌ Inline CSS

❌ Inline JavaScript

❌ Random colors

❌ Multiple icon libraries

❌ Browser default alerts

❌ Browser default date picker

❌ Fixed width layouts

❌ Non-responsive tables

❌ Inconsistent spacing

❌ Duplicate UI patterns

---

# 99. UI Review Checklist

Before approving any UI:

✓ Responsive Layout

✓ Bootstrap Grid Used

✓ Bootstrap Components Used

✓ Card Structure Followed

✓ Consistent Buttons

✓ Proper Icons

✓ Accessible Forms

✓ Flatpickr Configured

✓ Tom Select Configured

✓ SweetAlert2 Implemented

✓ Axios Used

✓ Alpine.js Used

✓ Loading State Present

✓ Empty State Present

✓ Print Friendly

✓ Dark Mode Ready

---

# 100. Definition of Done (UI)

A UI implementation is considered production-ready only if:

- It follows all layout and component standards.
- It is fully responsive.
- It passes accessibility checks.
- It supports keyboard navigation.
- It uses reusable Blade Components.
- It follows Bootstrap 5 conventions.
- It is ready for future Dark Mode support.
- It has been manually tested on Desktop, Tablet and Mobile.
- No inline CSS or JavaScript is used.
- The user experience remains consistent across all ERP modules.