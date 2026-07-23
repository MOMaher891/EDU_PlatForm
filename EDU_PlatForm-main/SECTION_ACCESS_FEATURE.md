# Section-Based Course Access Feature

## Overview

This feature allows students to purchase access to individual sections within a course, rather than the entire course. This provides more granular monetization options and allows students to focus on specific topics they're interested in.

## Database Changes

### New Tables

1. **student_section_access** - Tracks which sections each student has access to
   - `user_id` - The student
   - `course_id` - The course containing the section
   - `section_id` - The specific section
   - `payment_id` - Reference to the payment record
   - `price_paid` - Amount paid for this section
   - `access_granted_at` - When access was granted
   - `access_expires_at` - Optional expiration date
   - `is_active` - Whether access is currently active

### Modified Tables

1. **course_sections** - Added pricing fields
   - `price` - Individual section price
   - `discount_price` - Discounted price
   - `is_purchasable_separately` - Whether section can be purchased individually

## Models

### StudentSectionAccess
- Manages section access records
- Includes scopes for filtering active access
- Methods for checking expiration and access status

### CourseSection (Updated)
- Added pricing methods (`getEffectivePrice()`, `getDiscountPercentage()`)
- Added `hasStudentAccess()` method to check if a user has access
- Added `isPurchasable()` method to check if section can be purchased separately

### User (Updated)
- Added `sectionAccess()` relationship
- Added `hasSectionAccess()` method
- Added `getAccessibleSections()` method

## Services

### SectionAccessService
Core service for managing section access:

- `grantAccess()` - Grant access to a section
- `hasAccess()` - Check if user has access to a section
- `hasLessonAccess()` - Check if user has access to a lesson
- `getAccessibleSections()` - Get all accessible sections for a user in a course
- `revokeAccess()` - Revoke access to a section
- `getAccessStatistics()` - Get access statistics for a section

## Controllers

### PaymentController (Updated)
- Added support for section-based purchases
- `checkout()` method now accepts optional section parameter
- `process()` method handles both course and section payments
- Added `grantFreeSectionAccess()` for free sections

### StudentController (Updated)
- Updated to show only accessible sections
- Added access validation in `learnCourse()` method
- Shows section purchase options in course view

## Middleware

### SectionAccessMiddleware
- Enforces section access restrictions
- Prevents unauthorized access to sections and lessons
- Redirects users to course page with appropriate error messages

## Routes

### New Routes
```php
// Section purchase routes
Route::get('/payment/{course}/section/{section}/checkout', [PaymentController::class, 'checkout'])->name('payment.section.checkout');
Route::post('/payment/{course}/section/{section}/process', [PaymentController::class, 'process'])->name('payment.section.process');

// Protected section access routes
Route::middleware(['auth', 'section.access'])->group(function () {
    Route::get('/sections/{section}', ...)->name('sections.show');
    Route::get('/lessons/{lesson}', ...)->name('lessons.show');
});
```

## Frontend Changes

### Course Show Page
- Shows section purchase options for purchasable sections
- Displays pricing information for individual sections
- Shows access status (available, purchasable, locked)
- Provides direct purchase links for sections

### Payment Success Page
- Updated to handle both course and section purchases
- Shows appropriate success messages and action buttons

## Usage Examples

### Granting Section Access
```php
$sectionAccessService = app(SectionAccessService::class);
$sectionAccessService->grantAccess($user, $section, $paymentId, $pricePaid);
```

### Checking Access
```php
if ($sectionAccessService->hasAccess($user, $section)) {
    // User has access to this section
}
```

### Getting Accessible Sections
```php
$accessibleSections = $sectionAccessService->getAccessibleSections($user, $course);
```

## Testing

Run the section access tests:
```bash
php artisan test tests/Feature/SectionAccessTest.php
```

## Admin Interface

### Managing Section Pricing
1. Go to Admin > Courses > [Course] > Sections
2. Edit a section to set:
   - Individual price
   - Discount price
   - Whether it's purchasable separately

### Viewing Access Statistics
- Access statistics are available through the `getAccessStatistics()` method
- Can be integrated into admin dashboard for reporting

## Security Considerations

1. **Server-side validation** - All access checks are enforced server-side
2. **Middleware protection** - Routes are protected by SectionAccessMiddleware
3. **Database constraints** - Unique constraints prevent duplicate access records
4. **Payment verification** - Access is only granted after successful payment

## Future Enhancements

1. **Bulk section purchases** - Allow purchasing multiple sections at once
2. **Section bundles** - Create discounted bundles of related sections
3. **Temporary access** - Time-limited access to sections
4. **Access sharing** - Allow sharing section access with other users
5. **Analytics** - Detailed reporting on section sales and usage

## Migration

To add this feature to an existing system:

1. Run the migrations:
   ```bash
   php artisan migrate
   ```

2. Seed sample pricing data:
   ```bash
   php artisan db:seed --class=SectionAccessSeeder
   ```

3. Update existing views to include section purchase options

4. Test the feature thoroughly before going live
