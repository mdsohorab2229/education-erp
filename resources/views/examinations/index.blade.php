@extends('admin.layouts.master')

@section('title', 'Examinations')

@section('content')
<style>
    @media print {
        .d-print-none { display: none !important; }
        .navbar, .text-bg-dark, footer, .btn { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6; }
        .container-fluid { padding: 0 !important; }
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">Examinations</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Examinations</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-outline-secondary d-print-none" disabled title="Print">
                <i class="bi bi-printer"></i>
            </button>
        </div>
    </div>

    <div class="card shadow-sm mb-4 d-print-none">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filters
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.exams.index') }}" class="row g-3">
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="academic_year_id" class="form-label">Academic Year</label>
                    <select id="academic_year_id" name="academic_year_id" class="form-select">
                        <option value="">All Academic Years</option>
                        @foreach ($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ ($filters['academic_year_id'] ?? '') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="semester_id" class="form-label">Semester</label>
                    <select id="semester_id" name="semester_id" class="form-select">
                        <option value="">All Semesters</option>
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ ($filters['semester_id'] ?? '') == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="department_id" class="form-label">Department</label>
                    <select id="department_id" name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ ($filters['department_id'] ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="program_id" class="form-label">Program</label>
                    <select id="program_id" name="program_id" class="form-select">
                        <option value="">All Programs</option>
                        @foreach ($programs as $prog)
                            <option value="{{ $prog->id }}" {{ ($filters['program_id'] ?? '') == $prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="shift_id" class="form-label">Shift</label>
                    <select id="shift_id" name="shift_id" class="form-select">
                        <option value="">All Shifts</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ ($filters['shift_id'] ?? '') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="section_id" class="form-label">Section</label>
                    <select id="section_id" name="section_id" class="form-select">
                        <option value="">All Sections</option>
                        @foreach ($sections as $sec)
                            <option value="{{ $sec->id }}" {{ ($filters['section_id'] ?? '') == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="exam_type" class="form-label">Exam Type</label>
                    <select id="exam_type" name="exam_type" class="form-select">
                        <option value="">All Types</option>
                        @foreach ($examTypes as $type)
                            <option value="{{ $type->id }}" {{ ($filters['exam_type'] ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Exam title..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-primary border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-journal-bookmark-fill fs-2 text-primary"></i>
                    <h2 class="mt-2 mb-0 fw-bold">{{ $stats['total'] }}</h2>
                    <small class="text-muted">Total Exams</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-warning border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clock fs-2 text-warning"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-warning">{{ $stats['upcoming'] }}</h2>
                    <small class="text-muted">Upcoming</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-success border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-play-circle fs-2 text-success"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-success">{{ $stats['ongoing'] }}</h2>
                    <small class="text-muted">Ongoing</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-info border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-check-all fs-2 text-info"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-info">{{ $stats['completed'] }}</h2>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="bi bi-list-ul me-2"></i>Exam List
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th>Department</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Subjects</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($exams as $exam)
                            <tr>
                                <td>{{ $exam->title }}</td>
                                <td>{{ $exam->examType->name ?? '-' }}</td>
                                <td>{{ $exam->academicYear->name ?? '-' }}</td>
                                <td>{{ $exam->semester->name ?? '-' }}</td>
                                <td>{{ $exam->department->name ?? '-' }}</td>
                                <td>{{ $exam->start_date?->format('M d, Y') ?? '-' }}</td>
                                <td>{{ $exam->end_date?->format('M d, Y') ?? '-' }}</td>
                                <td>{{ $exam->exam_subjects_count }}</td>
                                <td>
                                    @php
                                        $badge = match($exam->status) {
                                            'published' => 'bg-success',
                                            'completed' => 'bg-info',
                                            'draft' => 'bg-warning text-dark',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ ucfirst($exam->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">No exams found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $exams->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
