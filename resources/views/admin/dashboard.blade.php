@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Dashboard</h1>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card text-bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Welcome</h5>
                        <p class="card-text">You are logged in as <strong>{{ Auth::user()->name }}</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
