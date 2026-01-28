# Learnings - Booking System

## [2026-01-28T20:43:30Z] Session Start
Starting execution of booking-system plan.

Wave 1 tasks (parallel):
- Task 1: Install Filament 5
- Task 2: Install Livewire 4  
- Task 3: Create 12 database migrations


## [2026-01-28T21:00:00Z] Task 3: Database Migrations Created

### Migration Files Created
Successfully created all 12 migration files with exact timestamps:
1. 2024_01_01_000001_create_service_categories_table.php
2. 2024_01_01_000002_create_services_table.php
3. 2024_01_01_000003_create_schedules_table.php
4. 2024_01_01_000004_create_schedule_exceptions_table.php
5. 2024_01_01_000005_create_employee_profiles_table.php
6. 2024_01_01_000006_create_vouchers_table.php
7. 2024_01_01_000007_create_appointments_table.php
8. 2024_01_01_000008_create_transactions_table.php
9. 2024_01_01_000009_create_reviews_table.php
10. 2024_01_01_000010_create_reference_photos_table.php
11. 2024_01_01_000011_create_settings_table.php
12. 2024_01_01_000012_create_booking_tokens_table.php

### Foreign Key Order Verification
✅ FK dependencies respected:
- service_categories (no FKs) → created first
- services (FK to service_categories) → created second
- vouchers (no FKs) → created before appointments
- appointments (FK to services, vouchers) → created after both
- reviews (FK to appointments) → created after appointments
- reference_photos (FK to service_categories) → created after categories
- booking_tokens (FK to appointments) → created last

### Migration Execution
- `php artisan migrate:fresh` completed successfully
- All 15 migrations ran (3 Laravel default + 12 booking system)
- Total execution time: ~1.7 seconds

### Table Verification
Verified all 12 booking system tables exist:
- appointments
- booking_tokens
- employee_profiles
- reference_photos
- reviews
- schedule_exceptions
- schedules
- service_categories
- services
- settings
- transactions
- vouchers

### Key Implementation Details
- Used Laravel Blueprint methods for all column types
- Enum values match plan specification exactly
- Indexes added for appointments (start_time, status) and transactions (payable_type, payable_id)
- All FK constraints use proper cascadeOnDelete() or nullOnDelete() methods
- Decimal precision set to (10,2) for all monetary columns
- Timestamps included on all tables

### Notes
- No issues encountered during migration creation or execution
- Schema matches plan specification 100%
- Ready for model creation (Task 4) and seeder creation (Task 5)

## [2026-01-28T21:45:00Z] Task 2: Livewire 4 Installation Complete

### What Was Done
1. Verified Livewire 4.1.0 was already installed via Composer
2. Published Livewire config: `php artisan livewire:publish --config`
3. Added `@livewireStyles` to `resources/views/layouts/page.blade.php` in `<head>` (after @vite, before @stack)
4. Added `@livewireScripts` before `</body>` (after @stack('scripts'))
5. Created Livewire wrapper layout: `resources/views/layouts/livewire.blade.php`
   - Uses `@extends('layouts.page')` and `@section('page')` pattern
   - Renders `{{ $slot }}` for full-page Livewire components
6. Created test component: `php artisan make:livewire TestComponent`
7. Fixed Filament v5 compatibility issue: removed `->locale('hu')` from AdminPanelProvider
8. Ran Pint formatter: all code passes

### Key Insights
- Layout uses `@yield('page')` (section-based), NOT `$slot` (component-based)
- Livewire wrapper layout bridges this by extending page layout and using @section
- Filament v5.1.1 doesn't support `->locale()` method (was removed)
- Test component created as view-only (no separate class file needed)

### Files Modified
- `resources/views/layouts/page.blade.php` - Added Livewire directives
- `resources/views/layouts/livewire.blade.php` - Created wrapper layout
- `resources/views/components/⚡test-component.blade.php` - Created test component
- `config/livewire.php` - Published config
- `app/Providers/Filament/AdminPanelProvider.php` - Fixed locale() issue

### Commit
`feat(frontend): install and configure Livewire 4`


## [2026-01-28T21:50:00Z] Task 1: Filament 5 Installation Complete

### What Was Done
1. Verified Filament 5.1.1 was already installed via Composer
2. Ran `php artisan filament:install --panels --no-interaction`
   - Created `app/Providers/Filament/AdminPanelProvider.php`
   - Published Filament assets to `public/js/filament/` and `public/css/filament/`
   - Registered provider in `bootstrap/providers.php`
3. Set `APP_LOCALE=hu` in `.env` file
4. Published Filament translations: `php artisan vendor:publish --tag=filament-panels-translations`
   - Hungarian (hu) translations already available in Filament 5
   - All translation files copied to `lang/vendor/filament-panels/hu/`
