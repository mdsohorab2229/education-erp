@extends('admin.layouts.master')

@section('title', 'Marks Approval')

@push('styles')
<style>
    .approval-card {
        transition: box-shadow 0.2s ease;
    }
    .approval-card:hover {
        box-shadow: 0 .25rem .75rem rgba(0,0,0,.08);
    }
    .approval-card .btn {
        white-space: nowrap;
    }
    .approval-tab {
        cursor: pointer;
        user-select: none;
    }
    .approval-tab.active {
        font-weight: 600;
    }

    @media print {
        header, nav, .breadcrumb, .no-print, .btn, .approval-tabs {
            display: none !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
            break-inside: avoid;
        }
        .card-header {
            background: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .badge.bg-success { background: #198754 !important; color: #fff !important; }
        .badge.bg-danger  { background: #dc3545 !important; color: #fff !important; }
        .badge.bg-warning { background: #ffc107 !important; color: #000 !important; }
        .stat-card {
            break-inside: avoid;
        }
    }

    @media (max-width: 575.98px) {
        .approval-action-group .btn {
            flex: 1;
            font-size: 0.75rem;
            padding: 0.25rem 0.375rem;
        }
    }
</style>
@endpush

@section('content')
    <div class="container-fluid py-4" x-data="marksApproval" role="main" aria-label="Marks Approval Dashboard" :aria-busy="loading ? 'true' : 'false'">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 no-print">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-check2-square me-2"></i>Marks Approval
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.examinations.index') }}">Examinations</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Marks Approval</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()" title="Print">
                    <i class="bi bi-printer"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" x-on:click="loadMarks" title="Refresh">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <div class="print-header text-center mb-4 d-none d-print-block">
            <h3 class="mb-1">Marks Approval Report</h3>
            <p class="text-muted mb-0">{{ now()->format('F d, Y h:i A') }}</p>
            <hr>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6">
                <div class="card shadow-sm border-start border-warning border-3 h-100 stat-card">
                    <div class="card-body text-center">
                        <i class="bi bi-clock fs-2 text-warning"></i>
                        <h2 class="mt-2 mb-0 fw-bold text-warning" x-text="stats.pending">0</h2>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6">
                <div class="card shadow-sm border-start border-success border-3 h-100 stat-card">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                        <h2 class="mt-2 mb-0 fw-bold text-success" x-text="stats.approved">0</h2>
                        <small class="text-muted">Approved</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6">
                <div class="card shadow-sm border-start border-danger border-3 h-100 stat-card">
                    <div class="card-body text-center">
                        <i class="bi bi-x-circle fs-2 text-danger"></i>
                        <h2 class="mt-2 mb-0 fw-bold text-danger" x-text="stats.rejected">0</h2>
                        <small class="text-muted">Rejected</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6">
                <div class="card shadow-sm border-start border-primary border-3 h-100 stat-card">
                    <div class="card-body text-center">
                        <i class="bi bi-archive fs-2 text-primary"></i>
                        <h2 class="mt-2 mb-0 fw-bold" x-text="stats.total">0</h2>
                        <small class="text-muted">Total Processed</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
                <div class="d-flex align-items-center gap-1" role="tablist" aria-label="Filter by approval status">
                    <button type="button" class="btn btn-sm approval-tab" :class="activeTab === 'pending' ? 'btn-warning active' : 'btn-outline-warning'" x-on:click="activeTab = 'pending'" role="tab" :aria-selected="activeTab === 'pending'" aria-controls="marks-panel">
                        <i class="bi bi-clock me-1"></i>Pending
                        <span class="badge bg-dark text-white ms-1" x-text="stats.pending">0</span>
                    </button>
                    <button type="button" class="btn btn-sm approval-tab" :class="activeTab === 'approved' ? 'btn-success active' : 'btn-outline-success'" x-on:click="activeTab = 'approved'" role="tab" :aria-selected="activeTab === 'approved'" aria-controls="marks-panel">
                        <i class="bi bi-check-circle me-1"></i>Approved
                        <span class="badge bg-dark text-white ms-1" x-text="stats.approved">0</span>
                    </button>
                    <button type="button" class="btn btn-sm approval-tab" :class="activeTab === 'rejected' ? 'btn-danger active' : 'btn-outline-danger'" x-on:click="activeTab = 'rejected'" role="tab" :aria-selected="activeTab === 'rejected'" aria-controls="marks-panel">
                        <i class="bi bi-x-circle me-1"></i>Rejected
                        <span class="badge bg-dark text-white ms-1" x-text="stats.rejected">0</span>
                    </button>
                    <button type="button" class="btn btn-sm approval-tab" :class="activeTab === 'all' ? 'btn-secondary active' : 'btn-outline-secondary'" x-on:click="activeTab = 'all'" role="tab" :aria-selected="activeTab === 'all'" aria-controls="marks-panel">
                        <i class="bi bi-list-ul me-1"></i>All
                        <span class="badge bg-dark text-white ms-1" x-text="stats.total">0</span>
                    </button>
                </div>
                <small class="text-muted" x-text="`Showing ${filteredMarks.length} of ${marks.length}`"></small>
            </div>

            <div class="card-body" id="marks-panel" role="tabpanel">
                <template x-if="loading">
                    <div class="placeholder-glow">
                        <div class="row g-3">
                            <template x-for="i in 6" :key="i">
                                <div class="col-md-6 col-xl-4">
                                    <div class="card border shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <span class="placeholder rounded-circle flex-shrink-0" style="width:44px;height:44px;"></span>
                                                <div class="flex-grow-1">
                                                    <span class="placeholder col-8 d-block mb-1"></span>
                                                    <span class="placeholder col-4 d-block"></span>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2 mb-3">
                                                <span class="placeholder col-3"></span>
                                                <span class="placeholder col-3"></span>
                                                <span class="placeholder col-3"></span>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <span class="placeholder col-4"></span>
                                                <span class="placeholder col-4"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="!loading && marks.length === 0">
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">No marks records available.</p>
                    </div>
                </template>

                <template x-if="!loading && marks.length > 0 && filteredMarks.length === 0">
                    <div class="text-center py-5">
                        <i class="bi bi-filter-circle fs-1 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">No marks match the selected filter.</p>
                    </div>
                </template>

                <template x-if="!loading && filteredMarks.length > 0">
                    <div class="row g-3">
                        <template x-for="mark in filteredMarks" :key="mark.id">
                            <div class="col-md-6 col-xl-4">
                                <div class="card border shadow-sm approval-card h-100">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <img :src="getAvatarUrl(mark)" :alt="mark.name" class="rounded-circle flex-shrink-0" width="44" height="44" loading="lazy">
                                            <div class="flex-grow-1 min-w-0">
                                                <strong class="text-truncate d-block" x-text="mark.name"></strong>
                                                <small class="text-muted d-block">
                                                    <span x-text="`Roll: ${mark.roll_no || '--'}`"></span>
                                                </small>
                                            </div>
                                            <span class="badge flex-shrink-0" :class="statusBadgeClass(mark.approval_status)">
                                                <i class="bi me-1" :class="statusIcon(mark.approval_status)"></i>
                                                <span x-text="mark.approval_status.charAt(0).toUpperCase() + mark.approval_status.slice(1)"></span>
                                            </span>
                                        </div>

                                        <div class="row g-1 mb-3 small">
                                            <div class="col-6">
                                                <span class="text-muted">Theory:</span>
                                                <span class="fw-medium" x-text="mark.obtained_mark != null ? mark.obtained_mark : '--'"></span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted">Practical:</span>
                                                <span class="fw-medium" x-text="mark.practical_mark != null ? mark.practical_mark : '--'"></span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted">Viva:</span>
                                                <span class="fw-medium" x-text="mark.viva_mark != null ? mark.viva_mark : '--'"></span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted">Total:</span>
                                                <span class="fw-medium" x-text="mark.total_mark != null ? mark.total_mark : '--'"></span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted">Grade:</span>
                                                <span class="fw-medium" x-text="mark.grade_letter || '--'"></span>
                                            </div>
                                            <div class="col-6" x-show="mark.approved_by">
                                                <span class="text-muted">Approved by:</span>
                                                <span class="fw-medium" x-text="mark.approved_by"></span>
                                            </div>
                                            <div class="col-12" x-show="mark.remark">
                                                <span class="text-muted">Remark:</span>
                                                <span class="fw-medium text-danger" x-text="mark.remark"></span>
                                            </div>
                                        </div>

                                        <div class="mt-auto pt-2 border-top d-flex flex-wrap gap-1 approval-action-group no-print">
                                            <button type="button" class="btn btn-success btn-sm" x-on:click="approveMark(mark)" :disabled="isProcessing(mark) || mark.approval_status === 'approved'" :aria-label="`Approve ${mark.name}`">
                                                <span x-show="!isProcessing(mark)"><i class="bi bi-check-lg me-1"></i>Approve</span>
                                                <span x-show="isProcessing(mark)"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" x-on:click="rejectMark(mark)" :disabled="isProcessing(mark) || mark.approval_status === 'rejected'" :aria-label="`Reject ${mark.name}`">
                                                <span x-show="!isProcessing(mark)"><i class="bi bi-x-lg me-1"></i>Reject</span>
                                                <span x-show="isProcessing(mark)"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm" x-on:click="resetMark(mark)" :disabled="isProcessing(mark) || mark.approval_status === 'pending'" :aria-label="`Reset approval for ${mark.name}`">
                                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="card-footer bg-white py-2 text-center no-print" x-show="!loading && marks.length > 0">
                <small class="text-muted" x-text="`Showing ${filteredMarks.length} of ${marks.length} mark record(s).`"></small>
            </div>
        </div>
    </div>
@endsection
