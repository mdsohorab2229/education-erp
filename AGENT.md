# AGENTS.md

# Production-Grade Laravel ERP Development Standards

Version: 2.0

---

# Project Stack

- Framework: Laravel 12+
- PHP: 8.3+
- Database: MySQL 8+
- Authentication: Laravel Breeze
- Roles & Permissions: spatie/laravel-permission
- Frontend:
    - Blade
    - Bootstrap 5
    - Alpine.js
    - Axios
    - SweetAlert2
- Debugging:
    - Laravel Debugbar
- Coding Standard:
    - PSR-12
    - SOLID
    - DRY
    - KISS

---

# Core Architecture Principles

This project strictly follows a layered architecture.

```
Controller
      ↓
Service
      ↓
Repository Interface
      ↓
Repository Implementation
      ↓
Eloquent Model
      ↓
Database
```

Business logic must never bypass these layers.

---

# 1. Controller Rules

Controllers are responsible only for HTTP communication.

Controllers MUST:

- Receive HTTP Requests
- Call exactly one Service per execution flow
- Return Views, JSON Resources, Redirects, or API Responses
- Use Form Request validation
- Handle HTTP response formatting only

Controllers MUST NEVER:

- Contain business logic
- Call Eloquent Models directly
- Execute database queries
- Use DB facade
- Use DB::transaction()
- Implement validation logic manually
- Perform calculations
- Access repositories directly

Controllers should remain thin.

---

# 2. Service Layer Rules

Services contain ALL business rules.

Services MUST:

- Implement complete business logic
- Depend only on Repository Interfaces
- Coordinate multiple repositories
- Handle database transactions
- Throw domain-specific exceptions
- Return DTOs, Collections, Models, or Resources as appropriate

Services MUST NEVER:

- Access Request objects
- Use request()
- Depend on Controllers
- Use Eloquent Models directly
- Perform raw SQL queries

Always pass primitive values or DTOs into Services.

---

# 3. Repository Rules

Repositories are the ONLY layer allowed to interact with the database.

Repositories MUST:

- Execute all database queries
- Handle Eloquent operations
- Handle Query Builder operations
- Apply eager loading
- Implement pagination
- Return Models or Collections

Repositories MUST NEVER:

- Implement business logic
- Perform authorization
- Validate requests

---

# 4. Model Rules

Models should only represent database entities.

Models SHOULD contain:

- Relationships
- Scopes
- Accessors
- Mutators
- Casts
- Fillable attributes

Models SHOULD NOT contain:

- Business logic
- Workflow logic
- Validation logic

---

# 5. Database Standards

Every table should follow these standards.

Required:

- id (bigIncrements)
- timestamps()
- Foreign key constraints
- Proper indexing
- Soft Deletes where applicable

Audit Columns (when required):

- created_by
- updated_by

Always:

- Use foreign keys
- Add indexes on searchable columns
- Prevent orphan records

---

# 6. Form Request Rules

Validation belongs ONLY inside Form Request classes.

Each CRUD module must contain:

- StoreRequest
- UpdateRequest

Never validate inside Controllers.

---

# 7. Resource Rules

API responses must use Laravel Resources.

Never return raw Eloquent Models directly for API endpoints.

---

# 8. Dependency Injection Rules

Always use constructor dependency injection.

Preferred:

```
public function __construct(
    StudentService $service
)
```

Avoid:

- Static calls
- Service Locator
- Unnecessary Facades

---

# 9. Database Transaction Rules

Transactions belong ONLY inside Services.

Use transactions whenever:

- Multiple repositories are involved
- Multiple writes occur
- Data consistency is required

Never create transactions inside:

- Controllers
- Repositories

---

# 10. Performance Standards

Always:

- Use eager loading
- Prevent N+1 queries
- Paginate listing pages
- Update only dirty attributes
- Select only required columns
- Cache expensive operations when appropriate

Never:

- Use Model::all() on large datasets
- Load unnecessary relationships
- Execute queries inside loops

---

# 11. Coding Standards

Every new PHP file MUST include:

```php
declare(strict_types=1);
```

Follow:

- PSR-12
- SOLID
- DRY
- KISS

Use:

- Constructor Injection
- Interface-driven architecture
- Small reusable methods

---

# 12. Error Handling

Never silently fail.

