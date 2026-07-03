<?php
declare(strict_types=1);

namespace App\Providers;

use App\Interfaces\Repositories\AcademicYearRepositoryInterface;
use App\Interfaces\Repositories\AttendanceRecordRepositoryInterface;
use App\Interfaces\Repositories\AttendanceSessionRepositoryInterface;
use App\Interfaces\Repositories\DepartmentRepositoryInterface;
use App\Interfaces\Repositories\GroupRepositoryInterface;
use App\Interfaces\Repositories\GuardianRepositoryInterface;
use App\Interfaces\Repositories\PermissionRepositoryInterface;
use App\Interfaces\Repositories\ProgramRepositoryInterface;
use App\Interfaces\Repositories\RoleRepositoryInterface;
use App\Interfaces\Repositories\RoutineRepositoryInterface;
use App\Interfaces\Repositories\SectionRepositoryInterface;
use App\Interfaces\Repositories\ShiftRepositoryInterface;
use App\Interfaces\Repositories\StudentDocumentRepositoryInterface;
use App\Interfaces\Repositories\StudentRepositoryInterface;
use App\Interfaces\Repositories\SubjectRepositoryInterface;
use App\Interfaces\Repositories\TeacherRepositoryInterface;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Group;
use App\Models\Program;
use App\Models\Section;
use App\Models\Shift;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Policies\AcademicYearPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\GroupPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\ProgramPolicy;
use App\Policies\RolePolicy;
use App\Policies\SectionPolicy;
use App\Policies\ShiftPolicy;
use App\Policies\StudentPolicy;
use App\Policies\SubjectPolicy;
use App\Policies\TeacherPolicy;
use App\Repositories\AcademicYearRepository;
use App\Repositories\AttendanceRecordRepository;
use App\Repositories\AttendanceSessionRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\GroupRepository;
use App\Repositories\GuardianRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\ProgramRepository;
use App\Repositories\RoleRepository;
use App\Repositories\RoutineRepository;
use App\Repositories\SectionRepository;
use App\Repositories\ShiftRepository;
use App\Repositories\StudentDocumentRepository;
use App\Repositories\StudentRepository;
use App\Repositories\SubjectRepository;
use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(AttendanceSessionRepositoryInterface::class, AttendanceSessionRepository::class);
        $this->app->bind(AttendanceRecordRepositoryInterface::class, AttendanceRecordRepository::class);
        $this->app->bind(AcademicYearRepositoryInterface::class, AcademicYearRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(ProgramRepositoryInterface::class, ProgramRepository::class);
        $this->app->bind(SectionRepositoryInterface::class, SectionRepository::class);
        $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
        $this->app->bind(ShiftRepositoryInterface::class, ShiftRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(GuardianRepositoryInterface::class, GuardianRepository::class);
        $this->app->bind(StudentDocumentRepositoryInterface::class, StudentDocumentRepository::class);
        $this->app->bind(TeacherRepositoryInterface::class, TeacherRepository::class);
        $this->app->bind(RoutineRepositoryInterface::class, RoutineRepository::class);
    }

    public function boot(): void
    {
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(AcademicYear::class, AcademicYearPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Program::class, ProgramPolicy::class);
        Gate::policy(Section::class, SectionPolicy::class);
        Gate::policy(Subject::class, SubjectPolicy::class);
        Gate::policy(Shift::class, ShiftPolicy::class);
        Gate::policy(Group::class, GroupPolicy::class);
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Teacher::class, TeacherPolicy::class);
    }
}
