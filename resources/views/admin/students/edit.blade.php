@extends('admin.layouts.master')

@section('title', 'Edit Student')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Edit Student: {{ $student->full_name }}</h1>

        <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card shadow-sm mb-3">
                <div class="card-header"><h5 class="mb-0">Student Information</h5></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $student->first_name) }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $student->last_name) }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}" required>
                            @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="gender" class="form-label">Gender</label>
                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">Select</option>
                                <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="blood_group" class="form-label">Blood Group</label>
                            <select name="blood_group" id="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                <option value="">Select</option>
                                @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                            @error('blood_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="roll_no" class="form-label">Roll No</label>
                            <input type="text" name="roll_no" id="roll_no" class="form-control @error('roll_no') is-invalid @enderror" value="{{ old('roll_no', $student->roll_no) }}">
                            @error('roll_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $student->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $student->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $student->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo</label>
                        <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png">
                        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if ($student->photo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="Student photo" class="img-thumbnail" width="100">
                            </div>
                        @endif
                    </div>

                    <hr>
                    <h6 class="mb-3">Academic Placement</h6>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="academic_year_id" class="form-label">Academic Year</label>
                            <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                <option value="">Select</option>
                                @foreach ($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ old('academic_year_id', $student->academic_year_id) == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                                @endforeach
                            </select>
                            @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="program_id" class="form-label">Program</label>
                            <select name="program_id" id="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                                <option value="">Select</option>
                                @foreach ($programs as $p)
                                    <option value="{{ $p->id }}" {{ old('program_id', $student->program_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="section_id" class="form-label">Section</label>
                            <select name="section_id" id="section_id" class="form-select @error('section_id') is-invalid @enderror" required>
                                <option value="">Select</option>
                                @foreach ($sections as $s)
                                    <option value="{{ $s->id }}" {{ old('section_id', $student->section_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('section_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="shift_id" class="form-label">Shift</label>
                            <select name="shift_id" id="shift_id" class="form-select @error('shift_id') is-invalid @enderror" required>
                                <option value="">Select</option>
                                @foreach ($shifts as $sh)
                                    <option value="{{ $sh->id }}" {{ old('shift_id', $student->shift_id) == $sh->id ? 'selected' : '' }}>{{ $sh->name }}</option>
                                @endforeach
                            </select>
                            @error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="group_id" class="form-label">Group</label>
                            <select name="group_id" id="group_id" class="form-select @error('group_id') is-invalid @enderror">
                                <option value="">Select</option>
                                @foreach ($groups as $g)
                                    <option value="{{ $g->id }}" {{ old('group_id', $student->group_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                                @endforeach
                            </select>
                            @error('group_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header"><h5 class="mb-0">Guardian Information</h5></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="guardian_name" class="form-label">Guardian Name</label>
                            <input type="text" name="guardian[name]" id="guardian_name" class="form-control @error('guardian.name') is-invalid @enderror" value="{{ old('guardian.name', $student->guardian->name ?? '') }}" required>
                            @error('guardian.name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="guardian_relation" class="form-label">Relation</label>
                            <select name="guardian[relation]" id="guardian_relation" class="form-select @error('guardian.relation') is-invalid @enderror" required>
                                <option value="">Select</option>
                                <option value="father" {{ old('guardian.relation', $student->guardian->relation ?? '') == 'father' ? 'selected' : '' }}>Father</option>
                                <option value="mother" {{ old('guardian.relation', $student->guardian->relation ?? '') == 'mother' ? 'selected' : '' }}>Mother</option>
                                <option value="guardian" {{ old('guardian.relation', $student->guardian->relation ?? '') == 'guardian' ? 'selected' : '' }}>Guardian</option>
                            </select>
                            @error('guardian.relation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="guardian_phone" class="form-label">Phone</label>
                            <input type="text" name="guardian[phone]" id="guardian_phone" class="form-control @error('guardian.phone') is-invalid @enderror" value="{{ old('guardian.phone', $student->guardian->phone ?? '') }}" required>
                            @error('guardian.phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="guardian_email" class="form-label">Email</label>
                            <input type="email" name="guardian[email]" id="guardian_email" class="form-control @error('guardian.email') is-invalid @enderror" value="{{ old('guardian.email', $student->guardian->email ?? '') }}">
                            @error('guardian.email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="guardian_occupation" class="form-label">Occupation</label>
                            <input type="text" name="guardian[occupation]" id="guardian_occupation" class="form-control @error('guardian.occupation') is-invalid @enderror" value="{{ old('guardian.occupation', $student->guardian->occupation ?? '') }}">
                            @error('guardian.occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="guardian_address" class="form-label">Address</label>
                            <textarea name="guardian[address]" id="guardian_address" rows="2" class="form-control @error('guardian.address') is-invalid @enderror">{{ old('guardian.address', $student->guardian->address ?? '') }}</textarea>
                            @error('guardian.address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Student</button>
            </div>
        </form>
    </div>
@endsection
