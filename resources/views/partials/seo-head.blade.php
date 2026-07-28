{{-- Technical SEO Head Partial --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Dynamic Title Tag --}}
<title>@hasSection('meta_title')@yield('meta_title') | {{ $appSettings->platform_name ?? config('app.name', 'منصة التعلم الإلكتروني') }}@else{{ $appSettings->platform_name ?? config('app.name', 'منصة التعلم الإلكتروني') }} - منصة التعليم الذكي والتدريب الإلكتروني@endif</title>

{{-- Search Engine Optimization (SEO) Meta Tags --}}
<meta name="description" content="@yield('meta_description', $appSettings->site_description ?? 'منصة تعليمية متكاملة لتقديم أفضل الكورسات والندوات التدريبية عبر الإنترنت بإشراف نخبة من المعلمين والخبراء.')">
<meta name="keywords" content="@yield('meta_keywords', 'تعليم إلكتروني, كورسات أونلاين, دورات تدريبية, تعلم البرمجة, منصة تعليمية, دروس أونلاين')">
<meta name="author" content="{{ $appSettings->platform_name ?? config('app.name', 'Educational Platform') }}">
<meta name="robots" content="@yield('meta_robots', 'index, follow')">
<link rel="canonical" href="@yield('canonical_url', url()->current())">

{{-- Open Graph / Facebook & WhatsApp Meta Tags --}}
<meta property="og:type" content="@yield('og_type', 'website')">
<meta property="og:site_name" content="{{ $appSettings->platform_name ?? config('app.name', 'منصة التعلم الإلكتروني') }}">
<meta property="og:url" content="@yield('canonical_url', url()->current())">
<meta property="og:title" content="@yield('og_title', View::yieldContent('meta_title', $appSettings->platform_name ?? config('app.name', 'منصة التعلم الإلكتروني')))">
<meta property="og:description" content="@yield('og_description', View::yieldContent('meta_description', 'منصة تعليمية متكاملة لتقديم أفضل الكورسات والندوات التدريبية عبر الإنترنت.'))">
<meta property="og:image" content="@yield('og_image', asset('images/default.png'))">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="ar_AR">

{{-- Twitter Cards Meta Tags --}}
<meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
<meta name="twitter:url" content="@yield('canonical_url', url()->current())">
<meta name="twitter:title" content="@yield('twitter_title', View::yieldContent('meta_title', $appSettings->platform_name ?? config('app.name', 'منصة التعلم الإلكتروني')))">
<meta name="twitter:description" content="@yield('twitter_description', View::yieldContent('meta_description', 'منصة تعليمية متكاملة لتقديم أفضل الكورسات والندوات التدريبية عبر الإنترنت.'))">
<meta name="twitter:image" content="@yield('twitter_image', View::yieldContent('og_image', asset('images/default.png')))">
