@extends('layouts.vertical', ['title' => 'Cihaz Yönetimi - İzmir Time Machine', 'topbarTitle' => 'Cihazlar'])

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Cihazlar</li>
                    </ol>
                </div>
                <h4 class="page-title">Cihaz Yönetimi</h4>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Actions Bar -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-cellphone text-info me-2" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h5 class="mb-0">Cihazlar</h5>
                                    <p class="text-muted mb-0">Toplam {{ $devices->total() }} cihaz</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.devices.index') }}" class="btn btn-info">
                                <i class="mdi mdi-refresh me-1"></i>
                                Yenile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.devices.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Arama</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Device ID, GCM ID...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-magnify me-1"></i>
                                    Ara
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Devices Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <a href="{{ route('admin.devices.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            ID
                                            @if(request('sort') === 'id')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.devices.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'device_id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Device ID
                                            @if(request('sort') === 'device_id')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.devices.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'gcm_id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            GCM ID
                                            @if(request('sort') === 'gcm_id')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Adres</th>
                                    <th>
                                        <a href="{{ route('admin.devices.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'latitude', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Koordinatlar
                                            @if(request('sort') === 'latitude' || request('sort') === 'longitude')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($devices as $device)
                                    <tr>
                                        <td>
                                            <span class="badge badge-soft-secondary">#{{ $device->id }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="text-dark mb-1">{{ $device->device_id }}</h6>
                                            </div>
                                        </td>
                                        <td>
                                            @if($device->gcm_id)
                                                <span class="text-monospace" style="font-size: 0.8rem;">
                                                    {{ Str::limit($device->gcm_id, 20) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($device->hasLocation())
                                                <div class="address-container">
                                                    <div class="text-dark fw-medium mb-1" 
                                                         data-bs-toggle="tooltip" 
                                                         data-bs-placement="top" 
                                                         title="{{ $device->address ?? 'Adres yükleniyor...' }}">
                                                        <i class="mdi mdi-map-marker text-danger me-1"></i>
                                                        <span class="address-display">
                                                            @if($device->address)
                                                                {{ Str::limit($device->address, 50) }}
                                                            @else
                                                                Adres yükleniyor...
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <small class="text-muted address-status" style="display: none;">
                                                        <i class="mdi mdi-loading mdi-spin me-1"></i>
                                                        <span class="address-text">Yükleniyor...</span>
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted">
                                                    <i class="mdi mdi-map-marker-off me-1"></i>
                                                    Konum yok
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($device->hasLocation())
                                                <div class="text-monospace" style="font-size: 0.8rem;">
                                                    <div class="text-dark">
                                                        <i class="mdi mdi-crosshairs-gps me-1"></i>
                                                        {{ number_format($device->latitude, 6) }}
                                                    </div>
                                                    <div class="text-muted">
                                                        {{ number_format($device->longitude, 6) }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.devices.show', $device) }}" class="btn btn-soft-primary btn-sm" title="Görüntüle">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-cellphone-off" style="font-size: 3rem;"></i>
                                                <h5 class="mt-2">Henüz cihaz kaydı yok</h5>
                                                <p>Cihazlar mobil uygulama üzerinden otomatik olarak kayıt olacaktır.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($devices->hasPages())
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="text-muted mb-0">
                                            {{ $devices->total() }} kayıttan {{ $devices->firstItem() }}-{{ $devices->lastItem() }} arası gösteriliyor
                                        </p>
                                    </div>
                                    <div>
                                        {{ $devices->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id, deviceId) {
        if (confirm(`"${deviceId}" ID'li cihazı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/devices/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Load addresses for devices that have location but no cached address
        const addressContainers = document.querySelectorAll('.address-container');
        
        addressContainers.forEach(function(container) {
            const addressDisplay = container.querySelector('.address-display');
            const addressStatus = container.querySelector('.address-status');
            const tooltipElement = container.querySelector('[data-bs-toggle="tooltip"]');
            
            if (addressDisplay && addressDisplay.textContent === 'Adres yükleniyor...') {
                // Show loading status
                if (addressStatus) {
                    addressStatus.style.display = 'block';
                }
                
                // Get device ID from the row
                const row = container.closest('tr');
                const deviceId = row.querySelector('.badge-soft-secondary').textContent.replace('#', '');
                
                // Make AJAX request to get address
                fetch(`/admin/devices/${deviceId}/address`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Hide loading status
                    if (addressStatus) {
                        addressStatus.style.display = 'none';
                    }
                    
                    if (data.success && data.address) {
                        // Update display text
                        addressDisplay.textContent = data.address.length > 50 ? 
                            data.address.substring(0, 50) + '...' : data.address;
                        
                        // Update tooltip with full address
                        if (tooltipElement) {
                            tooltipElement.setAttribute('title', data.address);
                            // Update existing tooltip
                            const existingTooltip = bootstrap.Tooltip.getInstance(tooltipElement);
                            if (existingTooltip) {
                                existingTooltip.setContent({'.tooltip-inner': data.address});
                            }
                        }
                        
                        // Update status
                        const statusText = addressStatus.querySelector('.address-text');
                        if (statusText) {
                            statusText.textContent = 'Adres yüklendi';
                            statusText.parentElement.classList.remove('text-muted');
                            statusText.parentElement.classList.add('text-success');
                        }
                    } else {
                        addressDisplay.textContent = 'Adres bulunamadı';
                        if (tooltipElement) {
                            tooltipElement.setAttribute('title', 'Adres bulunamadı');
                        }
                        
                        const statusText = addressStatus.querySelector('.address-text');
                        if (statusText) {
                            statusText.textContent = 'Adres bulunamadı';
                            statusText.parentElement.classList.remove('text-muted');
                            statusText.parentElement.classList.add('text-warning');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading address:', error);
                    
                    // Hide loading status
                    if (addressStatus) {
                        addressStatus.style.display = 'none';
                    }
                    
                    addressDisplay.textContent = 'Adres yüklenemedi';
                    if (tooltipElement) {
                        tooltipElement.setAttribute('title', 'Adres yüklenemedi');
                    }
                    
                    const statusText = addressStatus.querySelector('.address-text');
                    if (statusText) {
                        statusText.textContent = 'Yükleme hatası';
                        statusText.parentElement.classList.remove('text-muted');
                        statusText.parentElement.classList.add('text-danger');
                    }
                });
            }
        });
    });
</script>
@endpush
