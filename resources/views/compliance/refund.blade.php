@extends('layouts.app')

@section('title', 'سياسة الاسترجاع - EDU School')

@section('content')
<div class="container py-5">
    <div class="compliance-container fade-in">
        <div class="card border-0 shadow-lg">
            <div class="card-body p-5">
                <!-- Navigation Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">سياسة الاسترجاع</li>
                    </ol>
                </nav>

                <!-- Markdown Content Render Container -->
                <div class="compliance-content text-start" dir="rtl" lang="en">
                    <div id="markdown-raw" class="d-none">{!! e($content) !!}</div>
                    <div id="rendered-content" class="markdown-body">
                        <!-- Content parsed here by Marked.js -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .compliance-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .markdown-body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        line-height: 1.8;
        color: #334155;
        font-size: 1.05rem;
        text-align: start;
    }
    
    .markdown-body h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.75rem;
        margin-bottom: 1.75rem;
    }
    
    .markdown-body h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
        margin-top: 2rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 0.5rem;
    }
    
    .markdown-body h3 {
        font-size: 1.2rem;
        font-weight: 600;
        color: #334155;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    
    .markdown-body p {
        margin-bottom: 1.25rem;
    }
    
    .markdown-body ul, .markdown-body ol {
        margin-bottom: 1.25rem;
        padding-left: 0;
        padding-right: 0;
        padding-inline-start: 2rem;
    }
    
    .markdown-body li {
        margin-bottom: 0.5rem;
    }
    
    .markdown-body hr {
        margin: 2rem 0;
        border: 0;
        border-top: 1px solid #e2e8f0;
        opacity: 1;
    }
    
    .markdown-body strong {
        color: #0f172a;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        float: right;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<!-- Marked.js for parsing Markdown -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            const rawMarkdown = document.getElementById('markdown-raw').textContent;
            // Parse Markdown to HTML
            const renderedHtml = marked.parse(rawMarkdown);
            // Insert parsed HTML into the DOM
            document.getElementById('rendered-content').innerHTML = renderedHtml;
        } catch (error) {
            console.error('Error parsing markdown:', error);
            document.getElementById('rendered-content').innerHTML = '<div class="alert alert-danger">Error rendering page content. Please try again.</div>';
        }
    });
</script>
@endpush
@endsection
