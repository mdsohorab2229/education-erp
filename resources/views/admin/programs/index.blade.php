@extends('admin.layouts.master')

@section('title', 'Programs')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Programs</h1>
            @can('program-create')
                <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Create Program
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
                                <th>Code</th>
                                <th>Department</th>
                                <th>Duration (Years)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($programs as $program)
                                <tr>
                                    <td>{{ $program->id }}</td>
                                    <td>{{ $program->name }}</td>
                                    <td>{{ $program->code }}</td>
                                    <td>{{ $program->department->name ?? '-' }}</td>
                                    <td>{{ $program->duration_years }}</td>
                                    <td>
                                        @can('program-edit')
                                            <a href="{{ route('admin.programs.edit', $program->id) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('program-delete')
                                            <form action="{{ route('admin.programs.destroy', $program->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="6" class="text-center">No programs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
