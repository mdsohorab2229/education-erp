@extends('admin.layouts.master')

@section('title', 'Student Profile')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Student Profile</h1>
            <div>
                @can('student-edit')
                    <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                @endcan
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-body text-center">
                        @if ($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="Photo" class="img-thumbnail rounded-circle mb-3" width="150">
                        @else
                            <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:150px;height:150px;font-size:3rem;">
                                {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                            </div>
                        @endif
                        <h5>{{ $student->full_name }}</h5>
                        <p class="text-muted mb-1">
                            <span class="badge bg-secondary">{{ $student->admission_no }}</span>
                        </p>
                        @if ($student->roll_no)
                            <p class="text-muted mb-0">Roll No: {{ $student->roll_no }}</p>
                        @endif
                        <p class="mt-2">
                            @php
                                $badge = match($student->status) {
                                    'active' => 'bg-success',
                                    'inactive' => 'bg-warning text-dark',
                                    'graduated' => 'bg-info',
                                    'suspended' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badge }} fs-6">{{ ucfirst($student->status) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-header"><h5 class="mb-0">Personal Information</h5></div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th style="width:200px;">Date of Birth</th>
                                <td>{{ $student->date_of_birth?->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td>{{ ucfirst($student->gender) }}</td>
                            </tr>
                            <tr>
                                <th>Blood Group</th>
                                <td>{{ $student->blood_group ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $student->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $student->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ $student->address ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-header"><h5 class="mb-0">Academic Information</h5></div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th style="width:200px;">Academic Year</th>
                                <td>{{ $student->academicYear?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Program</th>
                                <td>{{ $student->program?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Section</th>
                                <td>{{ $student->section?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Shift</th>
                                <td>{{ $student->shift?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Group</th>
                                <td>{{ $student->group?->name ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-header"><h5 class="mb-0">Guardian Information</h5></div>
                    <div class="card-body">
                        @if ($student->guardian)
                            <table class="table table-sm">
                                <tr>
                                    <th style="width:200px;">Name</th>
                                    <td>{{ $student->guardian->name }}</td>
                                </tr>
                                <tr>
                                    <th>Relation</th>
                                    <td>{{ ucfirst($student->guardian->relation) }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $student->guardian->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $student->guardian->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Occupation</th>
                                    <td>{{ $student->guardian->occupation ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $student->guardian->address ?? '-' }}</td>
                                </tr>
                            </table>
                        @else
                            <p class="text-muted mb-0">No guardian information available.</p>
                        @endif
                    </div>
                </div>

                @if ($student->documents->isNotEmpty())
                    <div class="card shadow-sm mb-3">
                        <div class="card-header"><h5 class="mb-0">Documents</h5></div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach ($student->documents as $doc)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ str_replace('_', ' ', ucfirst($doc->document_type)) }}</span>
                                        <span class="text-muted small">{{ $doc->file_name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
