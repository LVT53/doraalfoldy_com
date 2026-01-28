# Draft: Salonic-like Booking System (FINAL)

## Requirements Summary

### Core Stack
- Laravel 12.48.1 (installed), Filament 5 (to install), Livewire 4 (to install)
- MariaDB, Tailwind CSS 4.x
- Barion payment gateway (sandbox first)
- TDD with Pest

### Decisions Made

| Decision | Choice |
|----------|--------|
| Reviews storage | Separate `reviews` table |
| Review creation | **Customer submission after appointment** (via magic link) |
| Reference photos | Separate `reference_photos` table, **per service category** |
| Voucher-service link | **Universal vouchers** (any service) |
| Vouchers & deposits | **Deposit must be card only**, voucher for remaining |
| Payment gateway | **Barion redirect** (not embedded) |
| Email notifications | Yes - confirmation, reminder, cancel, reschedule |
| Service categories | Yes |
| Booking flow | **Multi-step wizard** |
| Multi-service booking | **No - single service per booking** |
| Self-service cancellation | **Yes, configurable hours via admin** (0 = disabled) |
| Cancellation refund | **Manual by admin** (no auto-refund) |
| Reschedule scope | **Time only** - cannot change service |
| Customer identity | **Magic link in email** (no user accounts for customers) |
| Buffer time | **Configurable via admin** (global or per-service) |
| Schedule exceptions | **Yes - separate table** for holidays/closures |
| Admin language | Hungarian |
| Customer UI language | Hungarian + English |
| Admin users | **Multiple admins, same permissions** |

### Scope Boundaries

#### INCLUDE
1. **Database Layer**
   - Migrations: `service_categories`, `reviews`, `reference_photos`, `settings`, `schedule_exceptions`, `booking_tokens`
   - Add `category_id` to services, add `buffer_minutes` to services
   - Eloquent models for ALL tables
   - Factories and seeders

2. **Filament Admin Panel (Hungarian)**
   - Resources: Services, ServiceCategories, Appointments, Reviews, ReferencePhotos, Vouchers, Schedules, ScheduleExceptions
   - Settings page (key-value)
   - Employee profile management
   - Multi-admin authentication (Filament default)

3. **Livewire Booking Interface**
   - Multi-step wizard: Service → Date → Time → Details → Payment
   - Service selection by category
   - Available slots calculator (respects schedules, exceptions, buffer, existing bookings)
   - Voucher code validation & application
   - Barion redirect integration (sandbox)
   - Booking confirmation page
   - Magic link cancel/reschedule pages
   - Language switcher (HU/EN)

4. **Email Notifications** (exactly 6)
   - Booking confirmation (to customer, with magic link)
   - Appointment reminder (configurable hours before)
   - Cancellation confirmation (to customer)
   - Reschedule confirmation (to customer)
   - New booking notification (to admin)
   - Review request (to customer, after appointment)

5. **Customer Review Submission**
   - Magic link to leave review after appointment
   - Simple form: rating (1-5), comment
   - Admin approval before display

#### EXCLUDE (GUARDRAILS)
- User registration/accounts for customers
- Multiple employees
- Recurring appointments
- SMS notifications
- Waiting list
- Loyalty points
- Product/inventory management
- Advanced analytics dashboards
- Mobile app/PWA
- Chat/messaging
- Multi-service per booking
- Automatic refunds (manual only)

### Settings to Implement (Exact List)

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `cancellation_hours` | int | 24 | Hours before appointment cancel allowed (0=disabled) |
| `reminder_hours` | int | 24 | Hours before to send reminder email |
| `default_buffer_minutes` | int | 0 | Default buffer between appointments |
| `site_name` | string | "Dóra Álfoldy" | Site name for emails/branding |
| `admin_email` | string | | Email for admin notifications |
| `booking_terms` | text | | Terms shown at booking |
| `barion_pos_key` | string | | Barion POSKey (encrypted) |
| `barion_sandbox` | bool | true | Use sandbox mode |

### Database Schema (New Tables)

```sql
-- service_categories
id, name, slug, description, sort_order, created_at, updated_at

-- services (modify existing)
+ category_id (FK), + buffer_minutes (nullable, overrides default)

-- reviews
id, customer_name, customer_email, rating (1-5), content, 
is_approved, appointment_id (optional), created_at, updated_at

-- reference_photos
id, category_id (FK to service_categories), image_path, caption, 
sort_order, created_at, updated_at

-- settings
id, key (unique), value, created_at, updated_at

-- schedule_exceptions
id, date, reason, is_closed (bool), 
custom_start_time, custom_end_time, created_at, updated_at

-- booking_tokens
id, appointment_id (FK), token (uuid, unique), type (enum: cancel, reschedule, review), 
expires_at, used_at, created_at, updated_at
```

### Existing Tables (Keep As-Is)
- `appointments` - add relationship to service
- `vouchers` - universal, no service link needed
- `transactions` - polymorphic to appointments
- `employee_profiles` - single record for the employee
- `schedules` - weekly recurring schedule

### Edge Cases Handled

| Case | Solution |
|------|----------|
| Concurrent booking | DB transaction + pessimistic locking |
| Double-click submit | Debounce + server-side idempotency |
| Voucher expires mid-wizard | Re-validate at payment step |
| Service deactivated after booking | Appointment valid, service shows "(Inactive)" |
| Price change after booking | Store price at booking time in appointment |
| Schedule change after booking | Existing appointments grandfather'd |
| Cross-midnight booking | Handle in slot calculator |

### Test Strategy (TDD)

Priority test areas:
1. Slot availability calculator (most critical)
2. Concurrent booking prevention
3. Voucher validation and balance
4. Barion webhook handling
5. Cancellation time boundary
6. Magic link token validation
