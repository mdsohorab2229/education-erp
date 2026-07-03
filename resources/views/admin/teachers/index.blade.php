@extends('admin.layouts.master')

@section('title', 'Teachers')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Teachers</h1>
            @can('teacher-create')
                <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add Teacher
                </a>
            @endcan
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card border-left-primary shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs text-uppercase text-muted mb-1">Total Teachers</div>
                                <div class="h5 mb-0">{{ $stats['total'] }}</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-people fs-2 text-primary"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-left-success shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs text-uppercase text-muted mb-1">Active</div>
                                <div class="h5 mb-0 text-success">{{ $stats['active'] }}</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-check-circle fs-2 text-success"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-left-warning shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs text-uppercase text-muted mb-1">Inactive</div>
                                <div class="h5 mb-0 text-warning">{{ $stats['inactive'] }}</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-pause-circle fs-2 text-warning"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.teachers.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small">Department</label>
                        <select name="department_id" class="form-select form-select-sm">
                            <option value="">All Departments</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->id }}" {{ ($filters['department_id'] ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ ($filters['status'] ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Name / Employee ID / Email..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Departments</th>
                                <th>Subjects</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($teachers as $teacher)
                                <tr>
                                    <td>{{ $teacher->employee_id }}</td>
                                    <td>
                                        <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="text-decoration-none fw-medium">
                                            {{ $teacher->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $teacher->designation ?? '-' }}</td>
                                    <td>
                                        @foreach ($teacher->departments as $dept)
                                            <span class="badge bg-info me-1">{{ $dept->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($teacher->subjects as $subj)
                                            <span class="badge bg-secondary me-1">{{ $subj->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @php
                                            $badge = match($teacher->status) {
                                                'active' => 'bg-success',
                                                'inactive' => 'bg-warning text-dark',
                                                'suspended' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ ucfirst($teacher->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @can('teacher-list')
                                                <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="btn btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endcan
                                            @can('teacher-edit')
                                                <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan
                                            @can('teacher-delete')
                                                <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No teachers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $teachers->appends($filters)->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
