# Salonic-like Booking System

## TL;DR

> **Quick Summary**: Build a complete salon booking system with Livewire 4 multi-step wizard for customers and Filament 5 admin panel in Hungarian. Includes Barion payment integration, voucher system, and email notifications.
> 
> **Deliverables**:
> - Filament 5 admin panel with Hungarian localization
> - Livewire 4 booking wizard (Service → Date → Time → Details → Payment)
> - Barion payment integration (sandbox)
> - Email notification system (6 emails)
> - Customer self-service cancel/reschedule via magic links
> - Review collection system
> 
> **Estimated Effort**: XL (Large project, multiple phases)
> **Parallel Execution**: YES - 4 waves
> **Critical Path**: Package Setup → Migrations → Models → Filament Resources → Livewire Booking

---

## Current State Reconciliation (CRITICAL)

### Package State (Verified via composer.json)
| Package | Status | Action |
|---------|--------|--------|
| `filament/filament` | NOT INSTALLED | Fresh install v5.x |
| `livewire/livewire` | NOT INSTALLED | Fresh install v4.x |

### Database Connection (Verified via .env.example)
- **DB_CONNECTION**: `mariadb` (NOT mysql)
- All DB queries and tests must use `mariadb` connection name

### Existing Migration Files (Verified in database/migrations/)
| File | Tables Created |
|------|---------------|
| `0001_01_01_000000_create_users_table.php` | users, password_reset_tokens, sessions |
| `0001_01_01_000001_create_cache_table.php` | cache, cache_locks |
| `0001_01_01_000002_create_jobs_table.php` | jobs, job_batches, failed_jobs |

**Total existing migrations**: 3 files

### Live Database Tables (Verified via `php artisan tinker`)
Current tables in database: appointments, cache, cache_locks, employee_profiles, failed_jobs, job_batches, jobs, migrations, password_reset_tokens, schedules, services, sessions, transactions, users, vouchers

**Tables WITH migrations** (Laravel defaults): users, password_reset_tokens, sessions, cache, cache_locks, jobs, job_batches, failed_jobs, migrations

**Tables WITHOUT migrations** (business tables to recreate):

| Existing Table | Plan Changes |
|----------------|--------------|
| `services` | ADD category_id, buffer_minutes |
| `appointments` | ADD price_at_booking, deposit_at_booking, voucher_id (FK), voucher_discount, buffer_at_booking, locale, reminder_sent_at |
| `schedules` | No changes |
| `vouchers` | ADD used_at |
| `transactions` | ADD barion_status |
| `employee_profiles` | REMOVE reviews JSON column |

**Tables to CREATE** (do not exist yet): service_categories, schedule_exceptions, reviews, reference_photos, settings, booking_tokens

### Migration Strategy Decision
**Strategy**: Create fresh migrations that define the COMPLETE desired schema. Use `migrate:fresh --seed` for development/staging.

**⚠️ CRITICAL: Local Development Workflow**:
Because business tables already exist in the local database (appointments, services, schedules, etc.), running `php artisan migrate` will FAIL with "table already exists" errors.

**Correct workflow**:
```bash
# ALWAYS use migrate:fresh during development
php artisan migrate:fresh --seed

# NEVER use plain migrate (will fail):
php artisan migrate  # ❌ "Table 'services' already exists"
```

**Why**: The old tables have no migration records in the `migrations` table, so Laravel doesn't know they exist and tries to create them again.

**Production Safety** (different approach):
- Production deployment requires manual data migration BEFORE running new migrations
- Create backup of production data
- Map existing data to new schema (especially appointments, vouchers with existing balances)
- DO NOT run `migrate:fresh` on production - it drops all data
- Instead: Create ALTER TABLE migrations to modify existing tables

**Local Development**: `migrate:fresh --seed` is safe and expected. Run it every time after pulling migration changes.

---

## Context

### Original Request
Build a Salonic-like timeslot-booking interface with Livewire and Filament admin panel. Single employee salon with reviews, ratings, instagram link, reference photos. Voucher system for services.

### Interview Summary
**Key Discussions**:
- Customer identity: Magic links (no user accounts)
- Reviews: Customer submission after appointment, admin approval
- Vouchers: Universal (any service), deposit must be card-only
- Buffer time: Configurable via admin
- Cancellation: Configurable hours, manual refund by admin
- Schedule exceptions: Separate table for holidays
- Multi-service: No - single service per booking
- Reschedule: Time only, cannot change service

---

## Work Objectives

### Core Objective
Create a complete salon booking system where customers can book services via a multi-step wizard with Barion payment, and admins manage everything via Filament panel in Hungarian.

### Concrete Deliverables
- 12 new migration files (complete schema)
- 12 Eloquent models with relationships
- 4 PHP Enum classes
- 9 Filament resources (ServiceCategory, Service, Appointment, Schedule, ScheduleException, Voucher, Review, ReferencePhoto, EmployeeProfile) + Settings page
- 1 Livewire booking wizard (multi-step)
- 3 Livewire pages (cancel, reschedule, review)
- 6 email templates (Mailable classes)
- Barion service class
- Language files (HU/EN)

### Definition of Done
- [ ] `php artisan migrate:fresh --seed` creates complete schema
- [ ] `php artisan test` passes all tests
- [ ] Admin can CRUD all entities in Hungarian
- [ ] Customer can complete booking flow end-to-end
- [ ] Barion sandbox payment works
- [ ] Email notifications send correctly
- [ ] Cancel/reschedule via magic link works

### Must Have
- Pessimistic locking for slot booking
- Price stored at booking time (immutable)
- Magic link tokens with expiration
- Configurable settings via admin
- Hungarian admin panel

### Must NOT Have (Guardrails)
- User registration/accounts for customers
- Multiple employee support
- Recurring appointments
- SMS notifications
- Automatic refunds
- Multi-service per booking
- Abstract payment provider interface
- Complex analytics dashboards

---

## Complete Database Schema Specification

### Enum Definitions

```php
// App\Enums\AppointmentStatus
enum AppointmentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case NO_SHOW = 'no_show';
}

// App\Enums\VoucherType
enum VoucherType: string
{
    case PERCENTAGE = 'percentage';  // Single-use: value = percentage (10 = 10%)
    case FIXED = 'fixed';            // Single-use: value = fixed HUF amount
    case GIFT_CARD = 'gift_card';    // Multi-use: tracks balance
}

// App\Enums\TransactionStatus
enum TransactionStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
}

// App\Enums\BookingTokenType
enum BookingTokenType: string
{
    case CANCEL = 'cancel';
    case RESCHEDULE = 'reschedule';
    case REVIEW = 'review';
}
```

### Migration Files (12 total, correct order for FK constraints)

**CRITICAL: Migration order ensures FK targets exist before references**

**Migration Files (12 tables)** - ORDER IS CRITICAL FOR FK CONSTRAINTS:
```
1. 2024_01_01_000001_create_service_categories_table.php
2. 2024_01_01_000002_create_services_table.php
3. 2024_01_01_000003_create_schedules_table.php
4. 2024_01_01_000004_create_schedule_exceptions_table.php
5. 2024_01_01_000005_create_employee_profiles_table.php
6. 2024_01_01_000006_create_vouchers_table.php  -- MOVED BEFORE appointments (appointments.voucher_id FK)
7. 2024_01_01_000007_create_appointments_table.php  -- Now after vouchers
8. 2024_01_01_000008_create_transactions_table.php
9. 2024_01_01_000009_create_reviews_table.php
10. 2024_01_01_000010_create_reference_photos_table.php
11. 2024_01_01_000011_create_settings_table.php
12. 2024_01_01_000012_create_booking_tokens_table.php
```

**Total: 12 new migration files creating 12 new tables**

### Table Schemas (Complete with all columns)

```sql
-- 1. service_categories (NO FKs, safe to create first)
CREATE TABLE service_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 2. services (FK to service_categories - created after categories)
CREATE TABLE services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    duration_minutes INT UNSIGNED NOT NULL,
    buffer_minutes INT UNSIGNED NULL,
    price DECIMAL(10,2) NOT NULL,
    deposit_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE SET NULL
);

-- 3. schedules (NO FKs)
CREATE TABLE schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    day_of_week TINYINT UNSIGNED NOT NULL UNIQUE,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_off TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 4. schedule_exceptions (NO FKs)
CREATE TABLE schedule_exceptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL UNIQUE,
    reason VARCHAR(255) NULL,
    is_closed TINYINT(1) NOT NULL DEFAULT 1,
    custom_start_time TIME NULL,
    custom_end_time TIME NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 5. employee_profiles (NO FKs, NO reviews JSON - moved to reviews table)
CREATE TABLE employee_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    bio TEXT NULL,
    image_path VARCHAR(255) NULL,
    instagram_url VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 6. vouchers (NO FKs, includes used_at for single-use tracking) -- BEFORE appointments (appointments.voucher_id FK)
CREATE TABLE vouchers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(255) NOT NULL UNIQUE,
    type ENUM('percentage','fixed','gift_card') NOT NULL,
    value DECIMAL(10,2) NOT NULL,  -- For all types: the voucher's face value
    balance DECIMAL(10,2) NOT NULL DEFAULT 0,  -- For gift_card: remaining balance. For percentage/fixed: always 0 (uses used_at tracking)
    recipient_email VARCHAR(255) NULL,
    used_at TIMESTAMP NULL,  -- For percentage/fixed: set when used. For gift_card: always NULL (uses balance tracking)
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 7. appointments (FK to services, FK to vouchers) -- AFTER vouchers
CREATE TABLE appointments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    voucher_id BIGINT UNSIGNED NULL,  -- FK to voucher used (NULL if no voucher). Enables webhook to consume voucher.
    user_name VARCHAR(255) NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    user_phone VARCHAR(255) NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,  -- EXCLUDES buffer time (buffer only used for spacing between slots)
    buffer_at_booking INT UNSIGNED NOT NULL DEFAULT 0,  -- Buffer minutes stored at booking time (for overlap checks with different service buffers)
    status ENUM('pending','confirmed','cancelled','completed','no_show') NOT NULL DEFAULT 'pending',
    price_at_booking DECIMAL(10,2) NOT NULL,
    deposit_at_booking DECIMAL(10,2) NOT NULL,
    voucher_discount DECIMAL(10,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    locale VARCHAR(2) NOT NULL DEFAULT 'hu',
    reminder_sent_at TIMESTAMP NULL,  -- Tracks when reminder email was sent (NULL = not sent)
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE SET NULL,
    INDEX (start_time),
    INDEX (status)
);

-- 8. transactions (morphs, NO direct FKs)
CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id VARCHAR(255) NOT NULL,
    status ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
    amount DECIMAL(10,2) NOT NULL,
    payable_type VARCHAR(255) NOT NULL,
    payable_id BIGINT UNSIGNED NOT NULL,
    barion_status VARCHAR(50) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX (payable_type, payable_id)
);

-- 9. reviews (FK to appointments - nullable)
CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id BIGINT UNSIGNED NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    content TEXT NULL,
    is_approved TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL
);

-- 10. reference_photos (FK to service_categories)
CREATE TABLE reference_photos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE CASCADE
);

-- 11. settings (NO FKs)
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(255) NOT NULL UNIQUE,
    value TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 12. booking_tokens (FK to appointments)
CREATE TABLE booking_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id BIGINT UNSIGNED NOT NULL,
    token CHAR(36) NOT NULL UNIQUE,
    type ENUM('cancel','reschedule','review') NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
);
```

