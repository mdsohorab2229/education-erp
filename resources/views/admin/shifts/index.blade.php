@extends('admin.layouts.master')

@section('title', 'Shifts')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Shifts</h1>
            @can('shift-create')
                <a href="{{ route('admin.shifts.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Create Shift
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
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($shifts as $shift)
                                <tr>
                                    <td>{{ $shift->id }}</td>
                                    <td>{{ $shift->name }}</td>
                                    <td>{{ $shift->start_time ? date('h:i A', strtotime($shift->start_time)) : '-' }}</td>
                                    <td>{{ $shift->end_time ? date('h:i A', strtotime($shift->end_time)) : '-' }}</td>
                                    <td>
                                        @can('shift-edit')
                                            <a href="{{ route('admin.shifts.edit', $shift->id) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('shift-delete')
                                            <form action="{{ route('admin.shifts.destroy', $shift->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="5" class="text-center">No shifts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
