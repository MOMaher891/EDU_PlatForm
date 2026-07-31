@props(['course'])

@php
    $effectivePrice = $course->getEffectivePrice();
    $currency = config('app.currency', 'EGP');
    $rawDescription = $course->short_description ?: $course->description;
    $cleanDescription = trim(preg_replace('/\s+/', ' ', strip_tags($rawDescription ?? $course->title)));
    $rating = $course->getAverageRating();
    $reviewCount = $course->getTotalReviews();

    // بناء الهيكل الأساسي
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => $course->title,
        'description' => $cleanDescription,
        'provider' => [
            '@type' => 'Organization',
            'name' => $appSettings->platform_name ?? config('app.name', 'منصة التعلم الإلكتروني'),
            'sameAs' => url('/'),
        ],
        'instructor' => [
            '@type' => 'Person',
            'name' => $course->instructor->name ?? 'مدرب الكورس',
        ],
        'offers' => [
            '@type' => 'Offer',
            'category' => $course->category->name ?? 'دورات تعليمية',
            'price' => (string) $effectivePrice,
            'priceCurrency' => $currency,
            'availability' => 'https://schema.org/InStock',
            'url' => route('student.courses.show', $course),
        ],
    ];

    // الشروط بداخل PHP بدلاً من وسوم Blade
    if (!empty($course->thumbnail_url)) {
        $schema['image'] = $course->thumbnail_url;
    }

    if ($reviewCount > 0) {
        $schema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => number_format($rating, 1),
            'reviewCount' => (string) $reviewCount,
        ];
    }
@endphp

<script type="application/ld+json">
@json($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
</script>