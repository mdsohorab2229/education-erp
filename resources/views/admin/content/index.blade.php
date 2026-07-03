@extends('admin.layouts.master')

@section('title', 'Digital Content')

@section('content')
<div class="container-fluid py-4" x-data="contentList()">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">Digital Content</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Content</li>
                </ol>
            </nav>
        </div>
        @can('content-upload')
        <a href="{{ route('admin.content.upload') }}" class="btn btn-primary">
            <i class="bi bi-upload me-1"></i> Upload Content
        </a>
        @endcan
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Section</label>
                    <select class="form-select" x-model="filters.section_id" x-ref="sectionSelect">
                        <option value="">All Sections</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Type</label>
                    <select class="form-select" x-model="filters.type">
                        <option value="">All Types</option>
                        <option value="pdf">PDF</option>
                        <option value="video">Video</option>
                        <option value="notes">Notes</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button class="btn btn-primary" @@click="loadContent" :disabled="loading">
                        <span x-show="!loading"><i class="bi bi-search me-1"></i> Filter</span>
                        <span x-show="loading">
                            <span class="spinner-border spinner-border-sm"></span> Loading...
                        </span>
                    </button>
                    <button class="btn btn-secondary" @@click="resetFilters">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <template x-if="loading">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                    <p class="text-muted mt-2">Loading content...</p>
                </div>
            </template>

            <template x-if="!loading && items.length === 0">
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-x fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-0">No content found. @can('content-upload')<a href="{{ route('admin.content.upload') }}">Upload the first file</a>.@endcan</p>
                </div>
            </template>

            <template x-if="!loading && items.length > 0">
                <div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Section</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in items" :key="item.id">
                                    <tr>
                                        <td class="fw-medium" x-text="item.title"></td>
                                        <td>
                                            <span class="badge" :class="{
                                                'bg-danger': item.type === 'pdf',
                                                'bg-primary': item.type === 'video',
                                                'bg-success': item.type === 'notes'
                                            }" x-text="item.type.toUpperCase()"></span>
                                        </td>
                                        <td x-text="item.section?.name || '-'"></td>
                                        <td x-text="item.subject?.name || '-'"></td>
                                        <td>
                                            <span class="badge" :class="{
                                                'bg-success': item.status === 'active',
                                                'bg-warning text-dark': item.status === 'processing',
                                                'bg-danger': item.status === 'failed'
                                            }" x-text="item.status"></span>
                                        </td>
                                        <td class="text-nowrap" x-text="item.created_at ? new Date(item.created_at).toLocaleDateString() : '-'"></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a :href="`/admin/contents/${item.id}`" class="btn btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a :href="`/admin/contents/${item.id}/download`" class="btn btn-success" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                @can('content-edit')
                                                <button class="btn btn-warning" @@click="editItem(item)" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                @endcan
                                                @can('content-delete')
                                                <button class="btn btn-danger" @@click="confirmDelete(item)" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('contentList', () => ({
            loading: false,
            items: [],
            filters: { section_id: '', type: '' },
            init() {
                this.loadSections();
                this.loadContent();
            },
            loadSections() {
                axios.get('/api/sections').then(r => {
                    const data = Array.isArray(r.data) ? r.data : (r.data.data || []);
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        this.$refs.sectionSelect.appendChild(opt);
                    });
                });
            },
            loadContent() {
                this.loading = true;
                const params = {};
                if (this.filters.section_id) params.section_id = this.filters.section_id;
                if (this.filters.type) params.type = this.filters.type;
                const url = this.filters.section_id
                    ? `/admin/contents/by-section?${new URLSearchParams(params)}`
                    : '/admin/contents';
                axios.get(url).then(r => {
                    this.items = r.data.data || [];
                }).catch(() => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load content.' });
                }).finally(() => { this.loading = false; });
            },
            resetFilters() {
                this.filters = { section_id: '', type: '' };
                this.loadContent();
            },
            confirmDelete(item) {
                Swal.fire({
                    title: 'Delete Content?',
                    text: `Delete "${item.title}"? This cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Delete',
                }).then(res => {
                    if (res.isConfirmed) {
                        axios.delete(`/admin/contents/${item.id}`).then(() => {
                            Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1500, showConfirmButton: false });
                            this.items = this.items.filter(i => i.id !== item.id);
                        }).catch(() => {
                            Swal.fire({ icon: 'error', title: 'Failed', text: 'Could not delete content.' });
                        });
                    }
                });
            },
            editItem(item) {
                Swal.fire({
                    title: 'Edit Title',
                    input: 'text',
                    inputValue: item.title,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                }).then(res => {
                    if (res.isConfirmed && res.value && res.value !== item.title) {
                        axios.put(`/admin/contents/${item.id}`, { title: res.value }).then(r => {
                            Swal.fire({ icon: 'success', title: 'Updated!', timer: 1500, showConfirmButton: false });
                            item.title = r.data.data.title;
                        }).catch(() => {
                            Swal.fire({ icon: 'error', title: 'Failed', text: 'Could not update content.' });
                        });
                    }
                });
            },
        }));
    });
</script>
@endsection
