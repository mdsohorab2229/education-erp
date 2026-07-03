@extends('admin.layouts.master')

@section('title', 'Permission Details')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Permission: {{ $permission->name }}</h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <p><strong>ID:</strong> {{ $permission->id }}</p>
                <p><strong>Name:</strong> {{ $permission->name }}</p>
                <p><strong>Guard:</strong> {{ $permission->guard_name }}</p>
            </div>
        </div>

        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary mt-3">Back to Permissions</a>
    </div>
@endsection
