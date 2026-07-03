@extends('admin.layouts.master')

@section('title', 'Submit Assignment')

@section('content')
<div class="container-fluid py-4" x-data="submitAssignment()">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">Submit Assignment</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.assignment.index') }}">Assignments</a></li>
                    <li class="breadcrumb-item active">Submit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0"><i class="bi bi-card-text me-2"></i>Assignment Details</h5>
                </div>
                <div class="card-body">
                    <template x-if="!assignmentLoaded">
                        <div class="mb-3">
                            <label class="form-label">Select Assignment</label>
                            <select class="form-select" x-model="selectedAssignmentId" x-ref="assignmentSelect">
                                <option value="">Choose an assignment...</option>
                            </select>
                        </div>
                    </template>

                    <template x-if="assignmentLoaded">
                        <div>
                            <div class="mb-3">
                                <label class="form-label">Select Assignment</label>
                                <select class="form-select" x-model="selectedAssignmentId" x-ref="assignmentSelect" @@change="loadAssignmentDetail">
                                    <option value="">Choose an assignment...</option>
                                </select>
                            </div>

                            <template x-if="assignment">
                                <div>
                                    <hr>
                                    <h6 x-text="assignment.title" class="mb-2"></h6>
                                    <p class="small text-muted mb-2" x-text="assignment.description || 'No description.'"></p>
                                    <ul class="list-unstyled small mb-0">
                                        <li><strong>Subject:</strong> <span x-text="assignment.subject?.name || '-'"></span></li>
                                        <li><strong>Section:</strong> <span x-text="assignment.section?.name || '-'"></span></li>
                                        <li><strong>Due Date:</strong> <span x-text="assignment.due_date || '-'"></span></li>
                                        <li><strong>Total Marks:</strong> <span x-text="assignment.total_marks ?? '-'"></span></li>
                                    </ul>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0"><i class="bi bi-upload me-2"></i>Upload Submission</h5>
                </div>
                <div class="card-body">
                    <template x-if="!selectedAssignmentId">
                        <div class="text-center py-5">
                            <i class="bi bi-arrow-left fs-1 text-muted d-block mb-2"></i>
                            <p class="text-muted mb-0">Select an assignment from the list to submit.</p>
                        </div>
                    </template>

                    <template x-if="selectedAssignmentId">
                        <form @@submit.prevent="submitAssignment">
                            <div class="mb-3">
                                <label class="form-label">Submission File <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" @@change="form.submission_file = $event.target.files[0]" accept=".pdf,.doc,.docx,.zip,.jpg,.jpeg,.png">
                                <div class="invalid-feedback" x-show="errors.submission_file" x-text="errors.submission_file"></div>
                                <template x-if="form.submission_file">
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark fs-4"></i>
                                        <span class="small" x-text="form.submission_file.name"></span>
                                        <span class="badge bg-secondary" x-text="(form.submission_file.size / 1024).toFixed(1) + ' KB'"></span>
                                    </div>
                                </template>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-success" :disabled="submitting">
                                    <span x-show="!submitting"><i class="bi bi-cloud-upload me-1"></i> Submit</span>
                                    <span x-show="submitting">
                                        <span class="spinner-border spinner-border-sm"></span> Submitting...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>

            <template x-if="submissions.length > 0">
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Your Submissions</h5>
                    </div>
                    <div class="card-body">
                        <template x-for="sub in submissions" :key="sub.id">
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <span class="badge" :class="sub.marks !== null ? 'bg-success' : 'bg-warning text-dark'">
                                        <span x-text="sub.marks !== null ? `Marked: ${sub.marks}` : 'Pending Review'"></span>
                                    </span>
                                    <small class="text-muted ms-2" x-text="new Date(sub.created_at).toLocaleDateString()"></small>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('submitAssignment', () => ({
            selectedAssignmentId: '',
            assignment: null,
            assignmentLoaded: false,
            submitting: false,
            form: { submission_file: null },
            errors: {},
            submissions: [],
            init() {
                this.loadAssignments();
            },
            loadAssignments() {
                axios.get('/admin/assignments').then(r => {
                    const items = r.data.data || [];
                    items.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.title;
                        this.$refs.assignmentSelect.appendChild(opt);
                    });
                    this.assignmentLoaded = true;
                });
            },
            loadAssignmentDetail() {
                if (!this.selectedAssignmentId) return;
                axios.get(`/admin/assignments/${this.selectedAssignmentId}`).then(r => {
                    this.assignment = r.data.data || null;
                });
            },
            submitAssignment() {
                if (!this.form.submission_file) {
                    this.errors = { submission_file: ['Please select a file.'] };
                    return;
                }
                this.errors = {};
                this.submitting = true;
                const fd = new FormData();
                fd.append('assignment_id', this.selectedAssignmentId);
                fd.append('submission_file', this.form.submission_file);

                axios.post('{{ route("admin.assignments.submit") }}', fd, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                }).then(r => {
                    Swal.fire({ icon: 'success', title: 'Submitted!', text: 'Assignment submitted successfully.', timer: 2000, showConfirmButton: false });
                    this.submissions.unshift(r.data.data);
                    this.form.submission_file = null;
                    document.querySelector('input[type="file"]').value = '';
                }).catch(err => {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors || {};
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: err.response?.data?.message || 'Submission failed.' });
                    }
                }).finally(() => { this.submitting = false; });
            },
        }));
    });
</script>
@endsection
