# Issues - Booking System

## [2026-01-28T20:43:30Z] Known Issues from Plan
- Existing business tables in DB (appointments, services, etc.) have no migrations
- Must use `migrate:fresh` not `migrate` to avoid "table exists" errors
- phpunit.xml currently forces SQLite - needs MariaDB for tests

