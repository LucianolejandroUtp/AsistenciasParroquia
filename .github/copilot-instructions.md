# Copilot Instructions - Sistema de Asistencias Primera Comunión

## Project Overview

This is a **Laravel 11.x attendance tracking system** for First Communion students in a parish setting. The system manages **78 students across 2 groups** (Group A: 40, Group B: 38) using **QR codes for quick attendance registration** and features a **TinyDash Bootstrap template** for the frontend.

**Core Technologies:**
- Laravel 12.0 framework with PHP 8.2+
- Custom database schema with UUID fields and custom timestamps
- TinyDash Bootstrap template (located in `docs/recursos_desarrollo/templates_estilos/`)
- QR code system with unique syllable-based identifiers
- Vite + TailwindCSS build system

## Database Architecture

### Key Schema Patterns

**Custom Field Structure:** All tables follow a specific pattern:
```php
// Custom timestamps (not Laravel's default)
$table->timestamp('created_at')->useCurrent()->nullable();
$table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->nullable();

// UUID tracking 
$table->uuid('unique_id')->unique()->default(DB::raw('uuid()'))->nullable();

// Status enum
$table->enum('estado', ['ACTIVO', 'INACTIVO','ELIMINADO'])->default('ACTIVO')->nullable();
```

**Core Tables Hierarchy:**
```
user_types → users → attendance_sessions → attendances
groups → students → attendances
```

### Critical Business Rules

**QR Code Generation Algorithm:**
- Format: `{GROUP}-{FIRSTNAME}-{PATERNAL_SYLLABLE}-{MATERNAL_SYLLABLE}`
- Example: `A-ANTONY-ALF-VILCH` (Antony Alexander Alférez Vilchez, Group A)
- Implemented in `StudentSeeder.php` with UTF-8 syllable extraction
- Text normalization: removes accents, handles ñ→Ñ, compound surnames

**Attendance Status Enum:** `['present', 'absent', 'late', 'justified']`

**Unique Constraints:**
- One student per session: `unique(['attendance_session_id', 'student_id'])`
- Group order: `unique(['group_id', 'order_number'])`

## Project Structure

### Frontend Integration
- **TinyDash Template:** Bootstrap-based admin template in `docs/recursos_desarrollo/templates_estilos/tinydash-master-light/`
- **⚠️ CRITICAL:** Templates and styles in `templates_estilos/` directory are **READ-ONLY reference materials**. They must NEVER be modified directly. Only copy code/assets from these templates to your working directories.
- **Key CSS:** `app-light.css`, `app-dark.css` with theme switching
- **Icons:** Feather icons (`fe fe-*` classes)
- **Layout Types:** Vertical sidebar (default), horizontal nav, boxed layout

### Development Workflow

**Database Management:**
```bash
php artisan migrate           # Run migrations (custom timestamp format)
php artisan db:seed          # Populate with 78 students + demo users
```

**Seeders Run Order (defined in DatabaseSeeder.php):**
1. `UserTypeSeeder` → User types (Admin, Profesor, Staff)
2. `UserSeeder` → Demo users with hashed passwords  
3. `GroupSeeder` → Groups A & B
4. `StudentSeeder` → 78 students with generated QR codes

**Asset Building:**
```bash
npm run dev    # Vite development server
npm run build  # Production build
```

**Development Server (composer script):**
```bash
composer run dev  # Runs server + queue + vite concurrently
```

## Code Patterns

### Architectural Pattern
**Hybrid Architecture: MVC + Service + Repository**
```
Controllers (Thin) → Services (Business Logic) → Repositories (Data Access) → Models (Eloquent)
```

**Why This Architecture:**
- **Controllers:** Handle HTTP only, delegate business logic
- **Services:** Contain domain-specific logic (QR scanning, report generation)
- **Repositories:** Abstract complex database queries
- **Models:** Only relationships and basic properties

### Model Conventions
- Use custom timestamps: avoid Laravel's `$table->timestamps()`
- Include UUID and estado fields in fillable arrays
- Foreign key naming: `{table_name}_id` (e.g., `user_type_id`)