Use:

- Domain Exceptions
- Custom Exceptions
- Validation Exceptions

Return meaningful error messages.

---

# 13. Testing Standards

Every module MUST include:

## Feature Tests

- Authentication
- Authorization
- Validation
- CRUD Operations

## Factories

- Model Factory

## Seeders

- Database Seeder

---

# 14. Frontend Standards

Frontend Stack

- Bootstrap 5
- Blade Components
- Alpine.js
- Axios
- SweetAlert2

Rules:

- Never reload the page after AJAX unless required
- Use reusable Blade Components
- Keep JavaScript modular
- Use Axios for AJAX
- Display success/error alerts using SweetAlert2

---

# 15. Security Standards

Always:

- Authorize actions using Policies or Permissions
- Escape output
- Validate all input
- Protect against mass assignment
- Use CSRF protection
- Use Route Model Binding where appropriate

Never trust client-side validation.

---

# 16. Module Development Order

Every module MUST be generated in this exact order.

1. Migration
2. Model
3. Repository Interface
4. Repository Implementation
5. Service
6. Form Requests
7. Resource
8. Controller
9. Routes
10. Blade Views
11. Factory
12. Seeder
13. Feature Tests

Do not skip steps.

---

# 17. AI Execution Rules (Critical)

The AI assistant MUST:

- Generate ONLY requested files
- Never modify unrelated files
- Never rename existing classes
- Never remove existing functionality
- Never refactor outside requested scope
- Stop immediately after requested module is complete

If a dependency is missing, explain it before generating code.

---

# 18. Completion Report

After every task, provide a Markdown summary.

Example:


## Files Created

- Migration
- Model
- Repository Interface
- Repository
- Service
- StoreRequest
- UpdateRequest
- Resource
- Controller
- Routes
- Views
- Factory
- Seeder
- Feature Test

## Seeder Rules

Every module must include:

- Model Factory
- Module Seeder
- DatabaseSeeder registration

Seeder should:

- Generate realistic relational data
- Respect foreign key constraints
- Be safe to run multiple times where practical
- Never disable foreign key checks unnecessarily
- Never truncate unrelated tables
- Use factories for data generation
- Not create invalid or orphan records

## Files Modified

- routes/web.php

## Notes

- Business rules implemented
- Validation completed
- Repository pattern followed
- Feature tests added

---

# 19. Non-Negotiable Rules

The following rules are mandatory.

❌ No business logic inside Controllers

❌ No Eloquent inside Services

❌ No DB queries outside Repositories

❌ No validation inside Controllers

❌ No transactions outside Services

❌ No Model::all()

❌ No N+1 queries

❌ No unrelated code generation

❌ No unnecessary refactoring

❌ No breaking existing functionality

---

# Development Goal

Every generated module must be:

- Production Ready
- Maintainable
- Testable
- Scalable
- SOLID Compliant
- Repository Pattern Based
- Service Layer Driven
- Enterprise Grade
- Mobile Friendly
- Future-proof


Version 3.0

# 20. Module Completion Checklist

Every module MUST include:

- Database Migrations
- Models
- Repository Interface
- Repository Implementation
- Services
- Form Requests
- API Resources
- Controllers
- Routes
- Blade Views
- Factories
- Demo Seeder
- Test Seeder
- DatabaseSeeder Registration
- Feature Tests

If the module contains UI:

- Update Sidebar Navigation
- Verify menu visibility
- Verify active state
- Verify permission checks

If the module introduces permissions:

- Update PermissionSeeder
- Update RoleSeeder
- Assign default permissions

If the module introduces repositories:

- Register bindings

Never leave any of the above unfinished.

# 21. AI Final Verification Rules

Before marking any module COMPLETE, the AI MUST verify:

- php artisan migrate
- php artisan db:seed
- php artisan route:list
- php artisan optimize
- php artisan test

Verify:

- Sidebar updated
- Routes working
- Seeder registered
- Repository bindings registered
- Permissions available
- No N+1 queries
- No business logic in Controllers
- No Eloquent inside Services

Finally generate:

- Files Created
- Files Modified
- Routes Added
- Sidebar Updated
- Permissions Added
- Seeders Registered
- Tests Added
- QA Score
- Production Readiness


Before marking the module as COMPLETE, verify every item below.

