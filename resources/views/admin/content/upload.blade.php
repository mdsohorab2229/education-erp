@extends('admin.layouts.master')

@section('title', 'Upload Content')

@section('content')
<div class="container-fluid py-4" x-data="contentUpload()">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">Upload Digital Content</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.contents.index') }}">Content</a></li>
                    <li class="breadcrumb-item active">Upload</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="bi bi-upload me-2"></i>Upload New Content
            </h5>
        </div>
        <div class="card-body">
            <form @@submit.prevent="submitUpload">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" x-model="form.title" placeholder="Enter content title">
                        <div class="invalid-feedback" x-show="errors.title" x-text="errors.title"></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="form.type">
                            <option value="">Select type</option>
                            <option value="pdf">PDF Document</option>
                            <option value="video">Video</option>
                            <option value="notes">Notes</option>
                        </select>
                        <div class="invalid-feedback" x-show="errors.type" x-text="errors.type"></div>
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

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" x-model="form.description" rows="3" placeholder="Optional description"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" @@change="form.file = $event.target.files[0]">
                        <div class="invalid-feedback" x-show="errors.file" x-text="errors.file"></div>
                        <template x-if="form.file">
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark fs-4"></i>
                                <span class="small" x-text="form.file.name"></span>
                                <span class="badge bg-secondary" x-text="(form.file.size / 1024).toFixed(1) + ' KB'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary" :disabled="uploading">
                        <span x-show="!uploading"><i class="bi bi-cloud-upload me-1"></i> Upload</span>
                        <span x-show="uploading">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                            Uploading...
                        </span>
                    </button>
                    <a href="{{ route('admin.content.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('contentUpload', () => ({
            uploading: false,
            form: {
                title: '',
                type: '',
                section_id: '',
                subject_id: '',
                description: '',
                file: null,
            },
            errors: {},
            init() {
                this.loadOptions();
            },
            loadOptions() {
                axios.get('/api/sections').then(r => {
                    const items = Array.isArray(r.data) ? r.data : (r.data.data || []);
                    items.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        this.$refs.sectionSelect.appendChild(opt);
                    });
                });
                axios.get('/api/subjects').then(r => {
                    const items = Array.isArray(r.data) ? r.data : (r.data.data || []);
                    items.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        this.$refs.subjectSelect.appendChild(opt);
                    });
                });
            },
            submitUpload() {
                this.errors = {};
                this.uploading = true;

                const fd = new FormData();
                fd.append('title', this.form.title);
                fd.append('type', this.form.type);
                fd.append('section_id', this.form.section_id);
                fd.append('subject_id', this.form.subject_id);
                fd.append('description', this.form.description);
                if (this.form.file) {
                    fd.append('file', this.form.file);
                }

                axios.post('{{ route("admin.contents.upload") }}', fd, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                }).then(r => {
                    Swal.fire({ icon: 'success', title: 'Uploaded!', text: 'Content uploaded successfully.', timer: 2000, showConfirmButton: false });
                    this.form = { title: '', type: '', section_id: '', subject_id: '', description: '', file: null };
                    document.querySelector('input[type="file"]').value = '';
                }).catch(err => {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors || {};
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: err.response?.data?.message || 'Upload failed.' });
                    }
                }).finally(() => { this.uploading = false; });
            },
        }));
    });
</script>
@endsection
