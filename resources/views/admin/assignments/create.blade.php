@extends('admin.layouts.master')

@section('title', 'Create Assignment')

@section('content')
<div class="container-fluid py-4" x-data="createAssignment()">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">Create Assignment</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.assignments.index') }}">Assignments</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0"><i class="bi bi-plus-circle me-2"></i>Assignment Details</h5>
        </div>
        <div class="card-body">
            <form @@submit.prevent="submitAssignment">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" x-model="form.title" placeholder="Assignment title">
                        <div class="invalid-feedback" x-show="errors.title" x-text="errors.title"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" x-model="form.description" rows="4" placeholder="Describe the assignment"></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Section <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="form.section_id" x-ref="sectionSelect">
                            <option value="">Select section</option>
                        </select>
                        <div class="invalid-feedback" x-show="errors.section_id" x-text="errors.section_id"></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="form.subject_id" x-ref="subjectSelect">
                            <option value="">Select subject</option>
                        </select>
                        <div class="invalid-feedback" x-show="errors.subject_id" x-text="errors.subject_id"></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" x-model="form.total_marks" min="1" placeholder="e.g. 100">
                        <div class="invalid-feedback" x-show="errors.total_marks" x-text="errors.total_marks"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" x-model="form.due_date">
                        <div class="invalid-feedback" x-show="errors.due_date" x-text="errors.due_date"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Attachment (optional)</label>
                        <input type="file" class="form-control" @@change="form.attachment = $event.target.files[0]">
                        <template x-if="form.attachment">
                            <div class="mt-1 small text-muted" x-text="form.attachment.name"></div>
                        </template>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary" :disabled="saving">
                        <span x-show="!saving"><i class="bi bi-check-lg me-1"></i> Create Assignment</span>
                        <span x-show="saving">
                            <span class="spinner-border spinner-border-sm"></span> Saving...
                        </span>
                    </button>
                    <a href="{{ route('admin.assignment.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('createAssignment', () => ({
            saving: false,
            form: {
                title: '',
                description: '',
                section_id: '',
                subject_id: '',
                total_marks: '',
                due_date: '',
                attachment: null,
            },
            errors: {},
            init() {
                this.loadOptions();
            },
            loadOptions() {
                axios.get('/api/sections').then(r => {
                    const data = Array.isArray(r.data) ? r.data : (r.data.data || []);
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        this.$refs.sectionSelect.appendChild(opt);
                    });
                });
                axios.get('/api/subjects').then(r => {
                    const data = Array.isArray(r.data) ? r.data : (r.data.data || []);
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        this.$refs.subjectSelect.appendChild(opt);
                    });
                });
            },
            submitAssignment() {
                this.errors = {};
                this.saving = true;

                const fd = new FormData();
                fd.append('title', this.form.title);
                fd.append('description', this.form.description);
                fd.append('section_id', this.form.section_id);
                fd.append('subject_id', this.form.subject_id);
                fd.append('total_marks', this.form.total_marks);
                fd.append('due_date', this.form.due_date);
                if (this.form.attachment) {
                    fd.append('attachment', this.form.attachment);
                }

                axios.post('{{ route("admin.assignments.create") }}', fd, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                }).then(r => {
                    Swal.fire({ icon: 'success', title: 'Created!', text: 'Assignment created successfully.', timer: 2000, showConfirmButton: false });
                    this.form = { title: '', description: '', section_id: '', subject_id: '', total_marks: '', due_date: '', attachment: null };
                    document.querySelector('input[type="file"]').value = '';
                }).catch(err => {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors || {};
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: err.response?.data?.message || 'Creation failed.' });
                    }
                }).finally(() => { this.saving = false; });
            },
        }));
    });
</script>
@endsection
