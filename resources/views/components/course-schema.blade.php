@props(['course'])

@php
    $effectivePrice = $course->getEffectivePrice();
    $currency = config('app.currency', 'EGP');
    $rawDescription = $course->short_description ?: $course->description;
    $cleanDescription = trim(preg_replace('/\s+/', ' ', strip_tags($rawDescription ?? $course->title)));
    $rating = $course->getAverageRating();
    $reviewCount = $course->getTotalReviews();
@endphp

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Course",
  "name": {{ json_encode($course->title, JSON_UNESCAPED_UNICODE) }},
  "description": {{ json_encode($cleanDescription, JSON_UNESCAPED_UNICODE) }},
  "provider": {
    "@type": "Organization",
    "name": {{ json_encode($appSettings->platform_name ?? config('app.name', 'منصة التعلم الإلكتروني'), JSON_UNESCAPED_UNICODE) }},
    "sameAs": {{ json_encode(url('/'), JSON_UNESCAPED_SLASHES) }}
  },
  "instructor": {
    "@type": "Person",
    "name": {{ json_encode($course->instructor->name ?? 'مدرب الكورس', JSON_UNESCAPED_UNICODE) }}
  },
  "offers": {
    "@type": "Offer",
    "category": {{ json_encode($course->category->name ?? 'دورات تعليمية', JSON_UNESCAPED_UNICODE) }},
    "price": "{{ $effectivePrice }}",
    "priceCurrency": {{ json_encode($currency, JSON_UNESCAPED_UNICODE) }},
    "availability": "https://schema.org/InStock",
    "url": {{ json_encode(route('student.courses.show', $course), JSON_UNESCAPED_SLASHES) }}
  }
  @if($course->thumbnail_url)
  ,"image": {{ json_encode($course->thumbnail_url, JSON_UNESCAPED_SLASHES) }}
  @endif
  @if($reviewCount > 0)
  ,"aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{{ number_format($rating, 1) }}",
    "reviewCount": "{{ $reviewCount }}"
  }
  @endif
}
</script>
