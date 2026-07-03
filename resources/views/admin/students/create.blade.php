@extends('admin.layouts.master')

@section('title', 'Admit Student')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Admit New Student</h1>

        <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div x-data="{ step: 1 }" class="card shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-pills nav-fill">
                        <li class="nav-item">
                            <span class="nav-link" :class="step >= 1 ? 'active' : ''">
                                <i class="bi bi-person"></i> Student Info
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link" :class="step >= 2 ? 'active' : ''">
                                <i class="bi bi-shield"></i> Guardian
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link" :class="step >= 3 ? 'active' : ''">
                                <i class="bi bi-files"></i> Documents
                            </span>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link" :class="step >= 4 ? 'active' : ''">
                                <i class="bi bi-check-lg"></i> Review
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div x-show="step === 1">
                        <h5 class="mb-3">Student Information</h5>

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
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}" required>
                                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Gender</label>
                                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">Select</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="blood_group" class="form-label">Blood Group</label>
                                <select name="blood_group" id="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                    <option value="">Select</option>
                                    @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                                @error('blood_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="roll_no" class="form-label">Roll No</label>
                                <input type="text" name="roll_no" id="roll_no" class="form-control @error('roll_no') is-invalid @enderror" value="{{ old('roll_no') }}">
                                @error('roll_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo</label>
                            <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png">
                            @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr>
                        <h6 class="mb-3">Academic Placement</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="academic_year_id" class="form-label">Academic Year</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">Select</option>
                                    @foreach ($academicYears as $ay)
                                        <option value="{{ $ay->id }}" {{ old('academic_year_id') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="program_id" class="form-label">Program</label>
                                <select name="program_id" id="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                                    <option value="">Select</option>
                                    @foreach ($programs as $p)
                                        <option value="{{ $p->id }}" {{ old('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="section_id" class="form-label">Section</label>
                                <select name="section_id" id="section_id" class="form-select @error('section_id') is-invalid @enderror" required>
                                    <option value="">Select</option>
                                    @foreach ($sections as $s)
                                        <option value="{{ $s->id }}" {{ old('section_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
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
                                        <option value="{{ $sh->id }}" {{ old('shift_id') == $sh->id ? 'selected' : '' }}>{{ $sh->name }}</option>
                                    @endforeach
                                </select>
                                @error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="group_id" class="form-label">Group</label>
                                <select name="group_id" id="group_id" class="form-select @error('group_id') is-invalid @enderror">
                                    <option value="">Select</option>
                                    @foreach ($groups as $g)
                                        <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                                    @endforeach
                                </select>
                                @error('group_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div x-show="step === 2">
                        <h5 class="mb-3">Guardian Information</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="guardian_name" class="form-label">Guardian Name</label>
                                <input type="text" name="guardian[name]" id="guardian_name" class="form-control @error('guardian.name') is-invalid @enderror" value="{{ old('guardian.name') }}" required>
                                @error('guardian.name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="guardian_relation" class="form-label">Relation</label>
                                <select name="guardian[relation]" id="guardian_relation" class="form-select @error('guardian.relation') is-invalid @enderror" required>
                                    <option value="">Select</option>
                                    <option value="father" {{ old('guardian.relation') == 'father' ? 'selected' : '' }}>Father</option>
                                    <option value="mother" {{ old('guardian.relation') == 'mother' ? 'selected' : '' }}>Mother</option>
                                    <option value="guardian" {{ old('guardian.relation') == 'guardian' ? 'selected' : '' }}>Guardian</option>
                                </select>
                                @error('guardian.relation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="guardian_phone" class="form-label">Phone</label>
                                <input type="text" name="guardian[phone]" id="guardian_phone" class="form-control @error('guardian.phone') is-invalid @enderror" value="{{ old('guardian.phone') }}" required>
                                @error('guardian.phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="guardian_email" class="form-label">Email</label>
                                <input type="email" name="guardian[email]" id="guardian_email" class="form-control @error('guardian.email') is-invalid @enderror" value="{{ old('guardian.email') }}">
                                @error('guardian.email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="guardian_occupation" class="form-label">Occupation</label>
                                <input type="text" name="guardian[occupation]" id="guardian_occupation" class="form-control @error('guardian.occupation') is-invalid @enderror" value="{{ old('guardian.occupation') }}">
                                @error('guardian.occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="guardian_address" class="form-label">Address</label>
                                <textarea name="guardian[address]" id="guardian_address" rows="2" class="form-control @error('guardian.address') is-invalid @enderror">{{ old('guardian.address') }}</textarea>
                                @error('guardian.address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div x-show="step === 3">
                        <h5 class="mb-3">Documents (Optional)</h5>
                        <p class="text-muted small">Upload supporting documents (birth certificate, transcripts, etc.)</p>

                        <div id="documents-wrapper">
                            <div class="row mb-2 document-row">
                                <div class="col-md-4">
                                    <select name="documents[0][document_type]" class="form-select form-select-sm">
                                        <option value="birth_certificate">Birth Certificate</option>
                                        <option value="transcript">Transcript</option>
                                        <option value="photo_id">Photo ID</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="file" name="documents[0][file]" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-document" style="display:none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-document" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus"></i> Add Document
                        </button>
                    </div>

                    <div x-show="step === 4">
                        <h5 class="mb-3">Review & Submit</h5>
                        <p class="text-muted">Please review the information before submitting.</p>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> The admission number will be auto-generated upon submission.
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" x-show="step > 1" @click="step--">
                        <i class="bi bi-arrow-left"></i> Previous
                    </button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="button" class="btn btn-primary" x-show="step < 4" @click="step++">
                        Next <i class="bi bi-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-success" x-show="step === 4">
                        <i class="bi bi-check-lg"></i> Submit Admission
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let docIndex = 1;
        document.getElementById('add-document').addEventListener('click', function () {
            const wrapper = document.getElementById('documents-wrapper');
            const row = document.querySelector('.document-row').cloneNode(true);
            row.querySelectorAll('select, input').forEach(el => {
                const name = el.getAttribute('name').replace(/\d+/, docIndex);
                el.setAttribute('name', name);
                el.value = '';
            });
            row.querySelector('.remove-document').style.display = 'inline-block';
            row.querySelector('.remove-document').addEventListener('click', function () {
                row.remove();
            });
            wrapper.appendChild(row);
            docIndex++;
        });
    });
</script>
@endpush
