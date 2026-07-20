@extends('admin.layouts.master')

@section('title', 'Marks Entry')

@section('content')
<style>
    .marks-entry-table thead th {
        position: sticky;
        top: 0;
        z-index: 3;
        background-color: var(--bs-table-bg-state, var(--bs-table-bg-type, var(--bs-table-bg)));
        box-shadow: inset 0 1px 0 rgba(0,0,0,.05), inset 0 -1px 0 rgba(0,0,0,.1);
    }
    .marks-entry-table .sticky-roll,
    .marks-entry-table .sticky-student {
        position: sticky;
        z-index: 2;
        background-color: var(--bs-table-bg-state, var(--bs-table-bg-type, var(--bs-table-bg)));
    }
    .marks-entry-table thead .sticky-roll,
    .marks-entry-table thead .sticky-student {
        z-index: 4;
    }
    .marks-entry-table .sticky-roll {
        left: 0;
        min-width: 80px;
    }
    .marks-entry-table .sticky-student {
        left: 80px;
        min-width: 180px;
    }
    .marks-entry-table .photo-col {
        width: 70px;
    }
    .marks-entry-table .marks-input {
        width: 90px;
        min-width: 70px;
    }
    .marks-entry-table .total-col {
        width: 80px;
    }
    .marks-entry-table .grade-col {
        width: 80px;
    }
    .marks-entry-table .remark-input {
        min-width: 140px;
    }
    .marks-entry-table .status-col {
        width: 110px;
    }
    .marks-entry-table .marks-input:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    @media print {
        .d-print-none { display: none !important; }
        .navbar, .text-bg-dark, footer, .btn { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
        .container-fluid { padding: 0 !important; max-width: 100% !important; }
        .marks-entry-table { font-size: 9pt; }
        .marks-entry-table th,
        .marks-entry-table td { padding: 4px 6px !important; }
        .table-responsive { overflow: visible !important; }
        .marks-input { border: none !important; background: transparent !important; padding: 0 !important; }
        .remark-input { border: none !important; background: transparent !important; padding: 0 !important; }
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">Marks Entry</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Examinations</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Marks Entry</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-1 d-print-none">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()" title="Print">
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
            <form method="GET" action="{{ route('admin.marks.index') }}" class="row g-3 align-items-end">
                <div class="col-xl-4 col-md-6 col-12">
                    <label for="exam_subject_id" class="form-label">Exam Subject <span class="text-danger">*</span></label>
                    <select id="exam_subject_id" name="exam_subject_id" class="form-select" required>
                        <option value="">Select Exam Subject</option>
                        @foreach ($examSubjects as $es)
                            <option value="{{ $es->id }}" {{ ($examSubjectId ?? '') == $es->id ? 'selected' : '' }}>
                                {{ $es->subject->name ?? 'Subject' }} - {{ $es->exam->title ?? 'Exam' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-8 col-md-6 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-people me-1"></i> Load Students
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-primary border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-2 text-primary"></i>
                    <h2 class="mt-2 mb-0 fw-bold">{{ $stats['total'] }}</h2>
                    <small class="text-muted">Total Students</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-success border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle fs-2 text-success"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-success">{{ $stats['entered'] }}</h2>
                    <small class="text-muted">Marks Entered</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-info border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bar-chart fs-2 text-info"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-info">{{ number_format($stats['average'], 1) }}</h2>
                    <small class="text-muted">Average Total</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-warning border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-trophy fs-2 text-warning"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-warning">{{ $stats['highest'] }}</h2>
                    <small class="text-muted">Highest Total</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-secondary border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-pie-chart fs-2 text-secondary"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-secondary">{{ $stats['completion'] }}%</h2>
                    <small class="text-muted">Completion</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0">
                <i class="bi bi-pencil-square me-2"></i>Marks Entry
                <span class="badge bg-secondary ms-1">{{ $marks->count() }}</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                @if ($examSubject)
                    <span class="badge bg-info fs-6 px-3 py-2">
                        <i class="bi bi-journal me-1"></i>
                        {{ $examSubject->subject->name ?? 'Exam' }}
                    </span>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @if ($examSubject && $marks->count() > 0)
                    <form method="POST" action="{{ route('admin.marks.store') }}" id="marksForm">
                        @csrf
                        <input type="hidden" name="exam_subject_id" value="{{ $examSubject->id }}">
                        <table class="table table-bordered table-hover align-middle mb-0 marks-entry-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="sticky-roll text-center">Roll</th>
                                    <th class="text-center photo-col">Photo</th>
                                    <th class="sticky-student">Student</th>
                                    <th class="text-center">Theory<br><small class="text-muted fw-normal">({{ $examSubject->full_mark ?? 100 }})</small></th>
                                    <th class="text-center">Practical<br><small class="text-muted fw-normal">({{ $examSubject->practical_mark ?? 50 }})</small></th>
                                    <th class="text-center">Viva<br><small class="text-muted fw-normal">({{ $examSubject->viva_mark ?? 25 }})</small></th>
                                    <th class="text-center total-col">Total</th>
                                    <th class="text-center grade-col">Grade</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($marks as $mark)
                                    @php
                                        $student = $mark->student;
                                        $photoUrl = $student->photo ?: 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) . '&size=40&rounded=true&background=f0f0f0&color=333';
                                        $obtained = (float) ($mark->obtained_mark ?? 0);
                                        $practical = (float) ($mark->practical_mark ?? 0);
                                        $viva = (float) ($mark->viva_mark ?? 0);
                                        $total = $mark->total_mark ?? ($obtained + $practical + $viva);
                                    @endphp
                                    <tr>
                                        <td class="sticky-roll text-center fw-medium">{{ $student->roll_no ?? '-' }}</td>
                                        <td class="text-center photo-col">
                                            <img src="{{ $photoUrl }}" alt="{{ $student->full_name }}" class="rounded-circle" width="40" height="40" loading="lazy">
                                        </td>
                                        <td class="sticky-student">
                                            <span class="fw-medium">{{ $student->full_name }}</span>
                                        </td>
                                        <td class="text-center p-1">
                                            <input type="number" class="form-control form-control-sm marks-input text-center"
                                                   placeholder="0" min="0" step="0.5"
                                                   name="marks[{{ $mark->id }}][obtained_mark]"
                                                   value="{{ $mark->obtained_mark ?? '' }}">
                                            <input type="hidden" name="marks[{{ $mark->id }}][student_id]" value="{{ $student->id }}">
                                        </td>
                                        <td class="text-center p-1">
                                            <input type="number" class="form-control form-control-sm marks-input text-center"
                                                   placeholder="0" min="0" step="0.5"
                                                   name="marks[{{ $mark->id }}][practical_mark]"
                                                   value="{{ $mark->practical_mark ?? '' }}">
                                        </td>
                                        <td class="text-center p-1">
                                            <input type="number" class="form-control form-control-sm marks-input text-center"
                                                   placeholder="0" min="0" step="0.5"
                                                   name="marks[{{ $mark->id }}][viva_mark]"
                                                   value="{{ $mark->viva_mark ?? '' }}">
                                        </td>
                                        <td class="text-center fw-bold total-col">{{ $total ?: '--' }}</td>
                                        <td class="text-center grade-col">{{ $mark->grade->grade_letter ?? '--' }}</td>
                                        <td class="p-1">
                                            <input type="text" class="form-control form-control-sm remark-input" placeholder="Remark..."
                                                   name="marks[{{ $mark->id }}][remark]"
                                                   value="{{ $mark->remark ?? '' }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end p-3 d-print-none">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-save me-1"></i> Save All Marks
                            </button>
                        </div>
                    </form>
                @else
                    <table class="table table-bordered table-hover align-middle mb-0 marks-entry-table">
                        <thead class="table-light">
                            <tr>
                                <th class="sticky-roll text-center">Roll</th>
                                <th class="text-center photo-col">Photo</th>
                                <th class="sticky-student">Student</th>
                                <th class="text-center">Theory<br><small class="text-muted fw-normal">(100)</small></th>
                                <th class="text-center">Practical<br><small class="text-muted fw-normal">(50)</small></th>
                                <th class="text-center">Viva<br><small class="text-muted fw-normal">(25)</small></th>
                                <th class="text-center total-col">Total</th>
                                <th class="text-center grade-col">Grade</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Select an exam subject and click Load Students.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 d-print-none">
        <div>
            <span class="text-muted small">Edit marks in the table above and click Save All Marks when done.</span>
        </div>
    </div>
</div>
@endsection
