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

<div class="container-fluid py-4" x-data="examinations">
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
            <div class="row g-3">
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                    <select id="academic_year_id" class="form-select" x-model="filters.academic_year_id">
                        <option value="">Select Academic Year</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                    <select id="semester_id" class="form-select" x-model="filters.semester_id">
                        <option value="">Select Semester</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                    <select id="department_id" class="form-select" x-model="filters.department_id">
                        <option value="">Select Department</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="program_id" class="form-label">Program</label>
                    <select id="program_id" class="form-select" x-model="filters.program_id">
                        <option value="">Select Program</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="shift_id" class="form-label">Shift <span class="text-danger">*</span></label>
                    <select id="shift_id" class="form-select" x-model="filters.shift_id">
                        <option value="">Select Shift</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="section_id" class="form-label">Section <span class="text-danger">*</span></label>
                    <select id="section_id" class="form-select" x-model="filters.section_id">
                        <option value="">Select Section</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="group_id" class="form-label">Group</label>
                    <select id="group_id" class="form-select" x-model="filters.group_id">
                        <option value="">Select Group</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="exam_type" class="form-label">Exam Type</label>
                    <select id="exam_type" class="form-select" x-model="filters.exam_type">
                        <option value="">All Types</option>
                        <option value="midterm">Midterm</option>
                        <option value="final">Final</option>
                        <option value="quiz">Quiz</option>
                        <option value="class_test">Class Test</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" id="search" class="form-control" placeholder="Exam name..." x-model="filters.search">
                </div>
                <div class="col-xl-9 col-md-6 col-12 d-flex align-items-end gap-2 justify-content-md-end">
                    <button type="button" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <button type="button" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="bi bi-info-circle me-2"></i>Exam Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                        <i class="bi bi-pencil-square fs-2 text-primary"></i>
                        <div>
                            <small class="text-muted d-block">Exam Name</small>
                            <strong class="fs-6" x-text="exam.name || 'N/A'">N/A</strong>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                        <i class="bi bi-tag fs-2 text-success"></i>
                        <div>
                            <small class="text-muted d-block">Exam Type</small>
                            <strong class="fs-6" x-text="exam.type || 'N/A'">N/A</strong>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                        <i class="bi bi-calendar fs-2 text-warning"></i>
                        <div>
                            <small class="text-muted d-block">Academic Year</small>
                            <strong class="fs-6" x-text="exam.academic_year || 'N/A'">N/A</strong>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                        <i class="bi bi-calendar-range fs-2 text-info"></i>
                        <div>
                            <small class="text-muted d-block">Start Date</small>
                            <strong class="fs-6" x-text="exam.start_date || 'N/A'">N/A</strong>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                        <i class="bi bi-calendar-check fs-2 text-danger"></i>
                        <div>
                            <small class="text-muted d-block">End Date</small>
                            <strong class="fs-6" x-text="exam.end_date || 'N/A'">N/A</strong>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                        <i class="bi bi-flag fs-2 text-secondary"></i>
                        <div>
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-secondary fs-6" x-text="exam.status || 'Pending'">Pending</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-primary border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-journal-bookmark-fill fs-2 text-primary"></i>
                    <h2 class="mt-2 mb-0 fw-bold" x-text="summary.total">0</h2>
                    <small class="text-muted">Total Exams</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-warning border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clock fs-2 text-warning"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-warning" x-text="summary.upcoming">0</h2>
                    <small class="text-muted">Upcoming</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-success border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-play-circle fs-2 text-success"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-success" x-text="summary.ongoing">0</h2>
                    <small class="text-muted">Ongoing</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-info border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-check-all fs-2 text-info"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-info" x-text="summary.completed">0</h2>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-secondary border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-2 text-secondary"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-secondary" x-text="summary.total_students">0</h2>
                    <small class="text-muted">Total Students</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
