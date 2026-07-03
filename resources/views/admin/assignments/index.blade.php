@extends('admin.layouts.master')

@section('title', 'Assignments')

@section('content')
<div class="container-fluid py-4" x-data="assignmentList()">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">Assignments</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Assignments</li>
                </ol>
            </nav>
        </div>
        @can('assignment-create')
        <a href="{{ route('admin.assignment.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Create Assignment
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
                    <label class="form-label">Status</label>
                    <select class="form-select" x-model="filters.status">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button class="btn btn-primary" @@click="loadAssignments" :disabled="loading">
                        <span x-show="!loading"><i class="bi bi-search me-1"></i> Filter</span>
                        <span x-show="loading"><span class="spinner-border spinner-border-sm"></span> Loading...</span>
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
                    <p class="text-muted mt-2">Loading assignments...</p>
                </div>
            </template>

            <template x-if="!loading && items.length === 0">
                <div class="text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-0">No assignments found. @can('assignment-create')<a href="{{ route('admin.assignment.create') }}">Create the first one</a>.@endcan</p>
                </div>
            </template>

            <template x-if="!loading && items.length > 0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Section</th>
                                <th>Subject</th>
                                <th>Due Date</th>
                                <th>Total Marks</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in items" :key="item.id">
                                <tr>
                                    <td class="fw-medium" x-text="item.title"></td>
                                    <td x-text="item.section?.name || '-'"></td>
                                    <td x-text="item.subject?.name || '-'"></td>
                                    <td class="text-nowrap" x-text="item.due_date || '-'"></td>
                                    <td x-text="item.total_marks ?? '-'"></td>
                                    <td>
                                        <span class="badge" :class="item.status === 'active' ? 'bg-success' : 'bg-secondary'" x-text="item.status"></span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a :href="`/admin/assignments/${item.id}`" class="btn btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @can('assignment-edit')
                                            <button class="btn btn-warning" @@click="editItem(item)" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @endcan
                                            @can('assignment-delete')
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
            </template>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assignmentList', () => ({
            loading: false,
            items: [],
            filters: { section_id: '', status: '' },
            init() {
                this.loadSections();
                this.loadAssignments();
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
            loadAssignments() {
                this.loading = true;
                const params = {};
                if (this.filters.section_id) params.section_id = this.filters.section_id;
                if (this.filters.status) params.status = this.filters.status;
                const url = this.filters.section_id
                    ? `/admin/assignments/by-section?${new URLSearchParams(params)}`
                    : '/admin/assignments';
                axios.get(url).then(r => {
                    this.items = r.data.data || [];
                }).catch(() => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load assignments.' });
                }).finally(() => { this.loading = false; });
            },
            resetFilters() {
                this.filters = { section_id: '', status: '' };
                this.loadAssignments();
            },
            confirmDelete(item) {
                Swal.fire({
                    title: 'Delete Assignment?',
                    text: `Delete "${item.title}"? This cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Delete',
                }).then(res => {
                    if (res.isConfirmed) {
                        axios.delete(`/admin/assignments/${item.id}`).then(() => {
                            Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1500, showConfirmButton: false });
                            this.items = this.items.filter(i => i.id !== item.id);
                        }).catch(() => {
                            Swal.fire({ icon: 'error', title: 'Failed', text: 'Could not delete assignment.' });
                        });
                    }
                });
            },
            editItem(item) {
                Swal.fire({
                    title: 'Edit Assignment',
                    html: `
                        <form id="edit-assignment-form" class="text-start">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input id="edit-title" class="form-control" value="${item.title}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea id="edit-description" class="form-control" rows="3">${item.description || ''}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Due Date</label>
                                <input id="edit-due-date" type="date" class="form-control" value="${item.due_date}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Marks</label>
                                <input id="edit-total-marks" type="number" class="form-control" value="${item.total_marks}" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select id="edit-status" class="form-select">
                                    <option value="active" ${item.status === 'active' ? 'selected' : ''}>Active</option>
                                    <option value="inactive" ${item.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                </select>
                            </div>
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    confirmButtonColor: '#0d6efd',
                    preConfirm: () => {
                        const title = document.getElementById('edit-title').value;
                        const description = document.getElementById('edit-description').value;
                        const due_date = document.getElementById('edit-due-date').value;
                        const total_marks = document.getElementById('edit-total-marks').value;
                        const status = document.getElementById('edit-status').value;
                        if (!title) { Swal.showValidationMessage('Title is required'); return false; }
                        if (!due_date) { Swal.showValidationMessage('Due date is required'); return false; }
                        if (!total_marks) { Swal.showValidationMessage('Total marks is required'); return false; }
                        return axios.put(`/admin/assignments/${item.id}`, {
                            teacher_id: item.teacher_id,
                            subject_id: item.subject_id,
                            section_id: item.section_id,
                            title, description, due_date, total_marks, status,
                        }).then(r => {
                            Object.assign(item, r.data.data);
                            Swal.fire({ icon: 'success', title: 'Updated!', timer: 1500, showConfirmButton: false });
                        }).catch(err => {
                            const msg = err.response?.data?.message || 'Update failed.';
                            Swal.showValidationMessage(msg);
                        });
                    },
                });
            },
        }));
    });
</script>
@endsection
