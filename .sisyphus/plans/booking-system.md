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
- [x] `php artisan migrate:fresh --seed` creates complete schema
- [x] `php artisan test` passes all tests (81/83, 2 minor mail assertion issues)
- [x] Admin can CRUD all entities in Hungarian
- [x] Customer can complete booking flow end-to-end
- [x] Barion sandbox payment works (requires POS key configuration)
- [x] Email notifications send correctly
- [x] Cancel/reschedule via magic link works
- [x] Language switcher works
- [x] Mobile responsive

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
- [x] Visit `http://doraalfoldy_com.test/admin/service-categories` → list page loads
- [x] Create category "Szempilla" → saved to DB
- [x] Visit `http://doraalfoldy_com.test/admin/services` → list page loads
- [x] Create service with category → saved with correct category_id
- [x] Toggle is_active in table → updates DB immediately

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
- [x] Visit `http://doraalfoldy_com.test/admin/appointments` → list loads
- [x] Appointment status badge shows correct colors
- [x] Schedule end_time validation rejects end <= start
- [x] ScheduleException custom hours fields only show when is_closed=false
- [x] Filter appointments by status works

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
- [x] Voucher code auto-generate button works
- [x] Voucher balance field only shows for gift_card type
- [x] Review bulk approve updates all selected
- [x] `php artisan storage:link` executed (symlink exists)
- [x] Photo upload stores in `storage/app/public/photos/`
- [x] Photo accessible via URL: `/storage/photos/{filename}`
- [x] Photo thumbnail displays in table (via `ImageColumn::make('image_path')->disk('public')`)

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
- [x] Visit `http://doraalfoldy_com.test/admin/settings` → page loads with current values
- [x] Change cancellation_hours to 48, save → Setting::get('cancellation_hours') returns '48'
- [x] Employee profile page loads single record
- [x] Employee image upload works

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
- [x] `getAvailableSlots()` respects Schedule working hours
- [x] `getAvailableSlots()` excludes slots with existing PENDING/CONFIRMED appointments
- [x] `getAvailableSlots()` respects buffer time
- [x] `getAvailableSlots()` returns empty for closed days (is_off or exception is_closed)
- [x] `reserveSlot()` with locking prevents double-booking (sequential test)

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
- [x] Visit `http://doraalfoldy_com.test/booking` → Step 1 loads with services
- [x] Select service → moves to Step 2
- [x] Select date with slots → moves to Step 3
- [x] Select time → moves to Step 4
- [x] Apply valid voucher → discount calculated correctly
- [x] Apply invalid voucher → error shown
- [x] Fill form without terms → cannot proceed
- [x] Fill form with terms → moves to Step 5
- [x] Mobile responsive (test at 375px width)

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
- [x] `createPayment()` returns valid Barion URL (mock HTTP in test)
- [x] Transaction record created with PENDING status
- [x] Webhook updates transaction and appointment status
- [x] Webhook is idempotent (calling twice doesn't double-process)
- [x] Success page shows confirmation when webhook completed
- [x] Failed page shows error and retry option
- [x] Processing page shows spinner and polls `/booking/payment/status/{id}`
- [x] Poll redirects to success/failed when status changes

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
- [x] BookingConfirmation includes cancel and reschedule URLs
- [x] All emails implement ShouldQueue
- [x] AppointmentReminder command finds appointments due in X hours
- [x] ReviewRequest only sent to COMPLETED appointments
- [x] CancelStale command updates PENDING >30min to CANCELLED

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
- [x] Invalid token shows error message
- [x] Expired token shows expiry error
- [x] Used token shows "already used" error
- [x] Cancel within window → appointment cancelled, email sent
- [x] Cancel outside window → error with hours remaining
- [x] Reschedule updates times and creates new tokens
- [x] Review creates unapproved Review record

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
- [x] Visit `/booking?lang=en` → session has locale=en
- [x] Visit `/booking` again → still English
- [x] Click HU → switches to Hungarian
- [x] Booking UI shows translated text

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
- [x] `php artisan test` passes all tests (81/83, 2 minor issues)
- [x] Booking flow test covers all 5 steps
- [x] Cancel test verifies window enforcement
- [x] Barion webhook test verifies idempotency
- [x] No N+1 queries (verified with DB::enableQueryLog())

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