---

## Business Rules Specification

### Voucher Usage Rules (COMPLETE)

```php
/**
 * Voucher Types and Usage
 */

// PERCENTAGE type:
// - Single-use only
// - used_at = NULL means not yet used
// - After successful booking: set used_at = now()
// - Validation: type=percentage && used_at IS NULL && (expires_at IS NULL OR expires_at > now())
// - Cannot be used again once used_at is set

// FIXED type:
// - Single-use only
// - Same as percentage: used_at tracking
// - After successful booking: set used_at = now()

// GIFT_CARD type:
// - Multi-use, balance tracking
// - used_at is ALWAYS NULL (never set)
// - balance decremented on each use
// - Valid while: balance > 0 && (expires_at IS NULL OR expires_at > now())
// - Can be used multiple times until balance exhausted

/**
 * Voucher Application at Booking Time
 */
public function applyVoucher(Voucher $voucher, Service $service): float
{
    $remainingAfterDeposit = $service->price - $service->deposit_fee;
    
    return match($voucher->type) {
        VoucherType::PERCENTAGE => min(
            round($service->price * $voucher->value / 100, 2),
            $remainingAfterDeposit
        ),
        VoucherType::FIXED => min($voucher->value, $remainingAfterDeposit),
        VoucherType::GIFT_CARD => min($voucher->balance, $remainingAfterDeposit),
    };
}

/**
 * Voucher Consumption After Successful Payment
 */
public function consumeVoucher(Voucher $voucher, float $discountUsed): void
{
    match($voucher->type) {
        VoucherType::PERCENTAGE, VoucherType::FIXED => $voucher->update(['used_at' => now()]),
        VoucherType::GIFT_CARD => $voucher->decrement('balance', $discountUsed),
    };
}

/**
 * Voucher Validation - Model Scope (CANONICAL API)
 * 
 * Usage: Voucher::valid($code)->first()
 * 
 * This is the ONLY API for finding valid vouchers. Do not use findValidVoucher().
 */
// In App\Models\Voucher:
public function scopeValid(Builder $query, string $code): Builder
{
    return $query->where('code', $code)
        ->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        })
        ->where(function ($q) {
            // Percentage/Fixed: not yet used
            $q->where(function ($sub) {
                $sub->whereIn('type', ['percentage', 'fixed'])
                    ->whereNull('used_at');
            })
            // Gift card: has balance > 0
            ->orWhere(function ($sub) {
                $sub->where('type', 'gift_card')
                    ->where('balance', '>', 0);
            });
        });
}

// Usage in BookingWizard:
$voucher = Voucher::valid($this->voucherCode)->first();

// Usage in tests:
$voucher = Voucher::valid('TESTCODE')->first();
expect($voucher)->not->toBeNull();
```

### Buffer/Overlap Model (EXPLICIT)

**Key Decision**: `appointments.end_time` stores the **service end time EXCLUDING buffer**. Buffer is only used for spacing calculations when checking availability.

**Example**:
- Service: 60 min duration, 10 min buffer
- Booking at 10:00: stored as start_time=10:00, end_time=11:00
- Next available slot: 11:10 (end_time + buffer)

### Concurrency/Locking Strategy

**The Core Problem**: When no conflicting rows exist, `lockForUpdate()` locks nothing, so two concurrent requests both see "no conflict" and both create appointments.

**Solution**: Use a dedicated lock row in the `settings` table as a deterministic lock target.

```php
/**
 * Uses database transaction with advisory lock via settings table.
 * Connection: 'mariadb' (as per .env.example DB_CONNECTION)
 * 
 * LOCKING APPROACH:
 * 1. Lock a deterministic row (settings.key='slot_lock') using FOR UPDATE
 * 2. This serializes ALL slot reservation attempts
 * 3. Then check for actual conflicts and create if clear
 * 
 * WHY NOT LOCKING APPOINTMENT ROWS:
 * - If no conflicts exist, lockForUpdate() locks nothing
 * - Two concurrent requests both see "no conflict" → double booking
 * - Advisory lock via settings row guarantees mutual exclusion
 */
/**
 * Reserve a time slot for a service.
 * 
 * @param Service $service The service being booked
 * @param Carbon $startTime Desired start time
 * @param array $customerData Customer-provided data. ALLOWED KEYS ONLY:
 *   - user_name (string, required): Customer's name
 *   - user_email (string, required): Customer's email
 *   - user_phone (string, required): Customer's phone
 *   - notes (string|null, optional): Booking notes
 *   - locale (string, required): 'hu' or 'en'
 *   - voucher_id (int|null, optional): FK to applied voucher
 *   - voucher_discount (float, optional): Discount amount (default 0)
 * 
 * COMPUTED INTERNALLY (DO NOT pass in $customerData):
 *   - service_id, start_time, end_time, buffer_at_booking, status, price_at_booking, deposit_at_booking
 * 
 * @return Appointment|null Created appointment or null if slot taken
 */
public function reserveSlot(Service $service, Carbon $startTime, array $customerData): ?Appointment
{
    return DB::transaction(function () use ($service, $startTime, $customerData) {
        // STEP 1: Acquire advisory lock via settings table row
        DB::select("SELECT value FROM settings WHERE `key` = 'slot_lock' FOR UPDATE");
        
        // STEP 2: Calculate time ranges (COMPUTED - not from customerData)
        $newBuffer = $this->getEffectiveBuffer($service);
        $serviceEndTime = $startTime->copy()->addMinutes($service->duration_minutes);
        $newBlockedUntil = $serviceEndTime->copy()->addMinutes($newBuffer);
        
        // STEP 3: Check for actual conflicts
        $conflicting = Appointment::query()
            ->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED])
            ->where(function ($query) use ($startTime, $newBlockedUntil) {
                $query->whereRaw('start_time < ?', [$newBlockedUntil])
                      ->whereRaw('DATE_ADD(end_time, INTERVAL buffer_at_booking MINUTE) > ?', [$startTime]);
            })
            ->exists();
        
        if ($conflicting) {
            return null;
        }
        
        // STEP 4: Whitelist allowed customer data keys
        $allowedKeys = ['user_name', 'user_email', 'user_phone', 'notes', 'locale', 'voucher_id', 'voucher_discount'];
        $safeCustomerData = array_intersect_key($customerData, array_flip($allowedKeys));
        
        // STEP 5: Create appointment with computed + whitelisted fields
        return Appointment::create([
            // Computed fields (cannot be overridden)
            'service_id' => $service->id,
            'start_time' => $startTime,
            'end_time' => $serviceEndTime,
            'buffer_at_booking' => $newBuffer,
            'status' => AppointmentStatus::PENDING,
            'price_at_booking' => $service->price,
            'deposit_at_booking' => $service->deposit_fee,
            // Customer-provided fields (whitelisted)
            ...$safeCustomerData,
        ]);
    });
}
```

**Required Seed**: Add `slot_lock` key to SettingSeeder:
```php
Setting::set('slot_lock', 'lock_row');  // Value doesn't matter, just needs to exist
```

**Verification of Locking Guarantee**:
```bash
# True concurrent test using two parallel processes:
# Terminal 1:
php artisan tinker --execute="
    \$service = App\Models\Service::first();
    \$time = now()->addDay()->setTime(10, 0);
    \$result = app(App\Services\SlotAvailabilityService::class)->reserveSlot(
        \$service, \$time, ['user_name'=>'Test1','user_email'=>'t1@test.com','user_phone'=>'+36201111111','locale'=>'hu']
    );
    echo \$result ? 'CREATED:'.\$result->id : 'BLOCKED';
"

# Terminal 2 (run simultaneously):
php artisan tinker --execute="
    \$service = App\Models\Service::first();
    \$time = now()->addDay()->setTime(10, 0);
    \$result = app(App\Services\SlotAvailabilityService::class)->reserveSlot(
        \$service, \$time, ['user_name'=>'Test2','user_email'=>'t2@test.com','user_phone'=>'+36202222222','locale'=>'hu']
    );
    echo \$result ? 'CREATED:'.\$result->id : 'BLOCKED';
"

# Expected: Exactly ONE shows CREATED:N, the other shows BLOCKED
# Verify: SELECT COUNT(*) FROM appointments WHERE start_time = '...' → should be 1
```
```

### Reservation Lifecycle

```
1. Customer selects service → No DB record yet
2. Customer selects date/time → No DB record yet  
3. Customer fills details → No DB record yet
4. Customer clicks "Pay Deposit" →
   a. reserveSlot() called → Appointment created (status=PENDING)
   b. Barion payment initiated
   c. Customer redirected to Barion
5. Payment outcome:
   - SUCCESS: status=CONFIRMED, emails sent, tokens created
   - FAILURE: status=CANCELLED
6. Cleanup: Scheduled command cancels PENDING appointments >30min old
```

### Barion Status Mapping

```php
$barionToTransaction = [
    'Prepared' => TransactionStatus::PENDING,
    'Started' => TransactionStatus::PENDING,
    'InProgress' => TransactionStatus::PENDING,
    'Succeeded' => TransactionStatus::COMPLETED,
    'Canceled' => TransactionStatus::FAILED,
    'Failed' => TransactionStatus::FAILED,
    'Expired' => TransactionStatus::FAILED,
];

$barionToAppointment = [
    'Succeeded' => AppointmentStatus::CONFIRMED,
    'Canceled' => AppointmentStatus::CANCELLED,
    'Failed' => AppointmentStatus::CANCELLED,
    'Expired' => AppointmentStatus::CANCELLED,
];
```

---

## Settings Specification (9 Keys)

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `cancellation_hours` | int | 24 | Hours before cancel/reschedule allowed (0=disabled) |
| `reminder_hours` | int | 24 | Hours before reminder email sent |
| `default_buffer_minutes` | int | 0 | Default buffer between appointments |
| `site_name` | string | "Dóra Álfoldy" | Site name for branding |
| `admin_email` | string | "" | Admin notification email |
| `booking_terms` | text | "" | Terms shown at booking |
| `barion_pos_key` | string | "" | Barion POSKey (**encrypted** - see below) |
| `barion_sandbox` | bool | true | Use Barion sandbox |
| `slot_lock` | string | "lock_row" | Advisory lock row for slot reservation (DO NOT delete) |

### Encryption Contract for barion_pos_key

**Storage**: `barion_pos_key` is stored **encrypted** in the database using Laravel's `Crypt` facade.

**Setting model implementation**:
```php
class Setting extends Model
{
    protected static array $encryptedKeys = ['barion_pos_key'];
    
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;
        
