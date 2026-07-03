@extends('admin.layouts.master')

@section('title', 'Attendance')

@section('content')
    <div class="container-fluid py-4" x-data="attendance" role="main" aria-label="Attendance Management" :aria-busy="loading ? 'true' : 'false'">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h1 class="h3 mb-1">Attendance</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Attendance</li>
                    </ol>
                </nav>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Today</small>
                <strong class="fs-5">{{ now()->format('Y-m-d') }}</strong>
                <div class="mt-1">
                    <span class="badge bg-info">Session: --</span>
                </div>
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-outline-secondary" disabled title="Print">
                    <i class="bi bi-printer"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" disabled title="Refresh">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-funnel me-2"></i>Filters
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-xl-3 col-md-6 col-12">
                        <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <select id="academic_year_id" name="academic_year_id" class="form-select" autocomplete="off" x-model="academicYearId" x-ref="academicYearSelect">
                            <option value="">Select Academic Year</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                        <select id="semester_id" name="semester_id" class="form-select" autocomplete="off" x-model="semesterId" x-ref="semesterSelect">
                            <option value="">Select Semester</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                        <select id="department_id" name="department_id" class="form-select" autocomplete="off" x-model="departmentId" x-ref="departmentSelect">
                            <option value="">Select Department</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <label for="program_id" class="form-label">Program</label>
                        <select id="program_id" name="program_id" class="form-select" autocomplete="off" x-model="programId" x-ref="programSelect">
                            <option value="">Select Program</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <label for="shift_id" class="form-label">Shift <span class="text-danger">*</span></label>
                        <select id="shift_id" name="shift_id" class="form-select" autocomplete="off" x-model="shiftId" x-ref="shiftSelect">
                            <option value="">Select Shift</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <label for="group_id" class="form-label">Group</label>
                        <select id="group_id" name="group_id" class="form-select" autocomplete="off" x-model="groupId" x-ref="groupSelect">
                            <option value="">Select Group</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <label for="section_id" class="form-label">Section <span class="text-danger">*</span></label>
                        <select id="section_id" name="section_id" class="form-select" autocomplete="off" x-model="sectionId" x-ref="sectionSelect">
                            <option value="">Select Section</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                        <select id="subject_id" name="subject_id" class="form-select" autocomplete="off" x-model="subjectId" x-ref="subjectSelect">
                            <option value="">Select Subject</option>
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <label for="attendance_date" class="form-label">Attendance Date <span class="text-danger">*</span></label>
                        <input type="text" id="attendance_date" name="attendance_date" class="form-control" placeholder="YYYY-MM-DD" autocomplete="off" readonly
                            x-ref="dateInput"
                            x-init="$data.datePicker = flatpickr($el, { dateFormat: 'Y-m-d', maxDate: 'today', onChange: (selectedDates, dateStr) => { $data.date = dateStr || ''; } })">
                    </div>
                    <div class="col-xl-9 col-md-6 col-12 d-flex align-items-end justify-content-md-end gap-2">
                        <button type="button" class="btn btn-primary" x-on:click="loadStudents" :disabled="isLoading">
                            <i class="bi bi-people me-1"></i>
                            <span x-show="!loading">Load Students</span>
                            <span x-show="loading">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Loading...
                            </span>
                        </button>
                        <button type="button" class="btn btn-secondary" x-on:click="resetFilters">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl col-lg-3 col-md-4 col-6">
                <div class="card shadow-sm border-start border-primary border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-2 text-primary"></i>
                        <h2 class="mt-2 mb-0 fw-bold" x-text="summary.total">0</h2>
                        <small class="text-muted">Total</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-lg-3 col-md-4 col-6">
                <div class="card shadow-sm border-start border-success border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                        <h2 class="mt-2 mb-0 fw-bold text-success" x-text="summary.present">0</h2>
                        <small class="text-muted">Present</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-lg-3 col-md-4 col-6">
                <div class="card shadow-sm border-start border-danger border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-x-circle fs-2 text-danger"></i>
                        <h2 class="mt-2 mb-0 fw-bold text-danger" x-text="summary.absent">0</h2>
                        <small class="text-muted">Absent</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-lg-3 col-md-4 col-6">
                <div class="card shadow-sm border-start border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history fs-2 text-warning"></i>
                        <h2 class="mt-2 mb-0 fw-bold text-warning" x-text="summary.late">0</h2>
                        <small class="text-muted">Late</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-lg-3 col-md-4 col-6">
                <div class="card shadow-sm border-start border-info border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-door-open fs-2 text-info"></i>
                        <h2 class="mt-2 mb-0 fw-bold text-info" x-text="summary.leave">0</h2>
                        <small class="text-muted">Leave</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-lg-3 col-md-4 col-6">
                <div class="card shadow-sm border-start border-secondary border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-pie-chart fs-2 text-secondary"></i>
                        <h2 class="mt-2 mb-0 fw-bold text-secondary" x-text="summary.completionPercentage + '%'">0%</h2>
                        <small class="text-muted">Completion</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>Students
                        <span class="badge bg-secondary ms-1" x-text="students.length">0</span>
                    </h5>
                    <label for="attendance_search" class="visually-hidden">Search by Name, Roll or Student ID</label>
                    <input type="search" id="attendance_search" class="attendance-search-input form-control form-control-sm" placeholder="Search by Name, Roll or Student ID" autocomplete="off" x-model="searchQuery">
                </div>
                <div class="d-flex gap-1 flex-wrap">
                    <button type="button" class="btn btn-outline-success btn-sm" x-on:click="bulkUpdate('P')" :disabled="!hasStudents || saving">
                        <i class="bi bi-check-all"></i> All Present
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" x-on:click="bulkUpdate('A')" :disabled="!hasStudents || saving">
                        <i class="bi bi-x-lg"></i> All Absent
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm" x-on:click="bulkUpdate('L')" :disabled="!hasStudents || saving">
                        <i class="bi bi-clock-history"></i> All Late
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm" x-on:click="bulkUpdate('LV')" :disabled="!hasStudents || saving">
                        <i class="bi bi-door-open"></i> All Leave
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" x-on:click="clearAttendance" :disabled="!hasStudents || saving">
                        <i class="bi bi-eraser"></i> Clear
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover table-striped align-middle mb-0 attendance-table" aria-label="Attendance list">
                        <thead>
                            <tr>
                                <th class="sticky-roll">Roll</th>
                                <th class="text-center photo-col">Photo</th>
                                <th class="sticky-name">Student</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center">Late</th>
                                <th class="text-center">Leave</th>
                                <th>Remark</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="loading">
                                <tr>
                                    <td colspan="9" class="p-0">
                                        <div class="placeholder-glow px-3 py-2">
                                            <template x-for="i in 5" :key="i">
                                                <div class="d-flex align-items-center gap-2 border-bottom py-2" style="min-height:53px;">
                                                    <span class="placeholder col-1"></span>
                                                    <span class="placeholder rounded-circle flex-shrink-0" style="width:40px;height:40px;"></span>
                                                    <span class="placeholder col-2"></span>
                                                    <span class="placeholder col-1"></span>
                                                    <span class="placeholder col-1"></span>
                                                    <span class="placeholder col-1"></span>
                                                    <span class="placeholder col-1"></span>
                                                    <span class="placeholder col-2"></span>
                                                    <span class="placeholder col-1"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!loading && students.length === 0">
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-0">Select filters and click Load Students.</p>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!loading && students.length > 0 && filteredStudents.length === 0">
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                                        <p class="text-muted mb-0">No matching students found.</p>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="student in filteredStudents" :key="student.id">
                                <tr>
                                    <td class="sticky-roll">
                                        <strong x-text="student.roll_no"></strong>
                                    </td>
                                    <td class="text-center">
                                        <img :src="student.photo ? student.photo : `https://ui-avatars.com/api/?name=${encodeURIComponent(student.name)}&size=40&rounded=true&background=f0f0f0&color=333`" :alt="student.name" class="rounded-circle" width="40" height="40" loading="lazy">
                                    </td>
                                    <td class="sticky-name">
                                        <strong class="text-nowrap" x-text="student.name"></strong>
                                        <small class="d-block text-muted" x-text="`ID: ${student.id}`"></small>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block p-0 m-0">
                                            <input class="form-check-input position-static" type="radio" :name="`attendance_${student.id}`" :id="`present_${student.id}`" value="P" @change="onStatusChange(student, 'P')" :checked="student.status === 'P'" :disabled="isLoading">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block p-0 m-0">
                                            <input class="form-check-input position-static" type="radio" :name="`attendance_${student.id}`" :id="`absent_${student.id}`" value="A" @change="onStatusChange(student, 'A')" :checked="student.status === 'A'" :disabled="isLoading">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block p-0 m-0">
                                            <input class="form-check-input position-static" type="radio" :name="`attendance_${student.id}`" :id="`late_${student.id}`" value="L" @change="onStatusChange(student, 'L')" :checked="student.status === 'L'" :disabled="isLoading">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block p-0 m-0">
                                            <input class="form-check-input position-static" type="radio" :name="`attendance_${student.id}`" :id="`leave_${student.id}`" value="LV" @change="onStatusChange(student, 'LV')" :checked="student.status === 'LV'" :disabled="isLoading">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm remark-input" :name="`remark_${student.id}`" placeholder="Optional remark" autocomplete="off" x-model="student.remark" :disabled="isLoading">
                                    </td>
                                    <td class="text-center">
                                        <span x-show="getSaveStatusLabel(student) === 'Not Saved'" class="badge bg-secondary">
                                            <i class="bi bi-clock"></i> Not Saved
                                        </span>
                                        <span x-show="getSaveStatusLabel(student) === 'Saving...'" class="badge bg-info">
                                            <i class="bi bi-hourglass-split"></i> Saving...
                                        </span>
                                        <span x-show="getSaveStatusLabel(student) === 'Saved'" class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Saved
                                        </span>
                                        <span x-show="getSaveStatusLabel(student) === 'Error'" class="badge bg-danger">
                                            <i class="bi bi-exclamation-circle"></i> Error
                                        </span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <!-- Mobile Cards -->
                <div class="d-md-none">
                    <template x-if="loading">
                        <div class="placeholder-glow p-3">
                            <template x-for="i in 4" :key="i">
                                <div class="card border-bottom rounded-0 shadow-none mb-2">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <span class="placeholder rounded-circle flex-shrink-0" style="width:40px;height:40px;"></span>
                                            <span class="placeholder col-4"></span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <span class="placeholder col-1"></span>
                                            <span class="placeholder col-1"></span>
                                            <span class="placeholder col-1"></span>
                                            <span class="placeholder col-1"></span>
                                            <span class="placeholder col-3"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!loading && students.length === 0">
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                            <p class="text-muted mb-0">Select filters and click Load Students.</p>
                        </div>
                    </template>
                    <template x-if="!loading && students.length > 0 && filteredStudents.length === 0">
                        <div class="text-center py-5">
                            <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                            <p class="text-muted mb-0">No matching students found.</p>
                        </div>
                    </template>
                    <template x-for="student in filteredStudents" :key="student.id">
                        <div class="card border-bottom rounded-0 shadow-none attendance-card">
                            <div class="card-body py-3">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <img :src="student.photo ? student.photo : `https://ui-avatars.com/api/?name=${encodeURIComponent(student.name)}&size=40&rounded=true&background=f0f0f0&color=333`" :alt="student.name" class="rounded-circle flex-shrink-0" width="40" height="40" loading="lazy">
                                    <div class="flex-grow-1 min-w-0">
                                        <strong class="text-truncate d-block" x-text="student.name"></strong>
                                        <small class="text-muted">
                                            <span x-text="`Roll: ${student.roll_no}`"></span>
                                            <span class="ms-2" x-text="`ID: ${student.id}`"></span>
                                        </small>
                                    </div>
                                    <span x-show="getSaveStatusLabel(student) === 'Not Saved'" class="badge bg-secondary flex-shrink-0">
                                        <i class="bi bi-clock"></i>
                                    </span>
                                    <span x-show="getSaveStatusLabel(student) === 'Saving...'" class="badge bg-info flex-shrink-0">
                                        <i class="bi bi-hourglass-split"></i>
                                    </span>
                                    <span x-show="getSaveStatusLabel(student) === 'Saved'" class="badge bg-success flex-shrink-0">
                                        <i class="bi bi-check-circle"></i>
                                    </span>
                                    <span x-show="getSaveStatusLabel(student) === 'Error'" class="badge bg-danger flex-shrink-0">
                                        <i class="bi bi-exclamation-circle"></i>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-medium me-1">Status:</span>
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" :name="`mob_${student.id}`" :id="`mob_present_${student.id}`" value="P" @change="onStatusChange(student, 'P')" :checked="student.status === 'P'" :disabled="isLoading">
                                        <label class="form-check-label" :for="`mob_present_${student.id}`">P</label>
                                    </div>
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" :name="`mob_${student.id}`" :id="`mob_absent_${student.id}`" value="A" @change="onStatusChange(student, 'A')" :checked="student.status === 'A'" :disabled="isLoading">
                                        <label class="form-check-label" :for="`mob_absent_${student.id}`">A</label>
                                    </div>
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" :name="`mob_${student.id}`" :id="`mob_late_${student.id}`" value="L" @change="onStatusChange(student, 'L')" :checked="student.status === 'L'" :disabled="isLoading">
                                        <label class="form-check-label" :for="`mob_late_${student.id}`">L</label>
                                    </div>
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" :name="`mob_${student.id}`" :id="`mob_leave_${student.id}`" value="LV" @change="onStatusChange(student, 'LV')" :checked="student.status === 'LV'" :disabled="isLoading">
                                        <label class="form-check-label" :for="`mob_leave_${student.id}`">LV</label>
                                    </div>
                                    <input type="text" class="form-control form-control-sm ms-auto" style="max-width:150px;" placeholder="Remark" autocomplete="off" x-model="student.remark" :disabled="isLoading">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
@endsection
