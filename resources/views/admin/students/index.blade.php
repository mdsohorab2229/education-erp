@extends('admin.layouts.master')

@section('title', 'Students')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Students</h1>
            @can('student-create')
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Admit Student
                </a>
            @endcan
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card border-left-primary shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs text-uppercase text-muted mb-1">Total Students</div>
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
                <form method="GET" action="{{ route('admin.students.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small">Academic Year</label>
                        <select name="academic_year_id" class="form-select form-select-sm">
                            <option value="">All Years</option>
                            @foreach ($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ ($filters['academic_year_id'] ?? '') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Program</label>
                        <select name="program_id" class="form-select form-select-sm">
                            <option value="">All Programs</option>
                            @foreach ($programs as $p)
                                <option value="{{ $p->id }}" {{ ($filters['program_id'] ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Section</label>
                        <select name="section_id" class="form-select form-select-sm">
                            <option value="">All Sections</option>
                            @foreach ($sections as $s)
                                <option value="{{ $s->id }}" {{ ($filters['section_id'] ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Shift</label>
                        <select name="shift_id" class="form-select form-select-sm">
                            <option value="">All Shifts</option>
                            @foreach ($shifts as $sh)
                                <option value="{{ $sh->id }}" {{ ($filters['shift_id'] ?? '') == $sh->id ? 'selected' : '' }}>{{ $sh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="graduated" {{ ($filters['status'] ?? '') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="suspended" {{ ($filters['status'] ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Name / ID..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-12 mt-2">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-x-circle"></i> Clear</a>
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
                                <th>Admission No</th>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Program</th>
                                <th>Section</th>
                                <th>Shift</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr>
                                    <td>{{ $student->admission_no }}</td>
                                    <td>{{ $student->roll_no ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="text-decoration-none fw-medium">
                                            {{ $student->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $student->program->name ?? '-' }}</td>
                                    <td>{{ $student->section->name ?? '-' }}</td>
                                    <td>{{ $student->shift->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $badge = match($student->status) {
                                                'active' => 'bg-success',
                                                'inactive' => 'bg-warning text-dark',
                                                'graduated' => 'bg-info',
                                                'suspended' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ ucfirst($student->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @can('student-list')
                                                <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endcan
                                            @can('student-edit')
                                                <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan
                                            @can('student-edit')
                                                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#statusModal{{ $student->id }}">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            @endcan
                                            @can('student-delete')
                                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>

                                        @can('student-edit')
                                            <div class="modal fade" id="statusModal{{ $student->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.students.status', $student->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Change Status</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <select name="status" class="form-select">
                                                                    <option value="active" {{ $student->status == 'active' ? 'selected' : '' }}>Active</option>
                                                                    <option value="inactive" {{ $student->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                                    <option value="graduated" {{ $student->status == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                                                    <option value="suspended" {{ $student->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                                                </select>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $students->appends($filters)->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