        $value = $setting->value;
        if (in_array($key, static::$encryptedKeys) && $value) {
            $value = Crypt::decryptString($value);
        }
        return $value;
    }
    
    public static function set(string $key, mixed $value): void
    {
        if (in_array($key, static::$encryptedKeys) && $value) {
            $value = Crypt::encryptString($value);
        }
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
```

**Settings form behavior** (in Filament Settings page):
- POSKey field shows as password input (masked)
- If field is left empty on save, **keep existing value** (don't overwrite with null)
- If new value provided, encrypt and save

```php
TextInput::make('barion_pos_key')
    ->password()
    ->dehydrateStateUsing(fn ($state, $record) => 
        $state ?: Setting::get('barion_pos_key')  // Keep old if empty
    )
```

---

## External References (Executable)

### Package Installation Commands
```bash
# Filament 5
composer require filament/filament:"^5.0"
php artisan filament:install --panels

# Livewire 4  
composer require livewire/livewire:"^4.0"

# Hungarian translations for Filament (SINGLE APPROACH - no external packages)
php artisan vendor:publish --tag=filament-panels-translations
# This creates lang/vendor/filament-panels/ - edit Hungarian files there
```

### Documentation URLs
- Filament 5 Docs: https://filamentphp.com/docs/5.x
- Livewire 4 Docs: https://livewire.laravel.com/docs
- Barion API Docs: https://docs.barion.com/Main_Page
- Laravel 12 Docs: https://laravel.com/docs/12.x

---

## Timezone Strategy (EXPLICIT)

### Decision: Europe/Budapest for All Times

**Current state**: `config/app.php` has `'timezone' => 'UTC'` (hardcoded, not reading from env)

**Required change**: Edit `config/app.php` to use local business time:
```php
// config/app.php - CHANGE THIS LINE:
'timezone' => 'Europe/Budapest',  // Was: 'UTC'
```

**NOTE**: The current config does NOT use `env('APP_TIMEZONE')`, so setting `.env` won't work. You must edit the config file directly.

**Rationale**:
- This is a Hungarian salon serving local customers
- Working hours (09:00-17:00) are in local time
- Appointment times should display without conversion
- Cancellation windows (e.g., "24 hours before") are intuitive in local time
- Simpler than storing UTC and converting for display

**Implications**:
- Database stores `DATETIME` in Europe/Budapest timezone
- Schedules `start_time`/`end_time` are local time (e.g., 09:00 = 9 AM Budapest)
- Reminder calculation: `now()->addHours($reminderHours)` works correctly
- DST transitions: Carbon handles automatically

**If UTC is preferred later** (e.g., multi-region expansion):
- Store all times in UTC
- Convert on display using `->setTimezone('Europe/Budapest')`
- Update slot generation to work in UTC + convert

**For this implementation**: Use Europe/Budapest. It's simpler and matches user expectations for a local business.

---

## Locale Wiring (EXPLICIT)

### Scope Definition
- **Filament Admin Panel** (`/admin/*`): ALWAYS Hungarian (hardcoded in panel config)
- **Customer Booking Pages** (`/booking`, `/booking/*`): Switchable HU/EN via `?lang=` parameter

### Configuration Changes

**1. Set default locale via `.env`** (NOT hardcoding config/app.php):
```bash
# Add to .env file:
APP_LOCALE=hu
```

The `config/app.php` already reads from env: `'locale' => env('APP_LOCALE', 'en')`.
Setting `.env` is preferred because:
- Keeps config files unchanged (easier upgrades)
- Environment-specific (can differ per deployment)
- Already supported by Laravel's default config

**Verify** `config/app.php` has:
```php
'locale' => env('APP_LOCALE', 'en'),
'fallback_locale' => 'en',  // Keep English as fallback
```

**2. Filament Panel config** (`app/Providers/Filament/AdminPanelProvider.php`):
```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->locale('hu')  // Force Hungarian for admin panel
        // ... rest of config
}
```

**3. SetLocale middleware** - ONLY affects booking routes, NOT admin:
```php
// In bootstrap/app.php:
->withMiddleware(function (Middleware $middleware) {
    $middleware->appendToGroup('web', [
        // ... existing middleware
    ]);
    
    // Create custom middleware group for booking routes only
    $middleware->group('booking', [
        \App\Http\Middleware\SetLocale::class,
    ]);
})

// In routes/web.php:
Route::middleware('booking')->group(function () {
    Route::get('/booking', BookingWizard::class)->name('booking');
    Route::get('/booking/{token}/cancel', CancelBooking::class)->name('booking.cancel');
    Route::get('/booking/{token}/reschedule', RescheduleBooking::class)->name('booking.reschedule');
    Route::get('/booking/{token}/review', SubmitReview::class)->name('booking.review');
});
```

**Result**: Admin always sees Hungarian. Customers can switch languages. The two don't interfere.

---

## Verification Strategy

### Test Configuration
- **Framework**: Pest 4.x (installed)
- **DB Connection**: `mariadb` (NOT SQLite - required for MariaDB-specific SQL)
- **Approach**: TDD

### Test Database Setup (CRITICAL)

The current `phpunit.xml` forces SQLite in-memory, but this plan uses MariaDB-specific SQL:
- `DATE_ADD(end_time, INTERVAL buffer_at_booking MINUTE)` - SQLite doesn't support INTERVAL syntax
- `lockForUpdate()` - SQLite doesn't support row-level locking

**Required Change**: Update `phpunit.xml` to use MariaDB for tests:

```xml
<!-- phpunit.xml - REMOVE these lines: -->
<!-- <env name="DB_CONNECTION" value="sqlite"/> -->
<!-- <env name="DB_DATABASE" value=":memory:"/> -->

<!-- REPLACE with (or just remove to use .env.testing): -->
<env name="DB_CONNECTION" value="mariadb"/>
<env name="DB_DATABASE" value="doraalfoldy_com_test"/>
```

**Create test database** (one-time setup):
```bash
# In MariaDB/MySQL:
CREATE DATABASE doraalfoldy_com_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON doraalfoldy_com_test.* TO 'your_user'@'localhost';
```

**Alternative: Use `.env.testing`** (recommended):
```bash
# Create .env.testing with same values as .env but different DB:
DB_CONNECTION=mariadb
DB_DATABASE=doraalfoldy_com_test
```

Then remove DB_* env overrides from `phpunit.xml` entirely - Laravel will auto-load `.env.testing`.

**Why MariaDB for Tests**:
- Slot locking logic uses `lockForUpdate()` which requires real row locking
- Buffer overlap query uses MariaDB `DATE_ADD` syntax
- Tests must verify actual production behavior, not SQLite approximation

### Concurrency Test (Correct for MariaDB)

```php
use Illuminate\Support\Facades\DB;

it('prevents double booking via pessimistic lock', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    Schedule::factory()->create([
        'day_of_week' => now()->addDay()->dayOfWeek,
        'start_time' => '09:00',
        'end_time' => '17:00'
    ]);
    
    $targetTime = now()->addDay()->setTime(10, 0);
    $slotService = app(SlotAvailabilityService::class);
    
    // First reservation succeeds
    $result1 = $slotService->reserveSlot($service, $targetTime, [
        'user_name' => 'Test 1',
        'user_email' => 'test1@test.com',
        'user_phone' => '+36201234567',
        'locale' => 'hu',
    ]);
    
    // Second reservation for same slot fails
    $result2 = $slotService->reserveSlot($service, $targetTime, [
        'user_name' => 'Test 2',
        'user_email' => 'test2@test.com', 
        'user_phone' => '+36201234568',
        'locale' => 'hu',
    ]);
    
    expect($result1)->not->toBeNull();
    expect($result2)->toBeNull();
    expect(Appointment::count())->toBe(1);
});
```

Note: True concurrent testing requires multiple processes. The above tests sequential locking behavior. For true concurrency, use:
```bash
# Run in parallel (bash)
php artisan tinker --execute="app(SlotAvailabilityService::class)->reserveSlot(...)" &
php artisan tinker --execute="app(SlotAvailabilityService::class)->reserveSlot(...)" &
wait
```


---

## Execution Strategy

### Parallel Execution Waves

```
Wave 1 (Start Immediately):
├── Task 1: Install & configure Filament 5
├── Task 2: Install & configure Livewire 4
└── Task 3: Create ALL database migrations (12 files)

Wave 2 (After Wave 1):
├── Task 4: Create Eloquent models + enums
├── Task 5: Create factories & seeders
└── Task 6: Set up Hungarian translations

Wave 3 (After Wave 2):
├── Task 7: Build Filament resources (Services, Categories)
├── Task 8: Build Filament resources (Appointments, Schedules)
├── Task 9: Build Filament resources (Vouchers, Reviews, Photos)
└── Task 10: Build Settings page + Employee Profile

Wave 4 (After Wave 3):
├── Task 11: Build slot availability service
├── Task 12: Build Livewire booking wizard
├── Task 13: Build Barion integration
├── Task 14: Build email notifications
├── Task 15: Build magic link pages
└── Task 16: Build language switcher

Wave 5 (After Wave 4):
└── Task 17: Integration testing & polish
```

---

## TODOs

### Phase 1: Package Installation & Migrations

- [ ] 1. Install and Configure Filament 5

  **What to do**:
  - `composer require filament/filament:"^5.0"`
  - `php artisan filament:install --panels`
  - `php artisan make:filament-user` (create test admin)
  - Configure `app/Providers/Filament/AdminPanelProvider.php`
  - Publish Filament translations: `php artisan vendor:publish --tag=filament-panels-translations`
  - If Hungarian files not generated (Filament may only include common locales):
    1. Check if `lang/vendor/filament-panels/hu/` exists
    2. If NOT: Copy from `lang/vendor/filament-panels/en/` to `lang/vendor/filament-panels/hu/`
    3. Translate key UI strings (see below)
  - **Verify localization works**: After configuring panel with `->locale('hu')`, visit `/admin` and confirm UI shows Hungarian

  **Minimum Hungarian translations needed** (if creating manually):
  ```php
  // lang/vendor/filament-panels/hu/resources/pages/create-record.php
  return [
      'title' => 'Új :label létrehozása',
      'breadcrumb' => 'Új létrehozása',
  ];
  
  // lang/vendor/filament-panels/hu/resources/pages/edit-record.php
  return [
      'title' => ':label szerkesztése',
      'breadcrumb' => 'Szerkesztés',
  ];
  
  // lang/vendor/filament-panels/hu/resources/pages/list-records.php
  return [
      'title' => ':label lista',
      'breadcrumb' => 'Lista',
  ];
  ```

  **Also check/translate**: `lang/vendor/filament/hu/` for core components (buttons, modals, etc.)

  **Recommended Agent Profile**:
  - **Category**: `quick`
  - **Skills**: [`pest-testing`]

  **Parallelization**: Wave 1 (with Tasks 2, 3) | Blocks: 7-10 | Blocked By: None

  **References**:
  - Filament docs: https://filamentphp.com/docs/5.x/panels/installation

  **Acceptance Criteria**:
  - [ ] `composer show filament/filament` shows 5.x version
  - [ ] Admin panel loads at `/admin/login`
  - [ ] Commit: `feat(admin): install and configure Filament 5 panel`

---

- [ ] 2. Install and Configure Livewire 4

  **What to do**:
  - `composer require livewire/livewire:"^4.0"`
  - `php artisan livewire:publish --config`
  - Add `@livewireStyles` to `<head>` in `resources/views/layouts/page.blade.php`
  - Add `@livewireScripts` before `</body>`
  - Create test component to verify

  **Recommended Agent Profile**:
  - **Category**: `quick`
  - **Skills**: [`tailwindcss-development`]

  **Parallelization**: Wave 1 (with Tasks 1, 3) | Blocks: 11-16 | Blocked By: None

  **References**:
  - Livewire docs: https://livewire.laravel.com/docs/installation
  - `resources/views/layouts/page.blade.php`

  **Acceptance Criteria**:
  - [ ] `composer show livewire/livewire` shows 4.x version
  - [ ] Test Livewire component renders correctly
  - [ ] Commit: `feat(frontend): install and configure Livewire 4`

---

- [ ] 3. Create Database Migrations (12 files)

  **What to do**:
  Create 12 migration files with these exact timestamps and order:

  ```
  2024_01_01_000001_create_service_categories_table.php
  2024_01_01_000002_create_services_table.php
  2024_01_01_000003_create_schedules_table.php
  2024_01_01_000004_create_schedule_exceptions_table.php
  2024_01_01_000005_create_employee_profiles_table.php
  2024_01_01_000006_create_vouchers_table.php           -- BEFORE appointments (FK: appointments.voucher_id)
  2024_01_01_000007_create_appointments_table.php       -- AFTER vouchers, services
  2024_01_01_000008_create_transactions_table.php
  2024_01_01_000009_create_reviews_table.php
  2024_01_01_000010_create_reference_photos_table.php
  2024_01_01_000011_create_settings_table.php
  2024_01_01_000012_create_booking_tokens_table.php
  ```

  Use exact schemas from "Complete Database Schema Specification" section above.
  
  **CRITICAL**: FK order ensures parent tables exist before child tables:
  - `service_categories` → `services` (services.category_id FK)
  - `services` → `appointments` (appointments.service_id FK)
  - `vouchers` → `appointments` (appointments.voucher_id FK)
  - `appointments` → `booking_tokens`, `reviews` (child FKs)

  **Recommended Agent Profile**:
  - **Category**: `unspecified-low`
  - **Skills**: [`pest-testing`]

  **Parallelization**: Wave 1 (with Tasks 1, 2) | Blocks: 4, 5 | Blocked By: None

  **References**:
  - Complete schema in "Table Schemas" section above
  - `database/migrations/` existing patterns

  **Acceptance Criteria**:
  - [ ] 12 new migration files exist in `database/migrations/`
  - [ ] `php artisan migrate:fresh` completes without errors
  - [ ] All 12 new tables exist:
    ```bash
    php artisan tinker --execute="
      \$tables = ['service_categories','services','schedules','schedule_exceptions',
        'employee_profiles','appointments','vouchers','transactions',
        'reviews','reference_photos','settings','booking_tokens'];
      echo collect(\$tables)->every(fn(\$t) => Schema::hasTable(\$t)) ? 'ALL_OK' : 'MISSING';
    "
    # Expected output: ALL_OK
    ```
  - [ ] `vouchers` table has `used_at` column
  - [ ] Commit: `feat(database): add complete migration set for booking system`

---

### Phase 2: Models, Factories & Translations

- [x] 4. Create Eloquent Models and Enums

  **What to do**:
  
  Create 4 enum files in `app/Enums/`:
  - `AppointmentStatus.php`
  - `VoucherType.php`
  - `TransactionStatus.php`
  - `BookingTokenType.php`
  
  (Use exact definitions from "Enum Definitions" section)

  Create 12 model files in `app/Models/`:
  - `ServiceCategory.php` - hasMany services, hasMany referencePhotos
  - `Service.php` - belongsTo category, hasMany appointments
  - `Schedule.php` - scopes for day queries
  - `ScheduleException.php` - scopes for date queries
  - `EmployeeProfile.php` - singleton accessor
  - `Appointment.php` - belongsTo service, belongsTo voucher (nullable), hasMany tokens, morphOne transaction
  - `Voucher.php` - scopes for valid vouchers, balance methods
  - `Transaction.php` - morphTo payable
  - `Review.php` - belongsTo appointment (nullable)
  - `ReferencePhoto.php` - belongsTo category
  - `Setting.php` - static get/set helpers
  - `BookingToken.php` - belongsTo appointment, valid scope

  **Recommended Agent Profile**:
  - **Category**: `unspecified-low`
  - **Skills**: [`pest-testing`]

  **Parallelization**: Wave 2 (with Tasks 5, 6) | Blocks: 7-16 | Blocked By: Task 3

  **Acceptance Criteria**:
  - [ ] All enums defined correctly
  - [ ] All models have proper relationships
  - [ ] `Setting::get('key')` / `Setting::set('key', 'val')` work
  - [ ] `Voucher::valid('TESTCODE')->first()` scope works per Business Rules
  - [ ] Commit: `feat(models): create Eloquent models with relationships and enums`

---

- [ ] 5. Create Factories and Seeders

  **What to do**:
  Create factories for all models.
  Create seeders:
  - `ServiceCategorySeeder` - 3 categories: Szempilla, Smink, Szemoldok
  - `ScheduleSeeder` - Mon-Fri 9-17, Sat 9-14, Sun off
  - `SettingSeeder` - All 8 default settings + `slot_lock` key for advisory locking
  - `EmployeeProfileSeeder` - Single employee

  **Recommended Agent Profile**:
  - **Category**: `quick`
  - **Skills**: [`pest-testing`]

  **Parallelization**: Wave 2 (with Tasks 4, 6) | Blocks: 17 | Blocked By: Task 3

  **Acceptance Criteria**:
  - [ ] `php artisan migrate:fresh --seed` runs without errors
  - [ ] 3 categories, 7 schedules, **9 settings** (including slot_lock), 1 employee exist
  - [ ] Commit: `feat(database): add factories and seeders`

---

- [ ] 6. Set Up Hungarian Translations

  **What to do**:
  Create `lang/hu/` and `lang/en/` directories with:
  - `validation.php`, `pagination.php`, `auth.php`
  - `booking.php` - Custom booking UI strings
  - `emails.php` - Email content

  **Recommended Agent Profile**:
  - **Category**: `writing`
  - **Skills**: []

  **Parallelization**: Wave 2 (with Tasks 4, 5) | Blocks: 7-16 | Blocked By: Task 1

  **Acceptance Criteria**:
  - [ ] `__('booking.select_service')` returns correct Hungarian/English
  - [ ] Commit: `feat(i18n): add Hungarian and English translations`

---

### Phase 3: Filament Admin Resources

- [ ] 7-10. Build All Filament Resources

  (Tasks 7, 8, 9, 10 as detailed in previous plan sections)
  
  Create resources for: ServiceCategory, Service, Appointment, Schedule, ScheduleException, Voucher, Review, ReferencePhoto, Settings page, EmployeeProfile

  **Parallelization**: Wave 3 (parallel with each other) | Blocks: 17 | Blocked By: Tasks 4, 6

---

### Phase 4: Livewire Booking System

- [ ] 11-16. Build Booking System Components

  (Tasks 11-16 as detailed in previous plan sections)
  
  - SlotAvailabilityService with pessimistic locking
  - BookingWizard component (5 steps)
  - BarionService for payment
  - Email notifications (6 Mailables)
  - Magic link pages (cancel/reschedule/review)
  - Language switcher

  **Parallelization**: Wave 4 (parallel with each other) | Blocks: 17 | Blocked By: Wave 3

---

### Phase 5: Integration

- [ ] 17. Integration Testing and Polish

  Write comprehensive tests, fix bugs, verify E2E flow.

  **Parallelization**: Wave 5 (sequential) | Blocks: None | Blocked By: All

---

## Commit Strategy

| Task | Message |
|------|---------|
| 1 | `feat(admin): install and configure Filament 5 panel` |
| 2 | `feat(frontend): install and configure Livewire 4` |
| 3 | `feat(database): add complete migration set for booking system` |
| 4 | `feat(models): create Eloquent models with relationships and enums` |
| 5 | `feat(database): add factories and seeders` |
| 6 | `feat(i18n): add Hungarian and English translations` |
| 7 | `feat(admin): add Service and ServiceCategory resources` |
| 8 | `feat(admin): add Appointment, Schedule, ScheduleException resources` |
| 9 | `feat(admin): add Voucher, Review, ReferencePhoto resources` |
| 10 | `feat(admin): add Settings page and EmployeeProfile resource` |
| 11 | `feat(booking): implement slot availability service with locking` |
| 12 | `feat(booking): implement multi-step booking wizard component` |
| 13 | `feat(payment): implement Barion payment integration` |
| 14 | `feat(notifications): implement email notification system` |
| 15 | `feat(booking): implement magic link cancel/reschedule/review pages` |
| 16 | `feat(i18n): implement language switcher component` |
| 17 | `test(integration): add comprehensive E2E tests` |

---

## Success Criteria

```bash
# All migrations run
php artisan migrate:fresh --seed  # → Success, 12 new tables + seeded data

# All tests pass
php artisan test  # → All tests passing

# Admin accessible
curl -s http://localhost:8000/admin/login  # → 200 OK

# Booking accessible
curl -s http://localhost:8000/booking  # → 200 OK
```

### Final Checklist
- [ ] `migrate:fresh --seed` works
- [ ] All tests pass
- [ ] Admin panel in Hungarian
- [ ] Booking wizard completes flow
- [ ] Barion sandbox works
- [ ] Emails queued correctly
- [ ] Magic links work
- [ ] Language switcher works
- [ ] Mobile responsive

---

## Detailed Task Specifications (Tasks 7-17)

### Task 7: Filament Resources - Services & Categories

**Files to create**:
- `app/Filament/Resources/ServiceCategoryResource.php`
- `app/Filament/Resources/ServiceCategoryResource/Pages/ListServiceCategories.php`
- `app/Filament/Resources/ServiceCategoryResource/Pages/CreateServiceCategory.php`
- `app/Filament/Resources/ServiceCategoryResource/Pages/EditServiceCategory.php`
- `app/Filament/Resources/ServiceResource.php`
- `app/Filament/Resources/ServiceResource/Pages/ListServices.php`
- `app/Filament/Resources/ServiceResource/Pages/CreateService.php`
- `app/Filament/Resources/ServiceResource/Pages/EditService.php`

**ServiceCategoryResource form fields**:
```php
TextInput::make('name')->required()->maxLength(255),
TextInput::make('slug')->required()->unique(ignoreRecord: true),
Textarea::make('description'),
TextInput::make('sort_order')->numeric()->default(0),
```

**ServiceCategoryResource table columns**:
```php
TextColumn::make('name')->searchable()->sortable(),
TextColumn::make('slug'),
TextColumn::make('services_count')->counts('services'),
TextColumn::make('sort_order')->sortable(),
```

**ServiceResource form fields**:
```php
Select::make('category_id')->relationship('category', 'name'),
TextInput::make('name')->required(),
TextInput::make('slug')->required()->unique(ignoreRecord: true),
TextInput::make('duration_minutes')->numeric()->required()->suffix('perc'),
TextInput::make('buffer_minutes')->numeric()->nullable()->suffix('perc'),
TextInput::make('price')->numeric()->required()->suffix('Ft'),
TextInput::make('deposit_fee')->numeric()->default(0)->suffix('Ft'),
Textarea::make('description'),
Toggle::make('is_active')->default(true),
```

**ServiceResource table columns**:
```php
TextColumn::make('name')->searchable(),
TextColumn::make('category.name'),
TextColumn::make('duration_minutes')->suffix(' perc'),
TextColumn::make('price')->money('HUF'),
ToggleColumn::make('is_active'),
```

**Acceptance Criteria**:
- [ ] Visit `http://doraalfoldy_com.test/admin/service-categories` → list page loads
- [ ] Create category "Szempilla" → saved to DB
- [ ] Visit `http://doraalfoldy_com.test/admin/services` → list page loads
- [ ] Create service with category → saved with correct category_id
- [ ] Toggle is_active in table → updates DB immediately

---

### Task 8: Filament Resources - Appointments, Schedules, Exceptions

**Files to create**:
- `app/Filament/Resources/AppointmentResource.php` + Pages
- `app/Filament/Resources/ScheduleResource.php` + Pages
- `app/Filament/Resources/ScheduleExceptionResource.php` + Pages

**AppointmentResource form fields**:
```php
Select::make('service_id')->relationship('service', 'name')->required(),
TextInput::make('user_name')->required()->label('Ügyfél neve'),
TextInput::make('user_email')->email()->required(),
TextInput::make('user_phone')->tel()->required(),
DateTimePicker::make('start_time')->required(),
DateTimePicker::make('end_time')->required(),
Select::make('status')->options(AppointmentStatus::class)->required(),
Textarea::make('notes'),
```

**AppointmentResource table columns**:
```php
TextColumn::make('start_time')->dateTime('Y-m-d H:i')->sortable(),
TextColumn::make('user_name')->searchable(),
TextColumn::make('service.name'),
TextColumn::make('status')->badge()->color(fn($state) => match($state) {
    AppointmentStatus::PENDING => 'gray',
    AppointmentStatus::CONFIRMED => 'success',
    AppointmentStatus::CANCELLED => 'danger',
    AppointmentStatus::COMPLETED => 'info',
    AppointmentStatus::NO_SHOW => 'warning',
}),
```

**AppointmentResource filters**:
```php
SelectFilter::make('status')->options(AppointmentStatus::class),
SelectFilter::make('service_id')->relationship('service', 'name'),
Filter::make('date_range')->form([DatePicker::make('from'), DatePicker::make('until')]),
```

**ScheduleResource form fields**:
```php
Select::make('day_of_week')->options([
    0 => 'Vasárnap', 1 => 'Hétfő', 2 => 'Kedd', 3 => 'Szerda',
    4 => 'Csütörtök', 5 => 'Péntek', 6 => 'Szombat'
])->required(),
TimePicker::make('start_time')->required(),
TimePicker::make('end_time')->required()->after('start_time'),
Toggle::make('is_off')->label('Szabadnap'),
```

**ScheduleExceptionResource form fields**:
```php
DatePicker::make('date')->required()->unique(ignoreRecord: true),
TextInput::make('reason'),
Toggle::make('is_closed')->default(true)->reactive(),
TimePicker::make('custom_start_time')->visible(fn($get) => !$get('is_closed')),
TimePicker::make('custom_end_time')->visible(fn($get) => !$get('is_closed')),
```

**Acceptance Criteria**:
- [ ] Visit `http://doraalfoldy_com.test/admin/appointments` → list loads
- [ ] Appointment status badge shows correct colors
- [ ] Schedule end_time validation rejects end <= start
- [ ] ScheduleException custom hours fields only show when is_closed=false
- [ ] Filter appointments by status works

---

### Task 9: Filament Resources - Vouchers, Reviews, Photos

**Files to create**:
- `app/Filament/Resources/VoucherResource.php` + Pages
- `app/Filament/Resources/ReviewResource.php` + Pages
- `app/Filament/Resources/ReferencePhotoResource.php` + Pages

**VoucherResource form fields**:
```php
TextInput::make('code')->required()->unique(ignoreRecord: true)
    ->suffixAction(Action::make('generate')->icon('heroicon-o-sparkles')
        ->action(fn($set) => $set('code', strtoupper(Str::random(8))))),
Select::make('type')->options(VoucherType::class)->required()->reactive()
    ->afterStateUpdated(function ($state, $set) {
        // Reset balance when switching types
        if ($state !== 'gift_card') {
            $set('balance', 0);
        }
    }),
TextInput::make('value')->numeric()->required()
    ->suffix(fn($get) => $get('type') === 'percentage' ? '%' : 'Ft')
    ->helperText('For gift cards, this is the initial balance'),
TextInput::make('balance')->numeric()
    ->visible(fn($get) => $get('type') === 'gift_card')
    ->required(fn($get) => $get('type') === 'gift_card')
    ->default(0)
    ->helperText('Current remaining balance'),
TextInput::make('recipient_email')->email(),
DateTimePicker::make('expires_at'),
```

**VoucherResource mutateFormDataBeforeCreate hook** (set balance for gift cards):
```php
protected function mutateFormDataBeforeCreate(array $data): array
{
    // For percentage/fixed: balance is always 0
    // For gift_card: balance = value (initial balance equals face value) unless manually set
    if ($data['type'] === 'gift_card' && empty($data['balance'])) {
        $data['balance'] = $data['value'];
    } else if ($data['type'] !== 'gift_card') {
        $data['balance'] = 0;
    }
    return $data;
}
```

**ReviewResource form fields**:
```php
TextInput::make('customer_name')->required(),
TextInput::make('customer_email')->email()->required(),
Select::make('rating')->options([1 => '⭐', 2 => '⭐⭐', 3 => '⭐⭐⭐', 4 => '⭐⭐⭐⭐', 5 => '⭐⭐⭐⭐⭐'])->required(),
Textarea::make('content'),
Toggle::make('is_approved'),
```

**ReviewResource bulk actions**:
```php
BulkAction::make('approve')->action(fn(Collection $records) => $records->each->update(['is_approved' => true])),
```

**ReferencePhotoResource form fields**:
```php
Select::make('category_id')->relationship('category', 'name')->required(),
FileUpload::make('image_path')
    ->image()
    ->disk('public')  // Explicitly use public disk
    ->directory('photos')
    ->visibility('public')
    ->required(),
TextInput::make('caption'),
TextInput::make('sort_order')->numeric()->default(0),
```

**Storage Prerequisites** (must be done BEFORE Task 9):
```bash
# Create the storage symlink (one-time setup)
php artisan storage:link

# This creates: public/storage -> storage/app/public
# Uploaded photos will be accessible at: /storage/photos/filename.jpg
```

**Verify symlink exists**:
```bash
ls -la public/storage
# Should show: public/storage -> /path/to/storage/app/public
```

**If symlink doesn't work** (Windows or permission issues):
- Ensure `config/filesystems.php` has `'public'` disk configured with `'visibility' => 'public'`
- Files will be stored in `storage/app/public/photos/`
- Accessible via URL: `http://doraalfoldy_com.test/storage/photos/filename.jpg`

**Acceptance Criteria**:
- [ ] Voucher code auto-generate button works
- [ ] Voucher balance field only shows for gift_card type
- [ ] Review bulk approve updates all selected
- [ ] `php artisan storage:link` executed (symlink exists)
- [ ] Photo upload stores in `storage/app/public/photos/`
- [ ] Photo accessible via URL: `/storage/photos/{filename}`
- [ ] Photo thumbnail displays in table (via `ImageColumn::make('image_path')->disk('public')`)

---

### Task 10: Settings Page & Employee Profile

**Files to create**:
- `app/Filament/Pages/Settings.php`
- `app/Filament/Resources/EmployeeProfileResource.php` (single-record edit only)

**Settings page structure**:
```php
class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.settings';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill([
            'cancellation_hours' => Setting::get('cancellation_hours', '24'),
            'reminder_hours' => Setting::get('reminder_hours', '24'),
            // ... all 8 settings
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Foglalás beállítások')->schema([
                TextInput::make('cancellation_hours')->numeric()->required(),
                TextInput::make('reminder_hours')->numeric()->required(),
                TextInput::make('default_buffer_minutes')->numeric()->default(0),
            ]),
            Section::make('Oldal beállítások')->schema([
                TextInput::make('site_name')->required(),
                TextInput::make('admin_email')->email(),
                RichEditor::make('booking_terms'),
            ]),
            Section::make('Fizetés beállítások')->schema([
                TextInput::make('barion_pos_key')->password(),
                Toggle::make('barion_sandbox')->default(true),
            ]),
        ])->statePath('data');
    }
    
    public function save(): void
    {
        foreach ($this->data as $key => $value) {
            Setting::set($key, $value);
        }
        Notification::make()->success()->title('Beállítások mentve')->send();
    }
}
```

**EmployeeProfileResource**: Single record edit page that loads `EmployeeProfile::firstOrCreate([])`.

**Acceptance Criteria**:
- [ ] Visit `http://doraalfoldy_com.test/admin/settings` → page loads with current values
- [ ] Change cancellation_hours to 48, save → Setting::get('cancellation_hours') returns '48'
- [ ] Employee profile page loads single record
- [ ] Employee image upload works

---

### Task 11: Slot Availability Service (Detailed)

**File**: `app/Services/SlotAvailabilityService.php`

**Public methods**:
```php
public function getAvailableDates(Service $service, Carbon $from, Carbon $to): Collection
// Returns Collection of Carbon dates that have at least one available slot

public function getAvailableSlots(Service $service, Carbon $date): Collection
// Returns Collection of Carbon times (slot start times) for the given date

public function isSlotAvailable(Service $service, Carbon $startTime): bool
// Quick check if specific slot is available

public function reserveSlot(Service $service, Carbon $startTime, array $customerData): ?Appointment
// Creates PENDING appointment with pessimistic lock. Returns null if slot taken.

protected function getEffectiveBuffer(Service $service): int
// Returns service.buffer_minutes ?? Setting::get('default_buffer_minutes')

protected function getWorkingHours(Carbon $date): ?array
// Returns ['start' => Time, 'end' => Time] or null if closed
// Checks ScheduleException first, then Schedule for day_of_week
```

**Slot generation logic** (uses buffer for spacing, not storage):
```php
// Generate slots at (duration + buffer) intervals
// Buffer creates spacing between bookable slots
$slots = collect();
$current = $workStart->copy();
$buffer = $this->getEffectiveBuffer($service);
$slotSpacing = $service->duration_minutes + $buffer;  // Time between slot starts

while ($current->copy()->addMinutes($service->duration_minutes)->lte($workEnd)) {
    if ($this->isSlotAvailable($service, $current)) {
        $slots->push($current->copy());
    }
    $current->addMinutes($slotSpacing);  // Next slot starts after duration + buffer
}
```

**Buffer Example**:
- Working hours: 9:00-17:00
- Service: 60min + 10min buffer
- Slots generated: 9:00, 10:10, 11:20, 12:30, etc.
- If 10:10 booked: appointment.end_time = 11:10 (no buffer in DB)

**Acceptance Criteria**:
- [ ] `getAvailableSlots()` respects Schedule working hours
- [ ] `getAvailableSlots()` excludes slots with existing PENDING/CONFIRMED appointments
- [ ] `getAvailableSlots()` respects buffer time
- [ ] `getAvailableSlots()` returns empty for closed days (is_off or exception is_closed)
- [ ] `reserveSlot()` with locking prevents double-booking (sequential test)

---

### Task 12: Livewire Booking Wizard (Detailed)

**File**: `app/Livewire/BookingWizard.php`
**View**: `resources/views/livewire/booking-wizard.blade.php`
**Route**: `Route::get('/booking', BookingWizard::class)->name('booking');`

### Livewire Full-Page Component Layout Integration

**This repo uses**: `resources/views/layouts/page.blade.php` - a TRADITIONAL section-based layout with `@yield('page')`

**VERIFIED actual layout structure** (from file inspection):
```blade
<!-- resources/views/layouts/page.blade.php uses: -->
@yield('page')  <!-- NOT $slot -->
@stack('head')  <!-- In <head> -->
@stack('scripts')  <!-- Before </body> -->
```

**Required changes to page.blade.php**:
```blade
<!-- ADD @livewireStyles to @stack('head') location OR directly in <head>: -->
<head>
    ...
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles  {{-- ADD THIS LINE --}}
    @stack('head')
</head>

<!-- ADD @livewireScripts before </body>: -->
    @stack('scripts')
    @livewireScripts  {{-- ADD THIS LINE --}}
</body>
```

**Livewire full-page components with section-based layouts**:

For Livewire 4 with a `@yield()` layout, use the `layout` property with a wrapper view:

**Option 1 (Recommended): Create a Livewire-specific layout wrapper**:
```bash
# Create resources/views/layouts/livewire.blade.php
```
```blade
{{-- resources/views/layouts/livewire.blade.php --}}
@extends('layouts.page')

@section('page')
    {{ $slot }}
@endsection
```

**Then use in components**:
```php
// app/Livewire/BookingWizard.php
use Livewire\Attributes\Layout;

#[Layout('layouts.livewire')]
class BookingWizard extends Component
{
    // ... component code
}

// Same for CancelBooking.php, RescheduleBooking.php, SubmitReview.php
#[Layout('layouts.livewire')]
class CancelBooking extends Component { ... }
```

**Payment return pages** (regular Blade views from BookingController):
```blade
{{-- resources/views/booking/payment-success.blade.php --}}
@extends('layouts.page')

@section('page')
    <div class="max-w-2xl mx-auto p-6">
        <h1>{{ __('booking.payment_success') }}</h1>
        ...
    </div>
@endsection
```

**Files to create**:
- `resources/views/layouts/livewire.blade.php` (wrapper layout for Livewire components)
- `resources/views/booking/payment-success.blade.php`
- `resources/views/booking/payment-failed.blade.php`
- `resources/views/booking/payment-processing.blade.php`

**Component properties**:
```php
public int $step = 1;
public ?int $serviceId = null;
public ?string $selectedDate = null;
public ?string $selectedTime = null;
public string $userName = '';
public string $userEmail = '';
public string $userPhone = '';
public string $notes = '';
public string $voucherCode = '';
public ?Voucher $appliedVoucher = null;
public float $voucherDiscount = 0;
public bool $acceptTerms = false;
```

**Step 1 view** - Services grouped by category:
```blade
@foreach($categories as $category)
    <h3>{{ $category->name }}</h3>
    @foreach($category->services->where('is_active', true) as $service)
        <button wire:click="selectService({{ $service->id }})">
            {{ $service->name }} - {{ $service->duration_minutes }} perc - {{ number_format($service->price) }} Ft
        </button>
    @endforeach
@endforeach
```

**Step 2 view** - Calendar with available dates (next 30 days)
**Step 3 view** - Time slot grid
**Step 4 view** - Customer form + voucher + price breakdown
**Step 5 view** - Summary + Pay button

**Price breakdown calculation**:
```php
public function getPriceBreakdownProperty(): array
{
    $service = Service::find($this->serviceId);
    $total = $service->price;
    $discount = $this->voucherDiscount;
    $deposit = $service->deposit_fee;
    $remaining = $total - $discount - $deposit;
    
    return [
        'total' => $total,           // Szolgáltatás ára
        'discount' => $discount,     // Kupon kedvezmény
        'deposit' => $deposit,       // Előleg (most fizetendő)
        'remaining' => max(0, $remaining), // Fennmaradó (helyszínen)
    ];
}
```

**Step 5 "Pay" action → Barion redirect flow**:
```php
public function initiatePayment(): void
{
    // 1. Reserve the slot (creates PENDING appointment)
    $service = Service::find($this->serviceId);
    
    // Pass ONLY allowed customer data keys (buffer_at_booking is computed internally)
    $appointment = app(SlotAvailabilityService::class)->reserveSlot(
        $service,
        Carbon::parse($this->selectedDate . ' ' . $this->selectedTime),
        [
            'user_name' => $this->userName,
            'user_email' => $this->userEmail,
            'user_phone' => $this->userPhone,
            'notes' => $this->notes,
            'locale' => app()->getLocale(),
            'voucher_id' => $this->appliedVoucher?->id,
            'voucher_discount' => $this->voucherDiscount,
            // NOTE: buffer_at_booking is computed inside reserveSlot(), not passed here
        ]
    );
    
    if (!$appointment) {
        $this->addError('slot', __('booking.slot_taken'));
        $this->step = 3; // Back to time selection
        return;
    }
    
    // 2. Store appointment ID in session for payment return handling
    session(['pending_appointment_id' => $appointment->id]);
    
    // 3. Create Barion payment and redirect
    $barionUrl = app(BarionService::class)->createPayment($appointment);
    
    $this->redirect($barionUrl);
}
```

**Acceptance Criteria**:
- [ ] Visit `http://doraalfoldy_com.test/booking` → Step 1 loads with services
- [ ] Select service → moves to Step 2
- [ ] Select date with slots → moves to Step 3
- [ ] Select time → moves to Step 4
- [ ] Apply valid voucher → discount calculated correctly
- [ ] Apply invalid voucher → error shown
- [ ] Fill form without terms → cannot proceed
- [ ] Fill form with terms → moves to Step 5
- [ ] Mobile responsive (test at 375px width)

---

### Task 13: Barion Integration (Detailed)

**Files to create**:
- `app/Services/BarionService.php` - Payment logic
- `app/Http/Controllers/BarionController.php` - Webhook handler
- `app/Http/Controllers/BookingController.php` - Payment success/failed pages
- `routes/api.php` - New file for API routes

**API Routing Setup (CRITICAL - repo currently has no routes/api.php)**:

1. Create `routes/api.php`:
```php
<?php
use App\Http\Controllers\BarionController;
use Illuminate\Support\Facades\Route;

// Barion webhook callback - POST only, no CSRF, no auth
Route::post('/barion/callback', [BarionController::class, 'callback'])->name('barion.callback');
```

2. Register API routes in `bootstrap/app.php`:
```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',  // ADD THIS LINE
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

**Final Barion Callback URL**: `http://doraalfoldy_com.test/api/barion/callback`
(Laravel's API routes are prefixed with `/api` by default)

**VERIFICATION STEP (REQUIRED)**: After registering API routes, confirm the callback URL:
```bash
php artisan route:list --name=barion.callback
# Expected output should show:
# POST  api/barion/callback  barion.callback  App\Http\Controllers\BarionController@callback

# If prefix differs from /api, update BarionService::createPayment() CallbackUrl accordingly
```

---

### Barion Redirect/Return Contract (EXPLICIT)

**Documentation References** (specific pages):
- Payment/Start API: https://docs.barion.com/Payment-Start-v2
- Payment states and flow: https://docs.barion.com/Payment_lifecycle
- Redirect behavior: https://docs.barion.com/Redirect_mechanism

**RedirectUrl Behavior** (from Barion docs):
- Barion uses a **single RedirectUrl** for all outcomes (success, cancel, fail)
- On redirect, Barion appends `?paymentId={GUID}` query parameter
- The **parameter name is `paymentId`** (lowercase 'p', lowercase 'i')
- Customer may also click "Cancel" on Barion page → redirected to same URL

**How to distinguish success vs failure on redirect**:
1. Customer arrives at `RedirectUrl?paymentId=xxx`
2. We look up the transaction by `paymentId`
3. Check transaction status (set by webhook, or fetch from Barion API if webhook hasn't arrived)
4. Show appropriate page based on status

**No Separate Fail URL**: Barion does NOT support separate success/fail redirect URLs. Handle all outcomes at the single RedirectUrl.

---

### Barion Callback Contract (EXPLICIT)

**What Barion Sends** (per Barion API docs):
- HTTP Method: POST
- Content-Type: `application/x-www-form-urlencoded`
- Body parameter: `PaymentId` (GUID string, e.g., `a1b2c3d4-e5f6-7890-abcd-ef1234567890`)

**Example Request from Barion**:
```http
POST /api/barion/callback HTTP/1.1
Content-Type: application/x-www-form-urlencoded

PaymentId=a1b2c3d4-e5f6-7890-abcd-ef1234567890
```

**Controller Implementation**:
```php
// BarionController.php
public function callback(Request $request): Response
{
    $paymentId = $request->input('PaymentId');
    
    if (!$paymentId) {
        Log::warning('Barion callback missing PaymentId');
        return response('Missing PaymentId', 400);
    }
    
    $success = app(BarionService::class)->handleCallback($paymentId);
    
    // Barion expects HTTP 200 for success
    // Any non-2xx triggers retry (up to 5 retries over 24 hours)
    return response($success ? 'OK' : 'Error', $success ? 200 : 500);
}
```

**Authenticity Verification**:
- Barion does NOT sign callbacks; authenticity is verified by:
  1. Only we know the PaymentId (generated by our Payment/Start call)
  2. We call Barion's GET `/v2/Payment/GetPaymentState` to verify status
  3. If PaymentId is unknown to Barion → forged callback → ignore
- The `handleCallback()` method always fetches current status from Barion:
```php
public function handleCallback(string $paymentId): bool
{
    // Always verify with Barion - don't trust callback alone
    $barionStatus = $this->getPaymentStatus($paymentId);  // Calls Barion API
    
    if (!$barionStatus) {
        Log::warning("Unknown PaymentId in callback: {$paymentId}");
        return false;  // Return 500, Barion will retry
    }
    
    // ... process status ...
}
```

**Retry Behavior**:
- Barion retries if non-2xx response
- Up to 5 retries over 24 hours
- Our callback is idempotent (checks if already processed)

**What We Return**:
| Scenario | HTTP Status | Body | Barion Action |
|----------|-------------|------|---------------|
| Success | 200 | "OK" | No retry |
| Unknown PaymentId | 500 | "Error" | Retry |
| Already processed | 200 | "OK" | No retry |
| DB error | 500 | "Error" | Retry |

**Web routes** (add to `routes/web.php` inside booking middleware group):
```php
Route::get('/booking/payment/success', [BookingController::class, 'paymentSuccess'])->name('booking.payment.success');
Route::get('/booking/payment/failed', [BookingController::class, 'paymentFailed'])->name('booking.payment.failed');
Route::get('/booking/payment/status/{appointment}', [BookingController::class, 'paymentStatus'])->name('booking.payment.status');
```

**CSRF Handling**: API middleware group does NOT include CSRF verification, allowing Barion's server-to-server callback.

### Barion Configuration Contract

**Base URLs**:
```php
// BarionService.php
protected function getBaseUrl(): string
{
    return (bool) Setting::get('barion_sandbox', true)
        ? 'https://api.test.barion.com'   // Sandbox
        : 'https://api.barion.com';        // Production
}
```

**Required Settings** (validated on BarionService instantiation):
```php
public function __construct()
{
    $this->posKey = Setting::get('barion_pos_key');
    
    if (empty($this->posKey)) {
        throw new \RuntimeException('Barion POSKey not configured. Set it in Admin → Settings.');
    }
}
```

**Payee Field**:
- Barion's `Payee` field requires the **Barion account email** (the merchant's login email), NOT a generic admin notification email.
- For this implementation, we assume `Setting::get('admin_email')` IS the Barion merchant account email.
- If they differ, add a separate `barion_payee_email` setting.

**Current approach** (single email):
```php
'Payee' => Setting::get('admin_email'),  // Must be the Barion merchant account email
```

**Payment creation flow**:
```php
public function createPayment(Appointment $appointment): string
{
    $response = Http::post($this->baseUrl . '/v2/Payment/Start', [
        'POSKey' => $this->posKey,
        'PaymentType' => 'Immediate',
        'PaymentRequestId' => 'BOOKING-' . $appointment->id . '-' . time(),
        'Transactions' => [[
            'POSTransactionId' => 'TXN-' . $appointment->id,
            'Payee' => Setting::get('admin_email'),
            'Total' => $appointment->deposit_at_booking,
            'Items' => [[
                'Name' => $appointment->service->name . ' - Előleg',
                'Quantity' => 1,
                'Unit' => 'db',
                'UnitPrice' => $appointment->deposit_at_booking,
                'ItemTotal' => $appointment->deposit_at_booking,
            ]],
        ]],
        'RedirectUrl' => route('booking.payment.success'),
        'CallbackUrl' => route('barion.callback'),
        'Locale' => $appointment->locale === 'hu' ? 'hu-HU' : 'en-US',
    ]);
    
    $data = $response->json();
    
    // Create Transaction record
    Transaction::create([
        'payment_id' => $data['PaymentId'],
        'status' => TransactionStatus::PENDING,
        'amount' => $appointment->deposit_at_booking,
        'payable_type' => Appointment::class,
        'payable_id' => $appointment->id,
    ]);
    
    return $data['GatewayUrl'];
}
```

**Webhook callback** (idempotent):
```php
public function handleCallback(string $paymentId): bool
{
    $transaction = Transaction::where('payment_id', $paymentId)->first();
    if (!$transaction || $transaction->status !== TransactionStatus::PENDING) {
        return true; // Already processed or unknown
    }
    
    $barionStatus = $this->getPaymentStatus($paymentId);
    $newStatus = $this->mapTransactionStatus($barionStatus);
    
    $transaction->update([
        'status' => $newStatus,
        'barion_status' => $barionStatus,
    ]);
    
    $appointment = $transaction->payable;
    if ($newStatus === TransactionStatus::COMPLETED) {
        $appointment->update(['status' => AppointmentStatus::CONFIRMED]);
        
        // Consume voucher using stored voucher_id (not voucher_code)
        if ($appointment->voucher_id && $appointment->voucher_discount > 0) {
            $voucher = Voucher::find($appointment->voucher_id);
            if ($voucher) {
                $this->consumeVoucher($voucher, $appointment->voucher_discount);
            }
        }
        
        $this->createMagicTokens($appointment);
        Mail::to($appointment->user_email)->queue(new BookingConfirmation($appointment));
        Mail::to(Setting::get('admin_email'))->queue(new NewBookingNotification($appointment));
    } else {
        $appointment->update(['status' => AppointmentStatus::CANCELLED]);
    }
    
    return true;
}
```

**Payment Return Pages (Handle Webhook Timing + Session Loss)**:

When customer returns from Barion, webhook may or may not have arrived yet. Also, session may be lost (different device, cleared cookies).

**Session Loss Fallback**: Use Barion's `PaymentId` query parameter (sent on redirect) to look up the transaction/appointment directly.

```php
// BookingController.php
public function paymentSuccess(Request $request): View
{
    // Try session first, then fall back to Barion PaymentId query param
    $appointmentId = session('pending_appointment_id');
    $appointment = null;
    
    if ($appointmentId) {
        $appointment = Appointment::find($appointmentId);
    }
    
    // Fallback: Barion sends PaymentId as query param on redirect
    if (!$appointment && $request->has('PaymentId')) {
        $transaction = Transaction::where('payment_id', $request->query('PaymentId'))->first();
        $appointment = $transaction?->payable;
    }
    
    if (!$appointment) {
        return view('booking.payment-error', ['message' => __('booking.appointment_not_found')]);
    }
    
    $transaction = $appointment->transaction;
    
    // Case 1: Webhook already processed - show final status
    if ($transaction && $transaction->status === TransactionStatus::COMPLETED) {
        session()->forget('pending_appointment_id');
        return view('booking.payment-success', ['appointment' => $appointment]);
    }
    
    // Case 2: Webhook already processed but failed
    if ($transaction && $transaction->status === TransactionStatus::FAILED) {
        session()->forget('pending_appointment_id');
        return view('booking.payment-failed', ['appointment' => $appointment]);
    }
    
    // Case 3: Webhook not yet arrived - show processing state with polling
    return view('booking.payment-processing', [
        'appointment' => $appointment,
        'pollUrl' => route('booking.payment.status', ['id' => $appointment->id]),
    ]);
}

public function paymentStatus(Appointment $appointment): JsonResponse
{
    // AJAX endpoint for polling
    $transaction = $appointment->transaction;
    
    return response()->json([
        'status' => $transaction?->status->value ?? 'pending',
        'appointmentStatus' => $appointment->status->value,
    ]);
}
```

**Add polling route** to `routes/web.php` (inside booking middleware group):
```php
Route::get('/booking/payment/status/{appointment}', [BookingController::class, 'paymentStatus'])
    ->name('booking.payment.status');
```

**Processing view** (`resources/views/booking/payment-processing.blade.php`):
```blade
<div x-data="{ checking: true }" x-init="
    const poll = setInterval(async () => {
        const res = await fetch('{{ $pollUrl }}');
        const data = await res.json();
        if (data.status === 'completed') {
            window.location.href = '{{ route('booking.payment.success') }}';
        } else if (data.status === 'failed') {
            window.location.href = '{{ route('booking.payment.failed') }}';
        }
    }, 2000);
    setTimeout(() => clearInterval(poll), 60000); // Stop after 60s
">
    <div class="text-center">
        <div class="animate-spin ..."></div>
        <p>{{ __('booking.processing_payment') }}</p>
        <p class="text-sm text-gray-500">{{ __('booking.please_wait') }}</p>
    </div>
</div>
```

**Result**: Customer always sees appropriate state - immediate success/failure if webhook was fast, or a "processing" spinner that polls until webhook arrives.

**Acceptance Criteria**:
- [ ] `createPayment()` returns valid Barion URL (mock HTTP in test)
- [ ] Transaction record created with PENDING status
- [ ] Webhook updates transaction and appointment status
- [ ] Webhook is idempotent (calling twice doesn't double-process)
- [ ] Success page shows confirmation when webhook completed
- [ ] Failed page shows error and retry option
- [ ] Processing page shows spinner and polls `/booking/payment/status/{id}`
- [ ] Poll redirects to success/failed when status changes

---

### Task 14: Email Notifications (Detailed)

**6 Mailable classes** in `app/Mail/`:

| Class | Trigger | Recipient | Content |
|-------|---------|-----------|---------|
| `BookingConfirmation` | Payment success | Customer | Appointment details, cancel/reschedule magic links |
| `AppointmentReminder` | Scheduled (X hrs before) | Customer | Appointment details, magic links |
| `CancellationConfirmation` | Customer cancels | Customer | Cancelled details, rebook link |
| `RescheduleConfirmation` | Customer reschedules | Customer | Old time (crossed), new time, new magic links |
| `NewBookingNotification` | Payment success | Admin | Customer info, appointment details, admin link |
| `ReviewRequest` | Scheduled (after completed) | Customer | "How was your visit?", review magic link |

**Scheduled commands** in `routes/console.php`:
```php
// Send reminders X hours before
Schedule::command('booking:send-reminders')->hourly();

// Send review requests for completed appointments
Schedule::command('booking:send-review-requests')->dailyAt('10:00');

// Cancel stale PENDING appointments (>30 min old)
Schedule::command('booking:cancel-stale')->everyFiveMinutes();
```

**Command: `SendReminders`** (`app/Console/Commands/SendReminders.php`):
```php
$reminderHours = (int) Setting::get('reminder_hours', 24);
$targetTime = now()->addHours($reminderHours);

Appointment::where('status', AppointmentStatus::CONFIRMED)
    ->whereBetween('start_time', [$targetTime, $targetTime->copy()->addHour()])
    ->whereNull('reminder_sent_at')  // Use reminder_sent_at column (not booking token)
    ->each(function ($appointment) {
        Mail::to($appointment->user_email)->queue(new AppointmentReminder($appointment));
        $appointment->update(['reminder_sent_at' => now()]);  // Mark as sent
    });
```

**Reminder Tracking**: Uses `appointments.reminder_sent_at` column (added in schema). When NULL = not sent. Set to timestamp when reminder is sent. This prevents duplicate reminders without adding complexity to the booking token system.

**Acceptance Criteria**:
- [ ] BookingConfirmation includes cancel and reschedule URLs
- [ ] All emails implement ShouldQueue
- [ ] AppointmentReminder command finds appointments due in X hours
- [ ] ReviewRequest only sent to COMPLETED appointments
- [ ] CancelStale command updates PENDING >30min to CANCELLED

---

### Task 15: Magic Link Pages (Detailed)

**Routes** in `routes/web.php`:
```php
Route::get('/booking/{token}/cancel', CancelBooking::class)->name('booking.cancel');
Route::get('/booking/{token}/reschedule', RescheduleBooking::class)->name('booking.reschedule');
Route::get('/booking/{token}/review', SubmitReview::class)->name('booking.review');
```

**CancelBooking component logic**:
```php
public function mount(string $token): void
{
    $this->bookingToken = BookingToken::where('token', $token)
        ->where('type', BookingTokenType::CANCEL)
        ->first();
    
    if (!$this->bookingToken) {
        $this->error = __('booking.token_invalid');
        return;
    }
    if ($this->bookingToken->expires_at < now()) {
        $this->error = __('booking.token_expired');
        return;
    }
    if ($this->bookingToken->used_at) {
        $this->error = __('booking.token_used');
        return;
    }
    
    $this->appointment = $this->bookingToken->appointment;
    $cancellationHours = (int) Setting::get('cancellation_hours', 24);
    
    if ($cancellationHours > 0) {
        $deadline = $this->appointment->start_time->subHours($cancellationHours);
        if (now() > $deadline) {
            $hoursLeft = now()->diffInHours($this->appointment->start_time);
            $this->error = __('booking.cancellation_not_allowed', ['hours' => $hoursLeft]);
            return;
        }
    }
    
    $this->canCancel = true;
}

public function confirmCancel(): void
{
    $this->appointment->update(['status' => AppointmentStatus::CANCELLED]);
    $this->bookingToken->update(['used_at' => now()]);
    Mail::to($this->appointment->user_email)->queue(new CancellationConfirmation($this->appointment));
    $this->cancelled = true;
}
```

**RescheduleBooking**: Same validation, plus date/time picker to select new slot, then updates appointment times and generates new tokens.

**SubmitReview**: Validates review token (no expiry check by design - reviews can be submitted anytime), shows star rating + textarea, creates Review with is_approved=false.

### Review Token Lifecycle (EXPLICIT)

**When Created**: Review tokens are created by the `SendReviewRequests` scheduled command, NOT at booking confirmation.

**Why Separate**: Sending review request immediately after booking makes no sense - customer hasn't had the appointment yet. We wait until:
1. Appointment status = COMPLETED
2. Appointment end_time has passed
3. No review token exists yet

**Command: `SendReviewRequests`** (`app/Console/Commands/SendReviewRequests.php`):
```php
// Run daily at 10:00 AM (defined in routes/console.php)
public function handle(): void
{
    // Find completed appointments without review tokens
    Appointment::where('status', AppointmentStatus::COMPLETED)
        ->where('end_time', '<', now())  // Already finished
        ->whereDoesntHave('bookingTokens', function ($q) {
            $q->where('type', BookingTokenType::REVIEW);
        })
        ->each(function ($appointment) {
            // Create review token (no expiry - can review anytime)
            $token = BookingToken::create([
                'appointment_id' => $appointment->id,
                'token' => Str::uuid(),
                'type' => BookingTokenType::REVIEW,
                'expires_at' => now()->addYears(10),  // Effectively never expires
            ]);
            
            // Send email with review link
            Mail::to($appointment->user_email)->queue(new ReviewRequest($appointment, $token));
        });
}
```

**SubmitReview Expiry Handling**:
```php
// Review tokens skip expiry check - intentionally allow late reviews
public function mount(string $token): void
{
    $this->bookingToken = BookingToken::where('token', $token)
        ->where('type', BookingTokenType::REVIEW)
        ->first();
    
    if (!$this->bookingToken) {
        $this->error = __('booking.token_invalid');
        return;
    }
    // NOTE: No expires_at check for review tokens
    if ($this->bookingToken->used_at) {
        $this->error = __('booking.review_already_submitted');
        return;
    }
    
    $this->appointment = $this->bookingToken->appointment;
    $this->canReview = true;
}
```

**Token creation** (on successful payment):
```php
private function createMagicTokens(Appointment $appointment): void
{
    // Cancel token - expires 72h after appointment
    BookingToken::create([
        'appointment_id' => $appointment->id,
        'token' => Str::uuid(),
        'type' => BookingTokenType::CANCEL,
        'expires_at' => $appointment->start_time->addHours(72),
    ]);
    
    // Reschedule token - same expiry
    BookingToken::create([
        'appointment_id' => $appointment->id,
        'token' => Str::uuid(),
        'type' => BookingTokenType::RESCHEDULE,
        'expires_at' => $appointment->start_time->addHours(72),
    ]);
    
    // Review token - created after appointment is COMPLETED (separate process)
}
```

**Acceptance Criteria**:
- [ ] Invalid token shows error message
- [ ] Expired token shows expiry error
- [ ] Used token shows "already used" error
- [ ] Cancel within window → appointment cancelled, email sent
- [ ] Cancel outside window → error with hours remaining
- [ ] Reschedule updates times and creates new tokens
- [ ] Review creates unapproved Review record

---

### Task 16: Language Switcher (Detailed)

**Middleware**: `app/Http/Middleware/SetLocale.php`
```php
public function handle(Request $request, Closure $next): Response
{
    if ($request->has('lang')) {
        $locale = in_array($request->query('lang'), ['hu', 'en']) 
            ? $request->query('lang') 
            : 'hu';
        session(['locale' => $locale]);
    }
    
    App::setLocale(session('locale', 'hu'));
    
    return $next($request);
}
```

**Register in `bootstrap/app.php`** (booking-only group, NOT global web middleware):
```php
->withMiddleware(function (Middleware $middleware) {
    // DO NOT append SetLocale to global 'web' middleware
    // Instead, create a booking-specific middleware group
    $middleware->group('booking', [
        \App\Http\Middleware\SetLocale::class,
    ]);
})
```

**Apply to routes in `routes/web.php`**:
```php
Route::middleware('booking')->group(function () {
    Route::get('/booking', BookingWizard::class)->name('booking');
    Route::get('/booking/{token}/cancel', CancelBooking::class)->name('booking.cancel');
    Route::get('/booking/{token}/reschedule', RescheduleBooking::class)->name('booking.reschedule');
    Route::get('/booking/{token}/review', SubmitReview::class)->name('booking.review');
    Route::get('/booking/payment/success', [BookingController::class, 'paymentSuccess'])->name('booking.payment.success');
    Route::get('/booking/payment/failed', [BookingController::class, 'paymentFailed'])->name('booking.payment.failed');
});
```

**Result**: Admin panel (`/admin/*`) is unaffected - always uses `locale('hu')` from panel config. Booking routes get locale switching via `?lang=` parameter.

**Blade component**: `resources/views/components/language-switcher.blade.php`
```blade
<div class="flex gap-2">
    <a href="{{ request()->fullUrlWithQuery(['lang' => 'hu']) }}" 
       class="{{ app()->getLocale() === 'hu' ? 'font-bold' : '' }}">HU</a>
    <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
       class="{{ app()->getLocale() === 'en' ? 'font-bold' : '' }}">EN</a>
</div>
```

**Acceptance Criteria**:
- [ ] Visit `/booking?lang=en` → session has locale=en
- [ ] Visit `/booking` again → still English
- [ ] Click HU → switches to Hungarian
- [ ] Booking UI shows translated text

---

### Task 17: Integration Testing (Detailed)

**Test files to create**:
- `tests/Feature/BookingFlowTest.php` - Full wizard flow
- `tests/Feature/CancelBookingTest.php` - Cancel magic link
- `tests/Feature/RescheduleBookingTest.php` - Reschedule magic link
- `tests/Feature/ReviewSubmissionTest.php` - Review magic link
- `tests/Feature/BarionWebhookTest.php` - Payment callbacks
- `tests/Feature/SlotAvailabilityTest.php` - Availability edge cases

**E2E booking flow test**:
```php
it('completes full booking flow', function () {
    // Setup
    $category = ServiceCategory::factory()->create(['name' => 'Szempilla']);
    $service = Service::factory()->create([
        'category_id' => $category->id,
        'duration_minutes' => 60,
        'price' => 15000,
        'deposit_fee' => 5000,
    ]);
    Schedule::factory()->create([
        'day_of_week' => now()->addDay()->dayOfWeek,
        'start_time' => '09:00',
        'end_time' => '17:00',
    ]);
    
    // Execute wizard
    Livewire::test(BookingWizard::class)
        ->call('selectService', $service->id)
        ->assertSet('step', 2)
        ->call('selectDate', now()->addDay()->format('Y-m-d'))
        ->assertSet('step', 3)
        ->call('selectTime', '10:00')
        ->assertSet('step', 4)
        ->set('userName', 'Test User')
        ->set('userEmail', 'test@example.com')
        ->set('userPhone', '+36201234567')
        ->set('acceptTerms', true)
        ->call('proceedToPayment')
        ->assertSet('step', 5);
    
    // Verify appointment created
    expect(Appointment::where('user_email', 'test@example.com')->exists())->toBeTrue();
});
```

**Acceptance Criteria**:
- [ ] `php artisan test` passes all tests
- [ ] Booking flow test covers all 5 steps
- [ ] Cancel test verifies window enforcement
- [ ] Barion webhook test verifies idempotency
- [ ] No N+1 queries (check with `DB::enableQueryLog()`)

---

## Updated Success Criteria

### Verification Commands (Correct URLs)
```bash
# All migrations run
php artisan migrate:fresh --seed
# Expected: Success

# All tests pass
php artisan test
# Expected: All passing

# Admin accessible (Herd URL)
curl -s -o /dev/null -w "%{http_code}" http://doraalfoldy_com.test/admin/login
# Expected: 200

# Booking accessible (Herd URL)
curl -s -o /dev/null -w "%{http_code}" http://doraalfoldy_com.test/booking
# Expected: 200
```
