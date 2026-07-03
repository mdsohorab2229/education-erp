@extends('admin.layouts.master')

@section('title', 'Add Teacher')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Add New Teacher</h1>

        <form action="{{ route('admin.teachers.store') }}" method="POST">
            @csrf

            <div x-data="{ activeTab: 'profile' }" class="card shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link" :class="activeTab === 'profile' ? 'active' : ''" href="#" @click.prevent="activeTab = 'profile'">
                                <i class="bi bi-person"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" :class="activeTab === 'qualifications' ? 'active' : ''" href="#" @click.prevent="activeTab = 'qualifications'">
                                <i class="bi bi-mortarboard"></i> Qualifications
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div x-show="activeTab === 'profile'">
                        <h5 class="mb-3">Personal Information</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="designation" class="form-label">Designation</label>
                                <input type="text" name="designation" id="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}">
                                @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Gender</label>
                                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="">Select</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}">
                                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="joining_date" class="form-label">Joining Date</label>
                                <input type="date" name="joining_date" id="joining_date" class="form-control @error('joining_date') is-invalid @enderror" value="{{ old('joining_date') }}">
                                @error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div x-show="activeTab === 'qualifications'">
                        <h5 class="mb-3">Qualifications</h5>
                        <p class="text-muted small">Add academic qualifications (optional).</p>

                        <div id="qualifications-wrapper">
                            <div class="card mb-2 qualification-row">
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label small">Degree</label>
                                            <input type="text" name="qualifications[0][degree]" class="form-control form-control-sm" placeholder="e.g. MSc in CS">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">Institution</label>
                                            <input type="text" name="qualifications[0][institution]" class="form-control form-control-sm" placeholder="University name">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label small">Year</label>
                                            <input type="number" name="qualifications[0][year]" class="form-control form-control-sm" placeholder="2020">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label small">Grade</label>
                                            <input type="text" name="qualifications[0][grade]" class="form-control form-control-sm" placeholder="A / 3.5">
                                        </div>
                                        <div class="col-md-1 mb-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-qualification" style="display:none;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-qualification" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus"></i> Add Qualification
                        </button>
                    </div>
                </div>

                <div class="card-footer bg-white d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Create Teacher
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let qualIndex = 1;
        document.getElementById('add-qualification').addEventListener('click', function () {
            const wrapper = document.getElementById('qualifications-wrapper');
            const row = document.querySelector('.qualification-row').cloneNode(true);
            row.querySelectorAll('input').forEach(el => {
                const name = el.getAttribute('name').replace(/\d+/, qualIndex);
                el.setAttribute('name', name);
                el.value = '';
            });
            const btn = row.querySelector('.remove-qualification');
            btn.style.display = 'inline-block';
            btn.addEventListener('click', function () { row.remove(); });
            wrapper.appendChild(row);
            qualIndex++;
        });
    });
</script>
@endpush