### QR Code Implementation
Reference `StudentSeeder.php` for syllable extraction algorithm:
- Handles UTF-8 multibyte characters with `mb_strlen()`, `mb_substr()`
- Normalizes text: removes accents, preserves ñ
- Compound surname handling: splits on first space
- Vowel-based syllable detection

### Authentication System
- **User Types:** Admin, Profesor (Teacher), Staff roles
- **No email required:** Nullable email field for basic auth
- **Demo Credentials:** Check `UserSeeder.php` for test accounts

### Frontend Template Structure
**TinyDash Classes:**
- Layout: `vertical light` (body classes)
- Sidebar: `.sidebar` with `.sidebar-content`
- Topnav: `.topnav.navbar.navbar-light`
- Theme toggle: `#modeSwitcher` with `data-mode="light/dark"`

## Development Priorities

### Phase 1: Foundation (Current State)
- ✅ Database schema with migrations
- ✅ Seeders with QR code generation
- ⚠️ **Missing:** Controllers, views, authentication middleware

### Critical Implementation Points
1. **Authentication:** Implement role-based access (Admin/Profesor/Staff)
2. **QR Integration:** Frontend QR scanner using TinyDash components
3. **Responsive Design:** Tablet-optimized for attendance scanning
4. **Data Export:** PDF/Excel reports for parish administration

### API Endpoints (Documented in `docs/`)
```php
// Students
GET /api/students/group/{groupId}
POST /api/students/qr-scan

// Attendance Sessions  
POST /api/attendance-sessions
GET /api/attendance-sessions/{id}

// Attendances
POST /api/attendances  
GET /api/attendances/session/{sessionId}
```

## Business Context

**Domain Language:**
- "Sesiones de catequesis" (catechism sessions) not "classes"
- "Estudiantes de Primera Comunión" not generic "students"  
- "Catequista" (catechist) not "teacher"
- Parish context: pastoral care, justifications for absences

**Constraints:**
- Fixed student count: 78 total (immutable groups)
- QR codes are permanent identifiers
- Sessions cannot be deleted if they have attendance records
- Justifications allowed up to 7 days after session

## File Locations

**Documentation:** `docs/sistema_asistencias/` - Requirements, use cases, database docs
**Implementation Plan:** `docs/plan_implementacion/PLAN_FASES_IMPLEMENTACION.md`
**Template Assets:** `docs/recursos_desarrollo/templates_estilos/tinydash-master-light/` - **READ-ONLY REFERENCE**
**Seeders:** `database/seeders/` - Includes QR code generation logic

## ⚠️ IMPORTANT DEVELOPMENT GUIDELINES

### Template Usage Rules
- **Templates Directory:** `docs/recursos_desarrollo/templates_estilos/` contains **REFERENCE MATERIALS ONLY**
- **DO NOT MODIFY:** Never edit files directly within the `templates_estilos/` directory
- **COPY ONLY:** Extract and copy code/styles/assets to your working directories (`resources/`, `public/`, etc.)
- **Preserve Originals:** Keep template files intact for future reference and other developers

### User's Preferred Development Workflow
**CRITICAL:** Always follow this methodology when working with the user:

**1. DETAILED PLANNING FIRST** 📝
- **WHAT** will be implemented (specific features/components)
- **WHY** it's necessary (business justification)
- **HOW** it will be implemented (technical approach)
- **WHICH** files will be created/modified
- **WHAT** risks or considerations exist

**2. WAIT FOR APPROVAL** ✅
- Never implement without explicit user approval
- Allow time for user questions, corrections, or suggestions
- Adjust plan based on user feedback before proceeding

**3. EXPLAINED IMPLEMENTATION** 🔧
- Explain each step during implementation
- Show code and its purpose
- Connect each part to the original plan
- Maintain educational tone for user learning

The user prefers an **educational and collaborative approach** where they can understand, review, and contribute to each development decision.