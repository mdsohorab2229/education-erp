@extends('admin.layouts.master')

@section('title', 'Academic Years')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Academic Years</h1>
            @can('academic-year-create')
                <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Create Academic Year
                </a>
            @endcan
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Current</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($academicYears as $academicYear)
                                <tr>
                                    <td>{{ $academicYear->id }}</td>
                                    <td>{{ $academicYear->name }}</td>
                                    <td>{{ $academicYear->start_date->format('M d, Y') }}</td>
                                    <td>{{ $academicYear->end_date->format('M d, Y') }}</td>
                                    <td>
                                        @if ($academicYear->is_current)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('academic-year-edit')
                                            <a href="{{ route('admin.academic-years.edit', $academicYear->id) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('academic-year-delete')
                                            <form action="{{ route('admin.academic-years.destroy', $academicYear->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No academic years found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
