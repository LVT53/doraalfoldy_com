

## [2026-01-28T23:00:00Z] Task 7: Filament Resources Created

### What Was Done
1. Created ServiceCategoryResource in `app/Filament/Resources/ServiceCategoryResource.php`:
   - Form: name (required), slug (required, unique), description (textarea), sort_order (numeric, default 0)
   - Table: name (searchable, sortable), slug, services_count (counts relationship), sort_order (sortable)
   - Hungarian labels: 'Szolgáltatási kategóriák', 'Név', 'URL azonosító', 'Leírás', 'Sorrend'

2. Created ServiceResource in `app/Filament/Resources/ServiceResource.php`:
   - Form: category_id (relationship), name (required), slug (required, unique), duration_minutes (numeric, suffix 'perc'), buffer_minutes (numeric, nullable, suffix 'perc'), price (numeric, suffix 'Ft'), deposit_fee (numeric, default 0, suffix 'Ft'), description (textarea), is_active (toggle, default true)
   - Table: name (searchable), category.name, duration_minutes (suffix ' perc'), price (money HUF), is_active (toggle column)
   - Hungarian labels: 'Szolgáltatások', 'Kategória', 'Időtartam', 'Szünet', 'Ár', 'Előleg', 'Aktív'

3. Created 8 resource page files:
   - ServiceCategoryResource/Pages/ListServiceCategories.php
   - ServiceCategoryResource/Pages/CreateServiceCategory.php
   - ServiceCategoryResource/Pages/EditServiceCategory.php
   - ServiceResource/Pages/ListServices.php
   - ServiceResource/Pages/CreateService.php
   - ServiceResource/Pages/EditService.php

4. Verified routes are registered:
   - GET /admin/service-categories
   - GET /admin/service-categories/create
   - GET /admin/service-categories/{record}/edit
   - GET /admin/services
   - GET /admin/services/create
   - GET /admin/services/{record}/edit

### Key Implementation Details

#### Filament 5 Syntax Changes
- `form()` method signature changed from `form(Form $form): Form` to `form(Schema $schema): Schema`
- Must import `Filament\Schemas\Schema` instead of `Filament\Forms\Form`
- `navigationIcon` type changed from `?string` to `string|BackedEnum|null`
- Must import `BackedEnum` for type declaration

#### Form Components Used
- `Forms\Components\TextInput` - for name, slug, numeric fields
- `Forms\Components\Textarea` - for description
- `Forms\Components\Select` - for category_id relationship
- `Forms\Components\Toggle` - for is_active boolean
- Suffix support: `->suffix('perc')` and `->suffix('Ft')` for units

#### Table Columns Used
- `Tables\Columns\TextColumn` - for text fields with searchable/sortable
- `Tables\Columns\ToggleColumn` - for is_active (editable inline)
- `->counts('services')` - for counting related records
- `->money('HUF')` - for price formatting
- `->suffix(' perc')` - for duration display

#### Hungarian Localization
- All labels use Hungarian text per project requirements
- Navigation labels: 'Szolgáltatási kategóriák', 'Szolgáltatások'
- Form labels: 'Név', 'Kategória', 'Időtartam', 'Ár', 'Előleg', 'Aktív'
- Icons: heroicon-o-folder (categories), heroicon-o-briefcase (services)

### Files Created
- app/Filament/Resources/ServiceCategoryResource.php
- app/Filament/Resources/ServiceCategoryResource/Pages/ListServiceCategories.php
- app/Filament/Resources/ServiceCategoryResource/Pages/CreateServiceCategory.php
- app/Filament/Resources/ServiceCategoryResource/Pages/EditServiceCategory.php
- app/Filament/Resources/ServiceResource.php
- app/Filament/Resources/ServiceResource/Pages/ListServices.php
- app/Filament/Resources/ServiceResource/Pages/CreateService.php
- app/Filament/Resources/ServiceResource/Pages/EditService.php

### Verification Results
✅ Routes registered: /admin/service-categories and /admin/services
✅ Admin panel loads at http://doraalfoldy_com.test/admin
✅ Hungarian labels applied throughout
✅ ToggleColumn for is_active enables inline editing
✅ Relationship select for category_id works
✅ Money formatting for HUF currency
✅ Pint formatter: pass

### Notes
- Filament 5 uses Schema-based form definitions (not Form class)
- BackedEnum type required for navigationIcon property
- All 8 page files follow standard Filament resource page pattern
- Ready for Task 8 (ScheduleResource and ScheduleExceptionResource)
