# Decisions - Booking System

## [2026-01-28T20:43:30Z] Initial Decisions from Plan
- Database: MariaDB (not MySQL)
- Migration strategy: `migrate:fresh --seed` for development
- Timezone: Europe/Budapest
- Locale: Hungarian (admin), HU/EN switchable (customer)
- Locking: Advisory lock via settings.slot_lock row