5. Created test admin user via tinker:
   - Email: `admin@doraalfoldy.test`
   - Password: `password123`
6. Verified admin panel accessible at: `http://doraalfoldy_com.test/admin/login`

### Key Insights
- Filament 5.1.1 does NOT support `->locale('hu')` method on Panel class
- Locale is controlled via Laravel's `APP_LOCALE` environment variable
- Hungarian translations are built-in and auto-published
- Admin panel automatically uses app locale for UI strings

### Files Modified
- `.env` - Changed `APP_LOCALE=en` to `APP_LOCALE=hu`
- `app/Providers/Filament/AdminPanelProvider.php` - Created by installer
- `bootstrap/providers.php` - Auto-registered AdminPanelProvider
- `lang/vendor/filament-panels/hu/` - Published Hungarian translations

### Commit
`feat(admin): install and configure Filament 5 panel`

### Verification Checklist
✅ Filament 5.1.1 installed (composer show filament/filament)
✅ Admin panel configured at `/admin`
✅ Test admin user created (admin@doraalfoldy.test)
✅ Hungarian translations published
✅ Panel loads at `http://doraalfoldy_com.test/admin/login`
✅ Commit created


## [2026-01-28T22:15:00Z] Task 4: Eloquent Models and Enums Created

### What Was Done
1. Created 4 enum files in `app/Enums/`:
   - `AppointmentStatus.php` - 5 cases: pending, confirmed, cancelled, completed, no_show
   - `VoucherType.php` - 3 cases: percentage, fixed, gift_card (with business logic comments)
   - `TransactionStatus.php` - 4 cases: pending, completed, failed, refunded
   - `BookingTokenType.php` - 3 cases: cancel, reschedule, review

2. Created 12 model files in `app/Models/`:
   - `ServiceCategory.php` - hasMany services, hasMany referencePhotos
   - `Service.php` - belongsTo category, hasMany appointments
   - `Schedule.php` - scopeForDay() for day queries
   - `ScheduleException.php` - scopeForDate() for date queries
   - `EmployeeProfile.php` - static profile() singleton accessor
   - `Appointment.php` - belongsTo service, belongsTo voucher (nullable), hasMany tokens, morphOne transaction
   - `Voucher.php` - scopeValid() for finding valid vouchers, hasBalance(), isUsed() helpers
   - `Transaction.php` - morphTo payable
   - `Review.php` - belongsTo appointment (nullable)
   - `ReferencePhoto.php` - belongsTo category
   - `Setting.php` - static get()/set() with encryption for barion_pos_key
   - `BookingToken.php` - belongsTo appointment, scopeValid()

3. All models use:
   - `casts()` method (not $casts property) per Laravel 12 conventions
   - Explicit return type declarations on all methods
   - Proper fillable arrays for mass assignment
   - Enum casting for status/type fields

4. Verified functionality via tinker:
   - Models instantiate correctly
   - `Voucher::valid('CODE')->first()` scope works
   - `Setting::get('key')` / `Setting::set('key', 'val')` work
   - `Setting` encryption works for barion_pos_key
   - `EmployeeProfile::profile()` singleton accessor works

5. Ran Pint formatter: all code passes

### Key Implementation Details

#### Voucher Model
- `scopeValid()` implements complex business logic:
  - Checks expiration (expires_at IS NULL OR > now())
  - For percentage/fixed: checks used_at IS NULL
  - For gift_card: checks balance > 0
- Helper methods: `hasBalance()`, `isUsed()`

#### Setting Model
- Static `get()` and `set()` methods for key-value access
- Automatic encryption/decryption for sensitive keys (barion_pos_key)
- Uses `updateOrCreate()` for upsert behavior

#### Appointment Model
- Casts status to AppointmentStatus enum
- All monetary fields cast to decimal:2
- Datetime fields: start_time, end_time, reminder_sent_at
- Relationships: service, voucher (nullable), tokens, transaction (morphOne)

#### BookingToken Model
- `scopeValid()` checks: used_at IS NULL AND expires_at > now()
- Casts type to BookingTokenType enum

#### EmployeeProfile Model
- Static `profile()` method returns first record (singleton pattern)
- No relationships (standalone profile)

### Files Created
- `app/Enums/AppointmentStatus.php`
- `app/Enums/VoucherType.php`
- `app/Enums/TransactionStatus.php`
- `app/Enums/BookingTokenType.php`
- `app/Models/ServiceCategory.php`
- `app/Models/Service.php`
- `app/Models/Schedule.php`
- `app/Models/ScheduleException.php`
- `app/Models/EmployeeProfile.php`
- `app/Models/Appointment.php`
- `app/Models/Voucher.php`
- `app/Models/Transaction.php`
- `app/Models/Review.php`
- `app/Models/ReferencePhoto.php`
- `app/Models/Setting.php`
- `app/Models/BookingToken.php`

