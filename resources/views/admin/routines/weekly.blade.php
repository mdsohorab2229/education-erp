@extends('admin.layouts.master')

@section('title', 'Weekly Routine')

@section('content')
<style>
    .routine-grid th, .routine-grid td { vertical-align: top; }
    .routine-cell { min-height: 80px; transition: background-color 0.2s; }
    .routine-cell:hover { filter: brightness(0.95); }
    .routine-cell.bg-subject-1 { background-color: #e3f2fd; }
    .routine-cell.bg-subject-2 { background-color: #e8f5e9; }
    .routine-cell.bg-subject-3 { background-color: #fff3e0; }
    .routine-cell.bg-subject-4 { background-color: #fce4ec; }
    .routine-cell.bg-subject-5 { background-color: #f3e5f5; }
    .routine-cell.bg-subject-6 { background-color: #e0f7fa; }
    .routine-cell.bg-subject-7 { background-color: #fff8e1; }
    .routine-cell.bg-subject-8 { background-color: #efebe9; }
    .routine-cell.bg-subject-9 { background-color: #e8eaf6; }
    .routine-cell.bg-subject-0 { background-color: #fbe9e7; }
    @media print {
        .d-print-none { display: none !important; }
        .navbar, .text-bg-dark, footer, .btn { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6; }
        .routine-grid { font-size: 10px; }
        .routine-cell { min-height: 60px; }
        .routine-cell small { font-size: 9px; }
        .table-dark th { background-color: #212529 !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .routine-cell.bg-subject-1, .routine-cell.bg-subject-2, .routine-cell.bg-subject-3,
        .routine-cell.bg-subject-4, .routine-cell.bg-subject-5, .routine-cell.bg-subject-6,
        .routine-cell.bg-subject-7, .routine-cell.bg-subject-8, .routine-cell.bg-subject-9,
        .routine-cell.bg-subject-0 { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .container-fluid { padding: 0 !important; }
    }
</style>

<div class="container-fluid py-4" x-data="weeklyRoutine">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">Weekly Routine</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Weekly Routine</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-outline-secondary" x-on:click="printRoutine" title="Print">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <div class="card shadow-sm mb-4 d-print-none">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filters
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                    <select id="academic_year_id" class="form-select" x-model="filters.academic_year_id" x-ref="academicYearSelect">
                        <option value="">Select Academic Year</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                    <select id="semester_id" class="form-select" x-model="filters.semester_id" x-ref="semesterSelect">
                        <option value="">Select Semester</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                    <select id="department_id" class="form-select" x-model="filters.department_id" x-ref="departmentSelect">
                        <option value="">Select Department</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="program_id" class="form-label">Program</label>
                    <select id="program_id" class="form-select" x-model="filters.program_id" x-ref="programSelect">
                        <option value="">Select Program</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="shift_id" class="form-label">Shift <span class="text-danger">*</span></label>
                    <select id="shift_id" class="form-select" x-model="filters.shift_id" x-ref="shiftSelect">
                        <option value="">Select Shift</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="section_id" class="form-label">Section <span class="text-danger">*</span></label>
                    <select id="section_id" class="form-select" x-model="filters.section_id" x-ref="sectionSelect">
                        <option value="">Select Section</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <label for="group_id" class="form-label">Group</label>
                    <select id="group_id" class="form-select" x-model="filters.group_id" x-ref="groupSelect">
                        <option value="">Select Group</option>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6 col-12 d-flex align-items-end gap-2">
                    <button type="button" class="btn btn-primary" x-on:click="loadRoutine" :disabled="loading">
                        <i class="bi bi-search me-1"></i>
                        <span x-show="!loading">Load Routine</span>
                        <span x-show="loading">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
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

    <template x-if="routineData.length === 0 && !loading">
        <div class="text-center py-5">
            <i class="bi bi-calendar-week fs-1 text-muted d-block mb-2"></i>
            <p class="text-muted mb-0">Select filters and click Load Routine to view the weekly schedule.</p>
        </div>
    </template>

    <template x-if="loading">
        <div class="placeholder-glow">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <template x-for="i in 3" :key="i">
                        <div class="d-flex gap-3 mb-3">
                            <span class="placeholder col-1"></span>
                            <span class="placeholder col-2"></span>
                            <span class="placeholder col-2"></span>
                            <span class="placeholder col-2"></span>
                            <span class="placeholder col-2"></span>
                            <span class="placeholder col-2"></span>
                            <span class="placeholder col-1"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <template x-if="routineData.length > 0 && !loading">
        <div class="card shadow-sm" id="routine-print-area">
            <div class="card-header bg-white py-3 d-print-none">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar-week me-2"></i>
                    <span x-text="`${filters.section_name || 'Section'} - Weekly Routine`"></span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0 routine-grid">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="min-width: 80px;">Time</th>
                                <template x-for="day in days" :key="day">
                                    <th class="text-center text-capitalize" x-text="day"></th>
                                </template>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(slot, index) in timeSlots" :key="index">
                                <tr>
                                    <td class="text-center fw-medium text-nowrap" x-text="slot.label"></td>
                                    <template x-for="day in days" :key="day">
                                        <td class="p-1" style="min-width: 150px; height: 100px;">
                                            <template x-if="getRoutine(day, slot.start)">
                                                <div class="routine-cell h-100 d-flex flex-column justify-content-center p-1 rounded" :class="getSubjectColor(getRoutine(day, slot.start).subject)">
                                                    <small class="fw-bold text-truncate" x-text="getRoutine(day, slot.start).subject?.name || '-'"></small>
                                                    <small class="text-truncate">
                                                        <i class="bi bi-person"></i> <span x-text="getRoutine(day, slot.start).teacher?.name || '-'"></span>
                                                    </small>
                                                    <small class="text-truncate">
                                                        <i class="bi bi-door-open"></i> <span x-text="getRoutine(day, slot.start).room?.name || '-'"></span>
                                                    </small>
                                                </div>
                                            </template>
                                            <template x-if="!getRoutine(day, slot.start)">
                                                <div class="h-100 d-flex align-items-center justify-content-center">
                                                    <small class="text-muted">—</small>
                                                </div>
                                            </template>
                                        </td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('weeklyRoutine', () => ({
            loading: false,
            routineData: [],
            days: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            timeSlots: [],
            filters: {
                academic_year_id: '',
                semester_id: '',
                department_id: '',
                program_id: '',
                shift_id: '',
                section_id: '',
                group_id: '',
                section_name: '',
            },
            subjectColors: {},

            init() {
                this.initSelects();
            },

            initSelects() {
                const map = {
                    academic_year_id: 'academicYearSelect',
                    semester_id: 'semesterSelect',
                    department_id: 'departmentSelect',
                    program_id: 'programSelect',
                    shift_id: 'shiftSelect',
                    section_id: 'sectionSelect',
                    group_id: 'groupSelect',
                };

                Object.entries(map).forEach(([key, refName]) => {
                    const select = this.$refs[refName];
                    if (!select) return;
                    const url = key === 'academic_year_id' ? '/api/academic-years'
                        : key === 'semester_id' ? '/api/semesters'
                        : key === 'department_id' ? '/api/departments'
                        : key === 'program_id' ? '/api/programs'
                        : key === 'shift_id' ? '/api/shifts'
                        : key === 'section_id' ? '/api/sections'
                        : '/api/groups';
                    fetch(url)
                        .then(r => r.json())
                        .then(data => {
                            const items = Array.isArray(data) ? data : (data.data || []);
                            items.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item.id;
                                opt.textContent = item.name;
                                select.appendChild(opt);
                            });
                        });
                });
            },

            loadRoutine() {
                const required = ['academic_year_id', 'semester_id', 'department_id', 'shift_id', 'section_id'];
                const missing = required.filter(k => !this.filters[k]);
                if (missing.length) {
                    alert('Please fill all required fields.');
                    return;
                }

                this.loading = true;
                const params = new URLSearchParams();
                Object.entries(this.filters).forEach(([k, v]) => { if (v) params.append(k, v); });

                fetch(`/admin/routines/weekly?${params}`)
                    .then(r => r.json())
                    .then(res => {
                        this.routineData = res.data || [];
                        this.buildTimeSlots();
                        this.assignSubjectColors();
                        if (this.routineData.length > 0 && this.routineData[0].section) {
                            this.filters.section_name = this.routineData[0].section.name;
                        }
                    })
                    .catch(() => alert('Failed to load routine.'))
                    .finally(() => { this.loading = false; });
            },

            buildTimeSlots() {
                const times = new Set();
                this.routineData.forEach(r => {
                    if (r.start_time && r.end_time) {
                        times.add(`${r.start_time}-${r.end_time}`);
                    }
                });
                this.timeSlots = Array.from(times).sort().map(t => {
                    const [start, end] = t.split('-');
                    return { label: `${start} - ${end}`, start, end };
                });
            },

            assignSubjectColors() {
                const ids = [...new Set(this.routineData.map(r => r.subject?.id).filter(Boolean))];
                ids.forEach((id, i) => { this.subjectColors[id] = i % 10; });
            },

            getRoutine(day, startTime) {
                return this.routineData.find(r =>
                    r.day === day && r.start_time === startTime
                );
            },

            getSubjectColor(subject) {
                if (!subject) return '';
                const idx = this.subjectColors[subject.id];
                return idx !== undefined ? `bg-subject-${idx}` : 'bg-subject-0';
            },

            resetFilters() {
                Object.keys(this.filters).forEach(k => { this.filters[k] = ''; });
                this.routineData = [];
                this.timeSlots = [];
            },

            printRoutine() {
                window.print();
            },
        }));
    });
</script>
@endsection
