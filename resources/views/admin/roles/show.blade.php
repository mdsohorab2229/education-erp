@extends('admin.layouts.master')

@section('title', 'Role Details')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Role: {{ $role->name }}</h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <p><strong>ID:</strong> {{ $role->id }}</p>
                <p><strong>Name:</strong> {{ $role->name }}</p>
                <p><strong>Permissions:</strong></p>
                @foreach ($role->permissions as $permission)
                    <span class="badge bg-info">{{ $permission->name }}</span>
                @endforeach
            </div>
        </div>

        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary mt-3">Back to Roles</a>
    </div>
@endsection
