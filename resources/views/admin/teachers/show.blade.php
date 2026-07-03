@extends('admin.layouts.master')

@section('title', 'Teacher Profile')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Teacher Profile</h1>
            <div>
                @can('teacher-edit')
                    <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                @endcan
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-body text-center">
                        <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:150px;height:150px;font-size:3rem;">
                            {{ strtoupper(substr($teacher->first_name, 0, 1)) }}{{ strtoupper(substr($teacher->last_name, 0, 1)) }}
                        </div>
                        <h5>{{ $teacher->full_name }}</h5>
                        <p class="text-muted mb-1">
                            <span class="badge bg-secondary">{{ $teacher->employee_id }}</span>
                        </p>
                        @if ($teacher->designation)
                            <p class="text-muted mb-0">{{ $teacher->designation }}</p>
                        @endif
                        <p class="mt-2">
                            @php
                                $badge = match($teacher->status) {
                                    'active' => 'bg-success',
                                    'inactive' => 'bg-warning text-dark',
                                    'suspended' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badge }} fs-6">{{ ucfirst($teacher->status) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div x-data="{ activeTab: 'info' }">
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link" :class="activeTab === 'info' ? 'active' : ''" href="#" @click.prevent="activeTab = 'info'">
                                <i class="bi bi-info-circle"></i> Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" :class="activeTab === 'departments' ? 'active' : ''" href="#" @click.prevent="activeTab = 'departments'">
                                <i class="bi bi-building"></i> Departments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" :class="activeTab === 'subjects' ? 'active' : ''" href="#" @click.prevent="activeTab = 'subjects'">
                                <i class="bi bi-journal"></i> Subjects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" :class="activeTab === 'qualifications' ? 'active' : ''" href="#" @click.prevent="activeTab = 'qualifications'">
                                <i class="bi bi-mortarboard"></i> Qualifications
                            </a>
                        </li>
                    </ul>

                    <div x-show="activeTab === 'info'">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header"><h5 class="mb-0">Personal Information</h5></div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width:200px;">Date of Birth</th>
                                        <td>{{ $teacher->date_of_birth?->format('d M Y') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td>{{ $teacher->gender ? ucfirst($teacher->gender) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>{{ $teacher->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $teacher->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{ $teacher->address ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Joining Date</th>
                                        <td>{{ $teacher->joining_date?->format('d M Y') ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'departments'">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Assigned Departments</h5>
                                @can('teacher-edit')
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#departmentsModal">
                                        <i class="bi bi-plus"></i> Assign
                                    </button>
                                @endcan
                            </div>
                            <div class="card-body">
                                @if ($teacher->departments->isNotEmpty())
                                    <ul class="list-group">
                                        @foreach ($teacher->departments as $dept)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><strong>{{ $dept->name }}</strong> ({{ $dept->code }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted mb-0">No departments assigned.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'subjects'">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Assigned Subjects</h5>
                                @can('teacher-edit')
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#subjectsModal">
                                        <i class="bi bi-plus"></i> Assign
                                    </button>
                                @endcan
                            </div>
                            <div class="card-body">
                                @if ($teacher->subjects->isNotEmpty())
                                    <ul class="list-group">
                                        @foreach ($teacher->subjects as $subj)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><strong>{{ $subj->name }}</strong> ({{ $subj->code }})</span>
                                                <span class="badge bg-secondary">{{ $subj->credits }} cr</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted mb-0">No subjects assigned.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'qualifications'">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header"><h5 class="mb-0">Qualifications</h5></div>
                            <div class="card-body">
                                @if ($teacher->qualifications->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Degree</th>
                                                    <th>Institution</th>
                                                    <th>Year</th>
                                                    <th>Grade</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($teacher->qualifications as $qual)
                                                    <tr>
                                                        <td>{{ $qual->degree }}</td>
                                                        <td>{{ $qual->institution }}</td>
                                                        <td>{{ $qual->year ?? '-' }}</td>
                                                        <td>{{ $qual->grade ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No qualifications recorded.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('teacher-edit')
        {{-- Assign Subjects Modal --}}
        <div class="modal fade" id="subjectsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.teachers.subjects', $teacher->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Assign Subjects</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Select Subjects</label>
                                <select name="subject_ids[]" class="form-select" multiple size="10">
                                    @foreach ($teacher->subjects as $assigned)
                                        <option value="{{ $assigned->id }}" selected>{{ $assigned->name }} ({{ $assigned->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Assign Departments Modal --}}
        <div class="modal fade" id="departmentsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.teachers.departments', $teacher->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Assign Departments</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Select Departments</label>
                                <select name="department_ids[]" class="form-select" multiple size="8">
                                    @foreach ($teacher->departments as $assigned)
                                        <option value="{{ $assigned->id }}" selected>{{ $assigned->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection
