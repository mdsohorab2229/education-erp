<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex" style="min-height: 100vh;">
        <div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 250px;">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <span class="fs-4">{{ config('app.name') }}</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard
                    </a>
                </li>

                {{-- Administration --}}
                <li class="nav-item mt-3">
                    <small class="text-secondary text-uppercase px-2">Administration</small>
                </li>
                @can('role-list')
                <li>
                    <a href="{{ route('admin.roles.index') }}" class="nav-link text-white {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock me-2"></i>
                        Roles
                    </a>
                </li>
                @endcan
                @can('permission-list')
                <li>
                    <a href="{{ route('admin.permissions.index') }}" class="nav-link text-white {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                        <i class="bi bi-key me-2"></i>
                        Permissions
                    </a>
                </li>
                @endcan

                {{-- Academic Setup --}}
                <li class="nav-item mt-3">
                    <small class="text-secondary text-uppercase px-2">Academic Setup</small>
                </li>
                @can('academic-year-list')
                <li>
                    <a href="{{ route('admin.academic-years.index') }}" class="nav-link text-white {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar me-2"></i>
                        Academic Years
                    </a>
                </li>
                @endcan
                @can('department-list')
                <li>
                    <a href="{{ route('admin.departments.index') }}" class="nav-link text-white {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                        <i class="bi bi-building me-2"></i>
                        Departments
                    </a>
                </li>
                @endcan
                @can('program-list')
                <li>
                    <a href="{{ route('admin.programs.index') }}" class="nav-link text-white {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                        <i class="bi bi-book me-2"></i>
                        Programs
                    </a>
                </li>
                @endcan
                @can('section-list')
                <li>
                    <a href="{{ route('admin.sections.index') }}" class="nav-link text-white {{ request()->routeIs('admin.sections.*') ? 'active' : '' }}">
                        <i class="bi bi-columns me-2"></i>
                        Sections
                    </a>
                </li>
                @endcan
                @can('subject-list')
                <li>
                    <a href="{{ route('admin.subjects.index') }}" class="nav-link text-white {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                        <i class="bi bi-journal me-2"></i>
                        Subjects
                    </a>
                </li>
                @endcan
                @can('shift-list')
                <li>
                    <a href="{{ route('admin.shifts.index') }}" class="nav-link text-white {{ request()->routeIs('admin.shifts.*') ? 'active' : '' }}">
                        <i class="bi bi-clock me-2"></i>
                        Shifts
                    </a>
                </li>
                @endcan
                @can('group-list')
                <li>
                    <a href="{{ route('admin.groups.index') }}" class="nav-link text-white {{ request()->routeIs('admin.groups.*') ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i>
                        Groups
                    </a>
                </li>
                @endcan

                {{-- People --}}
                <li class="nav-item mt-3">
                    <small class="text-secondary text-uppercase px-2">People</small>
                </li>
                @can('student-list')
                <li>
                    <a href="{{ route('admin.students.index') }}" class="nav-link text-white {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <i class="bi bi-person-vcard me-2"></i>
                        Students
                    </a>
                </li>
                @endcan
                @can('teacher-list')
                <li>
                    <a href="{{ route('admin.teachers.index') }}" class="nav-link text-white {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                        <i class="bi bi-person-workspace me-2"></i>
                        Teachers
                    </a>
                </li>
                @endcan

                {{-- Academic Operations --}}
                <li class="nav-item mt-3">
                    <small class="text-secondary text-uppercase px-2">Academic Operations</small>
                </li>
                @can('attendance-list')
                <li>
                    <a href="{{ route('attendance.index') }}"
                    class="nav-link text-white {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check me-2"></i>
                        Attendance
                    </a>
                </li>
                @endcan
                @can('routine-list')
                <li>
                    <a href="{{ route('admin.routines.index') }}"
                    class="nav-link text-white {{ request()->routeIs('admin.routines.*') ? 'active' : '' }}">
                        <i class="bi bi-table me-2"></i>
                        Class Routine
                    </a>
                </li>
                @endcan
                @can('content-list')
                <li>
                    <a href="{{ route('admin.content.index') }}"
                    class="nav-link text-white {{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark me-2"></i>
                        Digital Content
                    </a>
                </li>
                @endcan
                @can('assignment-list')
                <li>
                    <a href="{{ route('admin.assignment.index') }}"
                    class="nav-link text-white {{ request()->routeIs('admin.assignment.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text me-2"></i>
                        Assignments
                    </a>
                </li>
                @endcan

                {{-- Examination --}}
                <li class="nav-item mt-3">
                    <small class="text-secondary text-uppercase px-2">Examination</small>
                </li>
                @can('exam-list')
                <li>
                    <a href="{{ route('admin.exams.index') }}"
                    class="nav-link text-white {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
                        <i class="bi bi-pencil-square me-2"></i>
                        Exams
                    </a>
                </li>
                @endcan
                @can('marks-entry')
                <li>
                    <a href="{{ route('admin.marks.index') }}"
                    class="nav-link text-white {{ request()->routeIs('admin.marks.*') && !request()->routeIs('admin.marks.approval.*') ? 'active' : '' }}">
                        <i class="bi bi-input-cursor me-2"></i>
                        Marks Entry
                    </a>
                </li>
                @endcan
                @can('marks-approve')
                <li>
                    <a href="{{ route('admin.marks.approval.pending') }}"
                    class="nav-link text-white {{ request()->routeIs('admin.marks.approval.*') ? 'active' : '' }}">
                        <i class="bi bi-check2-square me-2"></i>
                        Marks Approval
                    </a>
                </li>
                @endcan
            </ul>
            <hr>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </button>
            </form>
        </div>

        <div class="flex-grow-1 d-flex flex-column">
            <nav class="navbar navbar-expand navbar-light bg-white shadow-sm px-3">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1">@yield('title', 'Dashboard')</span>
                    <div class="d-flex align-items-center">
                        <span class="me-3">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </nav>

            <main class="flex-grow-1 p-4 bg-light">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
