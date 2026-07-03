<?php
declare(strict_types=1);

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\RoutineController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard-access');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])
        ->name('roles.index')
        ->middleware('permission:role-list');
    Route::get('/roles/create', [RoleController::class, 'create'])
        ->name('roles.create')
        ->middleware('permission:role-create');
    Route::post('/roles', [RoleController::class, 'store'])
        ->name('roles.store')
        ->middleware('permission:role-create');
    Route::get('/roles/{role}', [RoleController::class, 'show'])
        ->name('roles.show')
        ->middleware('permission:role-list');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->name('roles.edit')
        ->middleware('permission:role-edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->name('roles.update')
        ->middleware('permission:role-edit');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->name('roles.destroy')
        ->middleware('permission:role-delete');

    // Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])
        ->name('permissions.index')
        ->middleware('permission:permission-list');
    Route::get('/permissions/create', [PermissionController::class, 'create'])
        ->name('permissions.create')
        ->middleware('permission:permission-create');
    Route::post('/permissions', [PermissionController::class, 'store'])
        ->name('permissions.store')
        ->middleware('permission:permission-create');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])
        ->name('permissions.show')
        ->middleware('permission:permission-list');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
        ->name('permissions.edit')
        ->middleware('permission:permission-edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
        ->name('permissions.update')
        ->middleware('permission:permission-edit');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])
        ->name('permissions.destroy')
        ->middleware('permission:permission-delete');

    // Academic Years
    Route::get('/academic-years', [AcademicYearController::class, 'index'])
        ->name('academic-years.index')
        ->middleware('permission:academic-year-list');
    Route::get('/academic-years/create', [AcademicYearController::class, 'create'])
        ->name('academic-years.create')
        ->middleware('permission:academic-year-create');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])
        ->name('academic-years.store')
        ->middleware('permission:academic-year-create');
    Route::get('/academic-years/{academicYear}', [AcademicYearController::class, 'show'])
        ->name('academic-years.show')
        ->middleware('permission:academic-year-list');
    Route::get('/academic-years/{academicYear}/edit', [AcademicYearController::class, 'edit'])
        ->name('academic-years.edit')
        ->middleware('permission:academic-year-edit');
    Route::put('/academic-years/{academicYear}', [AcademicYearController::class, 'update'])
        ->name('academic-years.update')
        ->middleware('permission:academic-year-edit');
    Route::delete('/academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])
        ->name('academic-years.destroy')
        ->middleware('permission:academic-year-delete');

    // Departments
    Route::get('/departments', [DepartmentController::class, 'index'])
        ->name('departments.index')
        ->middleware('permission:department-list');
    Route::get('/departments/create', [DepartmentController::class, 'create'])
        ->name('departments.create')
        ->middleware('permission:department-create');
    Route::post('/departments', [DepartmentController::class, 'store'])
        ->name('departments.store')
        ->middleware('permission:department-create');
    Route::get('/departments/{department}', [DepartmentController::class, 'show'])
        ->name('departments.show')
        ->middleware('permission:department-list');
    Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])
        ->name('departments.edit')
        ->middleware('permission:department-edit');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])
        ->name('departments.update')
        ->middleware('permission:department-edit');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])
        ->name('departments.destroy')
        ->middleware('permission:department-delete');

    // Programs
    Route::get('/programs', [ProgramController::class, 'index'])
        ->name('programs.index')
        ->middleware('permission:program-list');
    Route::get('/programs/create', [ProgramController::class, 'create'])
        ->name('programs.create')
        ->middleware('permission:program-create');
    Route::post('/programs', [ProgramController::class, 'store'])
        ->name('programs.store')
        ->middleware('permission:program-create');
    Route::get('/programs/{program}', [ProgramController::class, 'show'])
        ->name('programs.show')
        ->middleware('permission:program-list');
    Route::get('/programs/{program}/edit', [ProgramController::class, 'edit'])
        ->name('programs.edit')
        ->middleware('permission:program-edit');
    Route::put('/programs/{program}', [ProgramController::class, 'update'])
        ->name('programs.update')
        ->middleware('permission:program-edit');
    Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])
        ->name('programs.destroy')
        ->middleware('permission:program-delete');

    // Sections
    Route::get('/sections', [SectionController::class, 'index'])
        ->name('sections.index')
        ->middleware('permission:section-list');
    Route::get('/sections/create', [SectionController::class, 'create'])
        ->name('sections.create')
        ->middleware('permission:section-create');
    Route::post('/sections', [SectionController::class, 'store'])
        ->name('sections.store')
        ->middleware('permission:section-create');
    Route::get('/sections/{section}', [SectionController::class, 'show'])
        ->name('sections.show')
        ->middleware('permission:section-list');
    Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])
        ->name('sections.edit')
        ->middleware('permission:section-edit');
    Route::put('/sections/{section}', [SectionController::class, 'update'])
        ->name('sections.update')
        ->middleware('permission:section-edit');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])
        ->name('sections.destroy')
        ->middleware('permission:section-delete');

    // Subjects
    Route::get('/subjects', [SubjectController::class, 'index'])
        ->name('subjects.index')
        ->middleware('permission:subject-list');
    Route::get('/subjects/create', [SubjectController::class, 'create'])
        ->name('subjects.create')
        ->middleware('permission:subject-create');
    Route::post('/subjects', [SubjectController::class, 'store'])
        ->name('subjects.store')
        ->middleware('permission:subject-create');
    Route::get('/subjects/{subject}', [SubjectController::class, 'show'])
        ->name('subjects.show')
        ->middleware('permission:subject-list');
    Route::get('/subjects/{subject}/edit', [SubjectController::class, 'edit'])
        ->name('subjects.edit')
        ->middleware('permission:subject-edit');
    Route::put('/subjects/{subject}', [SubjectController::class, 'update'])
        ->name('subjects.update')
        ->middleware('permission:subject-edit');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])
        ->name('subjects.destroy')
        ->middleware('permission:subject-delete');

    // Shifts
    Route::get('/shifts', [ShiftController::class, 'index'])
        ->name('shifts.index')
        ->middleware('permission:shift-list');
    Route::get('/shifts/create', [ShiftController::class, 'create'])
        ->name('shifts.create')
        ->middleware('permission:shift-create');
    Route::post('/shifts', [ShiftController::class, 'store'])
        ->name('shifts.store')
        ->middleware('permission:shift-create');
    Route::get('/shifts/{shift}', [ShiftController::class, 'show'])
        ->name('shifts.show')
        ->middleware('permission:shift-list');
    Route::get('/shifts/{shift}/edit', [ShiftController::class, 'edit'])
        ->name('shifts.edit')
        ->middleware('permission:shift-edit');
    Route::put('/shifts/{shift}', [ShiftController::class, 'update'])
        ->name('shifts.update')
        ->middleware('permission:shift-edit');
    Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])
        ->name('shifts.destroy')
        ->middleware('permission:shift-delete');

    // Groups
    Route::get('/groups', [GroupController::class, 'index'])
        ->name('groups.index')
        ->middleware('permission:group-list');
    Route::get('/groups/create', [GroupController::class, 'create'])
        ->name('groups.create')
        ->middleware('permission:group-create');
    Route::post('/groups', [GroupController::class, 'store'])
        ->name('groups.store')
        ->middleware('permission:group-create');
    Route::get('/groups/{group}', [GroupController::class, 'show'])
        ->name('groups.show')
        ->middleware('permission:group-list');
    Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])
        ->name('groups.edit')
        ->middleware('permission:group-edit');
    Route::put('/groups/{group}', [GroupController::class, 'update'])
        ->name('groups.update')
        ->middleware('permission:group-edit');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])
        ->name('groups.destroy')
        ->middleware('permission:group-delete');

    // Students
    Route::get('/students', [StudentController::class, 'index'])
        ->name('students.index')
        ->middleware('permission:student-list');
    Route::get('/students/create', [StudentController::class, 'create'])
        ->name('students.create')
        ->middleware('permission:student-create');
    Route::post('/students', [StudentController::class, 'store'])
        ->name('students.store')
        ->middleware('permission:student-create');
    Route::get('/students/{student}', [StudentController::class, 'show'])
        ->name('students.show')
        ->middleware('permission:student-list');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])
        ->name('students.edit')
        ->middleware('permission:student-edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])
        ->name('students.update')
        ->middleware('permission:student-edit');
    Route::put('/students/{student}/status', [StudentController::class, 'status'])
        ->name('students.status')
        ->middleware('permission:student-edit');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])
        ->name('students.destroy')
        ->middleware('permission:student-delete');

    // Teachers
    Route::get('/teachers', [TeacherController::class, 'index'])
        ->name('teachers.index')
        ->middleware('permission:teacher-list');
    Route::get('/teachers/create', [TeacherController::class, 'create'])
        ->name('teachers.create')
        ->middleware('permission:teacher-create');
    Route::post('/teachers', [TeacherController::class, 'store'])
        ->name('teachers.store')
        ->middleware('permission:teacher-create');
    Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])
        ->name('teachers.show')
        ->middleware('permission:teacher-list');
    Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])
        ->name('teachers.edit')
        ->middleware('permission:teacher-edit');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])
        ->name('teachers.update')
        ->middleware('permission:teacher-edit');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])
        ->name('teachers.destroy')
        ->middleware('permission:teacher-delete');
    Route::post('/teachers/{teacher}/subjects', [TeacherController::class, 'assignSubjects'])
        ->name('teachers.subjects')
        ->middleware('permission:teacher-edit');
    Route::post('/teachers/{teacher}/departments', [TeacherController::class, 'assignDepartments'])
        ->name('teachers.departments')
        ->middleware('permission:teacher-edit');

    // Routines
    Route::get('/routines', [RoutineController::class, 'index'])
        ->name('routines.index')
        ->middleware('permission:routine-list');
    Route::post('/routines', [RoutineController::class, 'store'])
        ->name('routines.store')
        ->middleware('permission:routine-create');
    Route::put('/routines/{routine}', [RoutineController::class, 'update'])
        ->name('routines.update')
        ->middleware('permission:routine-edit');
    Route::delete('/routines/{routine}', [RoutineController::class, 'destroy'])
        ->name('routines.destroy')
        ->middleware('permission:routine-delete');
    Route::get('/routines/weekly', [RoutineController::class, 'weekly'])
        ->name('routines.weekly')
        ->middleware('permission:routine-list');
    Route::get('/routines/teacher', [RoutineController::class, 'teacher'])
        ->name('routines.teacher')
        ->middleware('permission:routine-list');
    Route::get('/routines/student', [RoutineController::class, 'student'])
        ->name('routines.student')
        ->middleware('permission:routine-list');

    // Content Views
    Route::get('/content', [ContentController::class, 'listView'])
        ->name('content.index')
        ->middleware('permission:content-list');
    Route::get('/content/upload', [ContentController::class, 'uploadView'])
        ->name('content.upload')
        ->middleware('permission:content-upload');
    Route::get('/content/{id}', [ContentController::class, 'showView'])
        ->name('content.show')
        ->middleware('permission:content-list');

    // Assignment Views
    Route::get('/assignment', [AssignmentController::class, 'indexView'])
        ->name('assignment.index')
        ->middleware('permission:assignment-list');
    Route::get('/assignment/create', [AssignmentController::class, 'createView'])
        ->name('assignment.create')
        ->middleware('permission:assignment-create');
    Route::get('/assignment/submit', [AssignmentController::class, 'submitView'])
        ->name('assignment.submit')
        ->middleware('permission:assignment-submit');

    // Content
    Route::get('/contents/by-section', [ContentController::class, 'bySection'])
        ->name('contents.by-section')
        ->middleware('permission:content-list');
    Route::get('/contents/by-teacher', [ContentController::class, 'byTeacher'])
        ->name('contents.by-teacher')
        ->middleware('permission:content-list');
    Route::get('/contents', [ContentController::class, 'index'])
        ->name('contents.index')
        ->middleware('permission:content-list');
    Route::post('/contents', [ContentController::class, 'store'])
        ->name('contents.upload')
        ->middleware('permission:content-upload');
    Route::get('/contents/{content}', [ContentController::class, 'show'])
        ->name('contents.show')
        ->middleware('permission:content-list');
    Route::put('/contents/{content}', [ContentController::class, 'update'])
        ->name('contents.update')
        ->middleware('permission:content-edit');
    Route::delete('/contents/{content}', [ContentController::class, 'destroy'])
        ->name('contents.destroy')
        ->middleware('permission:content-delete');
    Route::get('/contents/{content}/download', [ContentController::class, 'download'])
        ->name('contents.download')
        ->middleware('permission:content-download');
    Route::get('/contents/{content}/comments', [ContentController::class, 'comments'])
        ->name('contents.comments')
        ->middleware('permission:content-list');
    Route::post('/contents/{content}/comments', [ContentController::class, 'addComment'])
        ->name('contents.comments.store')
        ->middleware('permission:content-comment');

    // Assignments
    Route::get('/assignments/by-section', [AssignmentController::class, 'bySection'])
        ->name('assignments.by-section')
        ->middleware('permission:assignment-list');
    Route::get('/assignments/by-teacher', [AssignmentController::class, 'byTeacher'])
        ->name('assignments.by-teacher')
        ->middleware('permission:assignment-list');
    Route::get('/assignments/upcoming', [AssignmentController::class, 'upcoming'])
        ->name('assignments.upcoming')
        ->middleware('permission:assignment-list');
    Route::post('/assignments/submit', [AssignmentController::class, 'submit'])
        ->name('assignments.submit')
        ->middleware('permission:assignment-submit');
    Route::get('/assignments', [AssignmentController::class, 'index'])
        ->name('assignments.index')
        ->middleware('permission:assignment-list');
    Route::post('/assignments', [AssignmentController::class, 'store'])
        ->name('assignments.create')
        ->middleware('permission:assignment-create');
    Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])
        ->name('assignments.show')
        ->middleware('permission:assignment-list');
    Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])
        ->name('assignments.update')
        ->middleware('permission:assignment-edit');
    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])
        ->name('assignments.destroy')
        ->middleware('permission:assignment-delete');
    Route::put('/assignments/{submission}/marks', [AssignmentController::class, 'marks'])
        ->name('assignments.review')
        ->middleware('permission:assignment-review');
});

// Attendance Routes
Route::middleware(['auth'])->prefix('attendance')->name('attendance.')->group(function (): void {
    Route::get('/', [AttendanceController::class, 'index'])
        ->name('index')
        ->middleware('permission:attendance-list');

    Route::post('/load-students', [AttendanceController::class, 'loadStudents'])
        ->name('load')
        ->middleware('permission:attendance-create');

    Route::post('/update', [AttendanceController::class, 'update'])
        ->name('update')
        ->middleware('permission:attendance-edit');

    Route::post('/bulk-update', [AttendanceController::class, 'bulkUpdate'])
        ->name('bulk-update')
        ->middleware('permission:attendance-edit');

    Route::get('/session/{session}', [AttendanceController::class, 'session'])
        ->name('session')
        ->middleware('permission:attendance-list');

    Route::get('/history', [AttendanceController::class, 'history'])
        ->name('history')
        ->middleware('permission:attendance-list');
});
