@extends('layouts.app')

@section('title', 'إدارة بوابات الدفع الإلكتروني')

@section('content')
<div class="admin-gateways-page">
    <!-- Page Header -->
    <div class="page-header mb-4" data-aos="fade-down">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h1 class="page-title">
                        <i class="fas fa-wallet text-indigo-400 me-2"></i>
                        إدارة بوابات الدفع الإلكتروني
                    </h1>
                    <p class="page-subtitle">إدارة مفاتيح الربط والتفعيل والوضع التجريبي لجميع بوابات الدفع دون المساس بالكود</p>
                </div>
                <div class="col-md-5 text-end mt-3 mt-md-0">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-light rounded-3 shadow-sm" onclick="saveSortOrders()">
                            <i class="fas fa-sort-numeric-down me-2"></i>
                            حفظ ترتيب البوابات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Quick Stats Cards -->
        <div class="row g-4 mb-4" data-aos="fade-up">
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon-wrap bg-indigo-50 text-indigo-600 rounded-3 p-3 me-3">
                            <i class="fas fa-cash-register fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">إجمالي البوابات المتاحة</h6>
                            <h3 class="fw-bold mb-0 text-slate-800">{{ $gateways->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon-wrap bg-emerald-50 text-emerald-600 rounded-3 p-3 me-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">البوابات المفعلة</h6>
                            <h3 class="fw-bold mb-0 text-emerald-600">{{ $gateways->where('is_active', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon-wrap bg-violet-50 text-violet-600 rounded-3 p-3 me-3">
                            <i class="fas fa-bolt fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">بيئة الدفع الحي (Live)</h6>
                            <h3 class="fw-bold mb-0 text-violet-600">{{ $gateways->where('mode', 'live')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon-wrap bg-amber-50 text-amber-600 rounded-3 p-3 me-3">
                            <i class="fas fa-vial fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">الوضع التجريبي (Sandbox)</h6>
                            <h3 class="fw-bold mb-0 text-amber-600">{{ $gateways->where('mode', 'sandbox')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Gateways Cards Grid -->
        <div class="row g-4" id="sortableGatewaysContainer">
            @foreach($gateways as $gw)
            <div class="col-lg-6 col-xl-4 gateway-card-item" data-id="{{ $gw->id }}">
                <div class="card gateway-card border-0 shadow-sm rounded-4 h-100 position-relative">
                    <div class="card-header bg-transparent border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="gateway-icon-badge gw-{{ $gw->code }}">
                                @if($gw->code === 'kashier')
                                    <i class="fas fa-shield-alt text-emerald-600 fa-2x"></i>
                                @elseif($gw->code === 'paymob')
                                    <i class="fas fa-mobile-alt text-violet-600 fa-2x"></i>
                                @elseif($gw->code === 'stripe')
                                    <i class="fab fa-stripe-s text-indigo-600 fa-2x"></i>
                                @elseif($gw->code === 'paypal')
                                    <i class="fab fa-paypal text-blue-600 fa-2x"></i>
                                @else
                                    <i class="fas fa-credit-card text-slate-600 fa-2x"></i>
                                @endif
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-slate-900">{{ $gw->name }}</h5>
                                <span class="badge bg-slate-100 text-slate-600 border rounded-pill">
                                    <i class="fas fa-code me-1"></i> {{ strtoupper($gw->code) }}
                                </span>
                            </div>
                        </div>

                        <!-- Active Toggle Switch -->
                        <div class="form-check form-switch form-switch-lg mb-0 ms-auto">
                            <input class="form-check-input gateway-status-switch" type="checkbox" role="switch"
                                   id="statusSwitch_{{ $gw->id }}"
                                   data-id="{{ $gw->id }}"
                                   {{ $gw->is_active ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- Badges row -->
                        <div class="d-flex gap-2 mb-3">
                            <!-- Status Badge -->
                            <span class="badge status-badge-{{ $gw->id }} {{ $gw->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }} py-2 px-3 rounded-3">
                                <i class="fas {{ $gw->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                <span class="status-label">{{ $gw->is_active ? 'مُفعّلة' : 'غير مُفعّلة' }}</span>
                            </span>

                            <!-- Mode Badge -->
                            <span class="badge mode-badge-{{ $gw->id }} {{ $gw->mode === 'live' ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }} py-2 px-3 rounded-3">
                                <i class="fas {{ $gw->mode === 'live' ? 'fa-globe' : 'fa-flask' }} me-1"></i>
                                <span class="mode-label">{{ $gw->mode === 'live' ? 'وضع الحي (Live)' : 'وضع الاختبار (Sandbox)' }}</span>
                            </span>
                        </div>

                        <!-- Mode Controls & Priority -->
                        <div class="bg-slate-50 p-3 rounded-3 mb-3 border">
                            <div class="row align-items-center g-2">
                                <div class="col-7">
                                    <label class="form-label small fw-bold text-slate-700 mb-1">البيئة المعتمدة:</label>
                                    <select class="form-select form-select-sm gateway-mode-select" data-id="{{ $gw->id }}">
                                        <option value="sandbox" {{ $gw->mode === 'sandbox' ? 'selected' : '' }}>Sandbox (اختباري)</option>
                                        <option value="live" {{ $gw->mode === 'live' ? 'selected' : '' }}>Live (حي)</option>
                                    </select>
                                </div>
                                <div class="col-5 text-end">
                                    <label class="form-label small fw-bold text-slate-700 mb-1">أولوية العرض:</label>
                                    <input type="number" class="form-control form-control-sm text-center sort-order-input"
                                           data-id="{{ $gw->id }}"
                                           value="{{ $gw->sort_order }}" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Credentials Preview -->
                        <div class="credentials-summary small text-slate-500 mb-4">
                            <i class="fas fa-key me-1 text-slate-400"></i>
                            <strong>المفاتيح الضابطة:</strong>
                            <div class="mt-1 font-monospace bg-white p-2 rounded border">
                                @php $creds = $gw->credentials ?? []; @endphp
                                @if(!empty($creds))
                                    @foreach(array_slice($creds, 0, 2) as $key => $val)
                                        <div><span class="text-slate-600">{{ $key }}:</span> <span class="text-indigo-600">{{ Str::limit((string)$val, 14, '***') }}</span></div>
                                    @endforeach
                                @else
                                    <span class="text-danger">لم يتم ضبط المفاتيح بعد</span>
                                @endif
                            </div>
                        </div>

                        <!-- Configure Action Button -->
                        <button type="button" class="btn btn-indigo w-100 py-2.5 rounded-3 d-flex align-items-center justify-content-center gap-2"
                                data-bs-toggle="modal" data-bs-target="#editGatewayModal_{{ $gw->id }}">
                            <i class="fas fa-sliders-h"></i>
                            تعديل البيانات والمفاتيح السرية
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal for Editing Gateway Credentials -->
            <div class="modal fade" id="editGatewayModal_{{ $gw->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <form method="POST" action="{{ route('admin.payment-gateways.update', $gw) }}">
                            @csrf
                            @method('PUT')

                            <div class="modal-header bg-slate-900 text-white p-4">
                                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                                    <i class="fas fa-key text-indigo-400"></i>
                                    إعدادات ومفاتيح بوابة: {{ $gw->name }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">اسم البوابة الظاهر للطلاب</label>
                                        <input type="text" class="form-control" name="name" value="{{ old('name', $gw->name) }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">وضع البيئة (Mode)</label>
                                        <select class="form-select" name="mode" required>
                                            <option value="sandbox" {{ $gw->mode === 'sandbox' ? 'selected' : '' }}>Sandbox (اختباري)</option>
                                            <option value="live" {{ $gw->mode === 'live' ? 'selected' : '' }}>Live (حي)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">ترتيب الظهور</label>
                                        <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $gw->sort_order) }}" min="0" required>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="modalActive_{{ $gw->id }}" {{ $gw->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold ms-2" for="modalActive_{{ $gw->id }}">تفعيل البوابة في صفحة الدفع</label>
                                        </div>
                                    </div>

                                    <hr class="my-4 border-slate-200">

                                    <h6 class="fw-bold text-slate-800 mb-3">
                                        <i class="fas fa-lock text-indigo-600 me-1"></i>
                                        مفاتيح الربط والاعتماد المشفرة (Encrypted Credentials):
                                    </h6>

                                    <!-- Dynamic Credential Fields based on Gateway Code -->
                                    @php $creds = $gw->credentials ?? []; @endphp

                                    @if($gw->code === 'kashier')
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Merchant ID (معرف التاجر)</label>
                                            <input type="text" class="form-control" name="credentials[merchant_id]"
                                                   value="{{ old('credentials.merchant_id', $creds['merchant_id'] ?? '') }}" placeholder="MID-XXXXX" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">API Key / iframe Key</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control secret-input" name="credentials[api_key]"
                                                       value="{{ old('credentials.api_key', $creds['api_key'] ?? '') }}" placeholder="أدخل API Key" required>
                                                <button class="btn btn-outline-secondary toggle-secret-btn" type="button">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Secret Key (الرمز السري المشفر)</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control secret-input" name="credentials[secret_key]"
                                                       value="{{ old('credentials.secret_key', $creds['secret_key'] ?? '') }}" placeholder="أدخل Secret Key" required>
                                                <button class="btn btn-outline-secondary toggle-secret-btn" type="button">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">العملة (Currency)</label>
                                            <input type="text" class="form-control" name="credentials[currency]"
                                                   value="{{ old('credentials.currency', $creds['currency'] ?? 'EGP') }}" required>
                                        </div>

                                    @elseif($gw->code === 'paymob')
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">API Key</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control secret-input" name="credentials[api_key]"
                                                       value="{{ old('credentials.api_key', $creds['api_key'] ?? '') }}" required>
                                                <button class="btn btn-outline-secondary toggle-secret-btn" type="button"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Integration ID</label>
                                            <input type="text" class="form-control" name="credentials[integration_id]"
                                                   value="{{ old('credentials.integration_id', $creds['integration_id'] ?? '') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Iframe ID</label>
                                            <input type="text" class="form-control" name="credentials[iframe_id]"
                                                   value="{{ old('credentials.iframe_id', $creds['iframe_id'] ?? '') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">HMAC Secret</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control secret-input" name="credentials[hmac_secret]"
                                                       value="{{ old('credentials.hmac_secret', $creds['hmac_secret'] ?? '') }}" required>
                                                <button class="btn btn-outline-secondary toggle-secret-btn" type="button"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">العملة (Currency)</label>
                                            <input type="text" class="form-control" name="credentials[currency]"
                                                   value="{{ old('credentials.currency', $creds['currency'] ?? 'EGP') }}" required>
                                        </div>

                                    @elseif($gw->code === 'stripe')
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Public Key</label>
                                            <input type="text" class="form-control" name="credentials[public_key]"
                                                   value="{{ old('credentials.public_key', $creds['public_key'] ?? '') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Secret Key</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control secret-input" name="credentials[secret_key]"
                                                       value="{{ old('credentials.secret_key', $creds['secret_key'] ?? '') }}" required>
                                                <button class="btn btn-outline-secondary toggle-secret-btn" type="button"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Webhook Secret</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control secret-input" name="credentials[webhook_secret]"
                                                       value="{{ old('credentials.webhook_secret', $creds['webhook_secret'] ?? '') }}">
                                                <button class="btn btn-outline-secondary toggle-secret-btn" type="button"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">العملة (Currency)</label>
                                            <input type="text" class="form-control" name="credentials[currency]"
                                                   value="{{ old('credentials.currency', $creds['currency'] ?? 'USD') }}" required>
                                        </div>

                                    @elseif($gw->code === 'paypal')
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Client ID</label>
                                            <input type="text" class="form-control" name="credentials[client_id]"
                                                   value="{{ old('credentials.client_id', $creds['client_id'] ?? '') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Client Secret</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control secret-input" name="credentials[client_secret]"
                                                       value="{{ old('credentials.client_secret', $creds['client_secret'] ?? '') }}" required>
                                                <button class="btn btn-outline-secondary toggle-secret-btn" type="button"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">العملة (Currency)</label>
                                            <input type="text" class="form-control" name="credentials[currency]"
                                                   value="{{ old('credentials.currency', $creds['currency'] ?? 'USD') }}" required>
                                        </div>

                                    @else
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Merchant Code</label>
                                            <input type="text" class="form-control" name="credentials[merchant_code]"
                                                   value="{{ old('credentials.merchant_code', $creds['merchant_code'] ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Security Key</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control secret-input" name="credentials[security_key]"
                                                       value="{{ old('credentials.security_key', $creds['security_key'] ?? '') }}">
                                                <button class="btn btn-outline-secondary toggle-secret-btn" type="button"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">العملة (Currency)</label>
                                            <input type="text" class="form-control" name="credentials[currency]"
                                                   value="{{ old('credentials.currency', $creds['currency'] ?? 'EGP') }}">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="modal-footer bg-slate-50 p-3">
                                <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-indigo rounded-3 px-4">
                                    <i class="fas fa-save me-1"></i> حفظ التغيرات والمفاتيح
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.admin-gateways-page {
    background-color: #f8fafc;
    min-height: 100vh;
    direction: rtl;
    text-align: right;
    font-family: 'Cairo', system-ui, -apple-system, sans-serif;
}

.page-header {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    color: white;
    padding: 2.2rem 0;
    border-radius: 0 0 24px 24px;
}

.page-title {
    font-size: 1.85rem;
    font-weight: 800;
}

.page-subtitle {
    opacity: 0.85;
    font-size: 0.95rem;
}

/* Gateway Cards Styling */
.gateway-card {
    transition: all 0.25s ease-in-out;
    border: 1px solid #e2e8f0 !important;
}

.gateway-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px -6px rgba(0,0,0,0.08) !important;
}

.gateway-icon-badge {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
}

.btn-indigo {
    background-color: #4f46e5;
    color: white;
    font-weight: 700;
    border: none;
    transition: all 0.2s ease;
}

.btn-indigo:hover {
    background-color: #4338ca;
    color: white;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.form-switch-lg .form-check-input {
    width: 3rem;
    height: 1.5rem;
    cursor: pointer;
}

.form-switch-lg .form-check-input:checked {
    background-color: #10b981;
    border-color: #10b981;
}

.bg-emerald-50 { background-color: #ecfdf5; }
.text-emerald-600 { color: #059669; }
.bg-emerald-100 { background-color: #d1fae5; }
.text-emerald-700 { color: #047857; }

.bg-indigo-50 { background-color: #eef2ff; }
.text-indigo-600 { color: #4f46e5; }
.text-indigo-400 { color: #818cf8; }

.bg-violet-50 { background-color: #f5f3ff; }
.text-violet-600 { color: #7c3aed; }

.bg-amber-50 { background-color: #fffbeb; }
.text-amber-600 { color: #d97706; }
.bg-amber-500 { background-color: #f59e0b; }

.bg-slate-900 { background-color: #0f172a; }
.bg-slate-50 { background-color: #f8fafc; }
.text-slate-800 { color: #1e293b; }
.text-slate-900 { color: #0f172a; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Show/Hide Secret Input Toggle Button Listener
    document.querySelectorAll('.toggle-secret-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('.secret-input');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Instant AJAX Toggle for Active Status Switch
    document.querySelectorAll('.gateway-status-switch').forEach(switchEl => {
        switchEl.addEventListener('change', function() {
            const gatewayId = this.dataset.id;
            const isActive = this.checked;

            fetch(`/admin/payment-gateways/${gatewayId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('نجاح', data.message, 'success');
                    updateBadgeStatus(gatewayId, data.is_active);
                } else {
                    showToast('خطأ', 'فشل في تغيير تفعيل البوابة', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('خطأ', 'حدث خطأ في الاتصال بالخادوم', 'error');
            });
        });
    });

    // Instant AJAX Toggle for Mode Select (Sandbox vs Live)
    document.querySelectorAll('.gateway-mode-select').forEach(selectEl => {
        selectEl.addEventListener('change', function() {
            const gatewayId = this.dataset.id;
            const mode = this.value;

            fetch(`/admin/payment-gateways/${gatewayId}/mode`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ mode: mode })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('تحديث البيئة', data.message, 'info');
                    updateBadgeMode(gatewayId, data.mode);
                } else {
                    showToast('خطأ', 'فشل في تحديث بيئة البوابة', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('خطأ', 'حدث خطأ غير متوقع', 'error');
            });
        });
    });
});

function updateBadgeStatus(id, isActive) {
    const badge = document.querySelector(`.status-badge-${id}`);
    if (badge) {
        const icon = badge.querySelector('i');
        const label = badge.querySelector('.status-label');
        if (isActive) {
            badge.className = `badge status-badge-${id} bg-emerald-100 text-emerald-700 py-2 px-3 rounded-3`;
            icon.className = 'fas fa-check-circle me-1';
            label.textContent = 'مُفعّلة';
        } else {
            badge.className = `badge status-badge-${id} bg-slate-100 text-slate-600 py-2 px-3 rounded-3`;
            icon.className = 'fas fa-times-circle me-1';
            label.textContent = 'غير مُفعّلة';
        }
    }
}

function updateBadgeMode(id, mode) {
    const badge = document.querySelector(`.mode-badge-${id}`);
    if (badge) {
        const icon = badge.querySelector('i');
        const label = badge.querySelector('.mode-label');
        if (mode === 'live') {
            badge.className = `badge mode-badge-${id} bg-emerald-600 text-white py-2 px-3 rounded-3`;
            icon.className = 'fas fa-globe me-1';
            label.textContent = 'وضع الحي (Live)';
        } else {
            badge.className = `badge mode-badge-${id} bg-amber-500 text-white py-2 px-3 rounded-3`;
            icon.className = 'fas fa-flask me-1';
            label.textContent = 'وضع الاختبار (Sandbox)';
        }
    }
}

function saveSortOrders() {
    const orders = [];
    document.querySelectorAll('.sort-order-input').forEach(input => {
        orders.push({
            id: input.dataset.id,
            sort_order: parseInt(input.value) || 0
        });
    });

    fetch('/admin/payment-gateways/update-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ orders: orders })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('نجاح', data.message, 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('خطأ', 'فشل في تحديث ترتيب البوابات', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('خطأ', 'حدث خطأ في تحديث ترتيب البوابات', 'error');
    });
}

function showToast(title, message, type = 'success') {
    const bgClass = type === 'success' ? 'bg-emerald-600' : (type === 'info' ? 'bg-indigo-600' : 'bg-danger');
    const toastDiv = document.createElement('div');
    toastDiv.className = `toast align-items-center text-white ${bgClass} border-0 position-fixed top-0 end-0 m-4 show`;
    toastDiv.style.zIndex = '9999';
    toastDiv.innerHTML = `
        <div class="d-flex">
            <div class="toast-body font-bold">
                <strong>${title}:</strong> ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.body.appendChild(toastDiv);
    setTimeout(() => {
        if (document.body.contains(toastDiv)) toastDiv.remove();
    }, 4000);
}
</script>
@endpush