### Notes
- All enum and model definitions match plan specification exactly
- Used Laravel 12 conventions: casts() method, not $casts property
- Setting model encryption uses Laravel's Crypt facade
- Voucher validation logic matches Business Rules (lines 446-466 in plan)
- Ready for factory/seeder creation (Task 5)


## Translations (Task 6)
- Set up Hungarian (hu) and English (en) translations in `lang/`.
- Created `booking.php` for custom booking UI strings.
- Created `emails.php` for email content.
- Standard Laravel files (`auth.php`, `pagination.php`, `validation.php`) were also added for both languages.
- Hungarian validation attributes include specific booking-related fields like `service_id`, `voucher_code`, etc.
- Verified translation retrieval using `App::setLocale()` and `__()` helper in Tinker.

## [2026-01-28T22:30:00Z] Task 5: Factories and Seeders Created

### What Was Done
1. Created 12 factories in `database/factories/`:
   - ServiceCategoryFactory, ServiceFactory, ScheduleFactory, ScheduleExceptionFactory
   - EmployeeProfileFactory, AppointmentFactory, VoucherFactory, TransactionFactory
   - ReviewFactory, ReferencePhotoFactory, SettingFactory, BookingTokenFactory

2. Created 4 seeders in `database/seeders/`:
   - ServiceCategorySeeder: 3 categories (Szempilla, Smink, Szemöldök)
   - ScheduleSeeder: 7 schedules (Mon-Fri 9-17, Sat 9-14, Sun off)
   - SettingSeeder: 9 settings (cancellation_hours, reminder_hours, default_buffer_minutes, site_name, admin_email, booking_terms, barion_pos_key, barion_sandbox, slot_lock)
   - EmployeeProfileSeeder: 1 employee (Dóra Álfoldy)

3. Registered seeders in DatabaseSeeder via $this->call()

4. Ran `php artisan migrate:fresh --seed` successfully

### Key Implementation Details

#### Factory Patterns
- ServiceFactory: Creates with random category via factory()
- AppointmentFactory: Creates with Service factory, calculates end_time from duration
- VoucherFactory: Uses unique bothify() for code generation
- ReviewFactory: Creates with Appointment factory
- ReferencePhotoFactory: Creates with ServiceCategory factory
- BookingTokenFactory: Creates with Appointment factory, uses sha256() for token

#### Seeder Patterns
- ServiceCategorySeeder: Direct create() calls with hardcoded Hungarian names
- ScheduleSeeder: Loop for Mon-Fri (0-4), separate Sat (5), separate Sun (6) with is_off=true
- SettingSeeder: Uses Setting::set() static method for all 9 keys
- EmployeeProfileSeeder: Single create() call with Dóra Álfoldy

#### Important Discovery
- EmployeeProfile migration has: id, name, bio, image_path, instagram_url, timestamps
- Does NOT have email/phone columns (unlike initial assumption)
- Updated factory and seeder to match actual migration schema

### Verification Results
✅ `php artisan migrate:fresh --seed` runs without errors
✅ ServiceCategory count: 3
✅ Schedule count: 7
✅ Setting count: 9 (including slot_lock)
✅ EmployeeProfile count: 1
✅ slot_lock setting value: "lock_row" (critical for advisory locking)
✅ Pint formatter: pass
✅ Commit: `feat(database): add factories and seeders`

### Files Created
- database/factories/ServiceCategoryFactory.php
- database/factories/ServiceFactory.php
- database/factories/ScheduleFactory.php
- database/factories/ScheduleExceptionFactory.php
- database/factories/EmployeeProfileFactory.php
- database/factories/AppointmentFactory.php
- database/factories/VoucherFactory.php
- database/factories/TransactionFactory.php
- database/factories/ReviewFactory.php
- database/factories/ReferencePhotoFactory.php
- database/factories/SettingFactory.php
- database/factories/BookingTokenFactory.php
- database/seeders/ServiceCategorySeeder.php
- database/seeders/ScheduleSeeder.php
- database/seeders/SettingSeeder.php
- database/seeders/EmployeeProfileSeeder.php

### Files Modified
- database/seeders/DatabaseSeeder.php - Added $this->call() for 4 seeders

### Notes
- All factories follow Laravel conventions with proper relationships
- Seeders use direct model creation for deterministic data
- slot_lock setting is CRITICAL for slot reservation locking strategy
- Ready for Task 6 (Hungarian Translations)

