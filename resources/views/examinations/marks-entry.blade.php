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

<div class="container-fluid py-4" x-data="marksEntry">
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
            <button type="button" class="btn btn-outline-secondary" @click="window.print()" title="Print">
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
            <div class="row g-3 align-items-end">
                <div class="col-xl-4 col-md-6 col-12">
                    <label for="exam_subject_id" class="form-label">Exam Subject <span class="text-danger">*</span></label>
                    <select id="exam_subject_id" class="form-select" x-model="examSubjectId">
                        <option value="">Select Exam Subject</option>
                    </select>
                </div>
                <div class="col-xl-8 col-md-6 col-12 d-flex gap-2">
                    <button type="button" class="btn btn-primary" @click="loadStudents" :disabled="loading">
                        <i class="bi bi-people me-1"></i>
                        <span x-show="!loading">Load Students</span>
                        <span x-show="loading">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-primary border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-2 text-primary"></i>
                    <h2 class="mt-2 mb-0 fw-bold" x-text="summary.total">0</h2>
                    <small class="text-muted">Total Students</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-success border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle fs-2 text-success"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-success" x-text="summary.entered">0</h2>
                    <small class="text-muted">Marks Entered</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-info border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bar-chart fs-2 text-info"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-info" x-text="summary.average.toFixed(1)">0.0</h2>
                    <small class="text-muted">Average Total</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-warning border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-trophy fs-2 text-warning"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-warning" x-text="summary.highest">0</h2>
                    <small class="text-muted">Highest Total</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-lg-3 col-md-4 col-6">
            <div class="card shadow-sm border-start border-secondary border-3 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-pie-chart fs-2 text-secondary"></i>
                    <h2 class="mt-2 mb-0 fw-bold text-secondary" x-text="summary.completion + '%'">0%</h2>
                    <small class="text-muted">Completion</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0">
                <i class="bi bi-pencil-square me-2"></i>Marks Entry
                <span class="badge bg-secondary ms-1" x-text="students.length">0</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <template x-if="examSubject">
                    <span class="badge bg-info fs-6 px-3 py-2">
                        <i class="bi bi-journal me-1"></i>
                        <span x-text="examSubject.subject?.name || 'Exam'"></span>
                    </span>
                </template>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 marks-entry-table">
                    <thead class="table-light">
                        <tr>
                            <th class="sticky-roll text-center">Roll</th>
                            <th class="text-center photo-col">Photo</th>
                            <th class="sticky-student">Student</th>
                            <th class="text-center">Theory<br><small class="text-muted fw-normal" x-text="'(' + (examSubject?.full_mark ?? 100) + ')'">(100)</small></th>
                            <th class="text-center">Practical<br><small class="text-muted fw-normal" x-text="'(' + (examSubject?.practical_mark ?? 50) + ')'">(50)</small></th>
                            <th class="text-center">Viva<br><small class="text-muted fw-normal" x-text="'(' + (examSubject?.viva_mark ?? 25) + ')'">(25)</small></th>
                            <th class="text-center total-col">Total</th>
                            <th class="text-center grade-col">Grade</th>
                            <th>Remark</th>
                            <th class="text-center status-col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loading">
                            <tr>
                                <td colspan="10" class="p-0">
                                    <div class="placeholder-glow px-3 py-2">
                                        <template x-for="i in 5" :key="i">
                                            <div class="d-flex align-items-center gap-2 border-bottom py-2" style="min-height:53px;">
                                                <span class="placeholder col-1"></span>
                                                <span class="placeholder rounded-circle flex-shrink-0" style="width:40px;height:40px;"></span>
                                                <span class="placeholder col-2"></span>
                                                <span class="placeholder col-1"></span>
                                                <span class="placeholder col-1"></span>
                                                <span class="placeholder col-1"></span>
                                                <span class="placeholder col-1"></span>
                                                <span class="placeholder col-1"></span>
                                                <span class="placeholder col-1"></span>
                                                <span class="placeholder col-1"></span>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loading && students.length === 0">
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Select an exam subject and click Load Students.</p>
                                </td>
                            </tr>
                        </template>
                        <template x-for="student in students" :key="student.id">
                            <tr>
                                <td class="sticky-roll text-center fw-medium" x-text="student.roll_no"></td>
                                <td class="text-center photo-col">
                                    <img :src="getAvatarUrl(student)" :alt="student.name" class="rounded-circle" width="40" height="40" loading="lazy">
                                </td>
                                <td class="sticky-student">
                                    <span class="fw-medium" x-text="student.name"></span>
                                </td>
                                <td class="text-center p-1">
                                    <input type="number" class="form-control form-control-sm marks-input text-center"
                                           placeholder="0" min="0" step="0.5"
                                            x-model.number="student.obtained_mark"
                                            @input="onMarkChange(student)">
                                </td>
                                <td class="text-center p-1">
                                    <input type="number" class="form-control form-control-sm marks-input text-center"
                                           placeholder="0" min="0" step="0.5"
                                           x-model.number="student.practical_mark"
                                           @input="onMarkChange(student)">
                                </td>
                                <td class="text-center p-1">
                                    <input type="number" class="form-control form-control-sm marks-input text-center"
                                           placeholder="0" min="0" step="0.5"
                                           x-model.number="student.viva_mark"
                                           @input="onMarkChange(student)">
                                </td>
                                <td class="text-center fw-bold total-col" x-text="student.total_mark !== null ? student.total_mark : calcStudentTotal(student) || '--'">--</td>
                                <td class="text-center grade-col" x-text="student.grade?.grade_letter || '--'">--</td>
                                <td class="p-1">
                                    <input type="text" class="form-control form-control-sm remark-input" placeholder="Remark..."
                                           x-model="student.remark"
                                           @input="onMarkChange(student)">
                                </td>
                                <td class="text-center status-col">
                                    <span x-show="getSaveStatusLabel(student) === 'Saving...'" class="badge bg-info">
                                        <i class="bi bi-hourglass-split"></i> Saving...
                                    </span>
                                    <span x-show="getSaveStatusLabel(student) === 'Saved'" class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Saved
                                    </span>
                                    <span x-show="getSaveStatusLabel(student) === 'Failed'" class="badge bg-danger">
                                        <i class="bi bi-exclamation-circle"></i> Failed
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 d-print-none">
        <div>
            <span class="text-muted small">Changes are auto-saved. Tab between fields to enter marks.</span>
        </div>
    </div>
</div>
@endsection
