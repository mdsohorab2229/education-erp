@extends('admin.layouts.master')

@section('title', 'Edit Teacher')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Edit Teacher: {{ $teacher->full_name }}</h1>

        <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card shadow-sm mb-3">
                <div class="card-header"><h5 class="mb-0">Personal Information</h5></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $teacher->first_name) }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $teacher->last_name) }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $teacher->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $teacher->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="designation" class="form-label">Designation</label>
                            <input type="text" name="designation" id="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation', $teacher->designation) }}">
                            @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="gender" class="form-label">Gender</label>
                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select</option>
                                <option value="male" {{ old('gender', $teacher->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $teacher->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $teacher->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $teacher->date_of_birth?->format('Y-m-d')) }}">
                            @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="joining_date" class="form-label">Joining Date</label>
                            <input type="date" name="joining_date" id="joining_date" class="form-control @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', $teacher->joining_date?->format('Y-m-d')) }}">
                            @error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $teacher->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="active" {{ old('status', $teacher->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $teacher->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status', $teacher->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header"><h5 class="mb-0">Qualifications</h5></div>
                <div class="card-body">
                    <div id="qualifications-wrapper">
                        @forelse ($teacher->qualifications as $qIndex => $qual)
                            <div class="card mb-2 qualification-row">
                                <div class="card-body py-2">
                                    <input type="hidden" name="qualifications[{{ $qIndex }}][id]" value="{{ $qual->id }}">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label small">Degree</label>
                                            <input type="text" name="qualifications[{{ $qIndex }}][degree]" class="form-control form-control-sm" value="{{ $qual->degree }}">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">Institution</label>
                                            <input type="text" name="qualifications[{{ $qIndex }}][institution]" class="form-control form-control-sm" value="{{ $qual->institution }}">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label small">Year</label>
                                            <input type="number" name="qualifications[{{ $qIndex }}][year]" class="form-control form-control-sm" value="{{ $qual->year }}">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label small">Grade</label>
                                            <input type="text" name="qualifications[{{ $qIndex }}][grade]" class="form-control form-control-sm" value="{{ $qual->grade }}">
                                        </div>
                                        <div class="col-md-1 mb-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-qualification">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
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
                        @endforelse
                    </div>
                    <button type="button" id="add-qualification" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus"></i> Add Qualification
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Teacher</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let qualIndex = document.querySelectorAll('.qualification-row').length;
        document.getElementById('add-qualification').addEventListener('click', function () {
            const wrapper = document.getElementById('qualifications-wrapper');
            const row = document.querySelector('.qualification-row').cloneNode(true);
            row.querySelectorAll('input').forEach(el => {
                const name = el.getAttribute('name').replace(/\d+/, qualIndex);
                el.setAttribute('name', name);
                if (!name.includes('[id]')) { el.value = ''; }
            });
            const btn = row.querySelector('.remove-qualification');
            btn.style.display = 'inline-block';
            btn.addEventListener('click', function () { row.remove(); });
            wrapper.appendChild(row);
            qualIndex++;
        });
        document.querySelectorAll('.remove-qualification').forEach(btn => {
            btn.addEventListener('click', function () { btn.closest('.qualification-row').remove(); });
        });
    });
</script>
@endpush
