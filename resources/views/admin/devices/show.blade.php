@extends('layouts.vertical', ['title' => 'Cihaz Detayı - İzmir Time Machine', 'topbarTitle' => 'Cihaz Detayı'])

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.devices.index') }}">Cihazlar</a></li>
                        <li class="breadcrumb-item active">Cihaz Detayı</li>
                    </ol>
                </div>
                <h4 class="page-title">Cihaz Detayı: {{ $device->device_id }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- General Information -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Genel Bilgiler</h4>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="fw-medium">ID:</td>
                            <td><span class="badge badge-soft-secondary">#{{ $device->id }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Device ID:</td>
                            <td><code>{{ $device->device_id }}</code></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- GCM Information -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">GCM Bilgileri</h4>
                </div>
                <div class="card-body">
                    @if($device->gcm_id)
                        <div class="mb-2">
                            <label class="form-label fw-medium">GCM ID:</label>
                            <div class="form-control-plaintext">
                                <code style="font-size: 0.8rem; word-break: break-all;">{{ $device->gcm_id }}</code>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Bu token push notification gönderimi için kullanılır.
                        </small>
                    @else
                        <div class="text-muted text-center py-3">
                            <i class="mdi mdi-bell-off" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">GCM ID bulunamadı</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.devices.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Geri Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Location Information -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Konum Bilgileri</h4>
                </div>
                <div class="card-body">
                    @if($device->hasLocation())
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Adres:</label>
                                    <div class="form-control-plaintext">
                                        @if($device->address)
                                            <div class="text-dark fw-medium mb-2" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="{{ $device->address }}">
                                                <i class="mdi mdi-map-marker text-danger me-1"></i>
                                                {{ Str::limit($device->address, 80) }}
                                            </div>
                                        @else
                                            <div class="text-muted" id="address-loading">
                                                <i class="mdi mdi-loading mdi-spin me-1"></i>
                                                Adres yükleniyor...
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Enlem (Latitude):</label>
                                    <div class="form-control-plaintext">
                                        <code>{{ number_format($device->latitude, 8) }}</code>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Boylam (Longitude):</label>
                                    <div class="form-control-plaintext">
                                        <code>{{ number_format($device->longitude, 8) }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-map-marker-off text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-2">Konum bilgisi yok</h5>
                            <p class="text-muted">Bu cihaz henüz konum bilgisi paylaşmamış.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Load address if not already loaded
        const loadingElement = document.getElementById('address-loading');
        
        if (loadingElement) {
            const deviceId = {{ $device->id }};
            
            fetch(`/admin/devices/${deviceId}/address`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.address) {
                    loadingElement.innerHTML = `
                        <div class="text-dark fw-medium mb-2" 
                             data-bs-toggle="tooltip" 
                             data-bs-placement="top" 
                             title="${data.address}">
                            <i class="mdi mdi-map-marker text-danger me-1"></i>
                            ${data.address.length > 80 ? data.address.substring(0, 80) + '...' : data.address}
                        </div>
                    `;
                    
                    // Initialize tooltip for the new element
                    const newTooltipElement = loadingElement.querySelector('[data-bs-toggle="tooltip"]');
                    if (newTooltipElement) {
                        new bootstrap.Tooltip(newTooltipElement);
                    }
                } else {
                    loadingElement.innerHTML = `
                        <div class="text-warning">
                            <i class="mdi mdi-map-marker-off me-1"></i>
                            Adres bulunamadı
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading address:', error);
                loadingElement.innerHTML = `
                    <div class="text-danger">
                        <i class="mdi mdi-alert-circle me-1"></i>
                        Adres yüklenemedi
                    </div>
                `;
            });
        }
    });
</script>
@endpush