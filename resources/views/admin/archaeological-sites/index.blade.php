@extends('layouts.vertical', ['title' => 'Ören Yerleri Yönetimi - İzmir Time Machine', 'topbarTitle' => 'Ören Yerleri'])

@section('content')

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.regions.index') }}">Bölgeler</a></li>
                    <li class="breadcrumb-item active">Ören Yerleri</li>
                </ol>
            </div>
            <h4 class="page-title">Ören Yerleri Yönetimi</h4>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle me-1"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filters -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.archaeological-sites.index') }}">
                    <div class="row align-items-end">
                        <div class="col-lg-2">
                            <label for="region_id" class="form-label">Bölge</label>
                            <select class="form-select" id="region_id" name="region_id">
                                <option value="">Tüm Bölgeler</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="sub_region_id" class="form-label">Alt Bölge</label>
                            <select class="form-select" id="sub_region_id" name="sub_region_id">
                                <option value="">Tüm Alt Bölgeler</option>
                                @foreach($subRegions as $subRegion)
                                    <option value="{{ $subRegion->id }}" 
                                            data-region="{{ $subRegion->region_id }}"
                                            {{ request('sub_region_id') == $subRegion->id ? 'selected' : '' }}>
                                        {{ $subRegion->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="status" class="form-label">Durum</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tümü</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Pasif</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label for="search" class="form-label">Arama</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Ören yeri adı veya açıklama...">
                        </div>
                        <div class="col-lg-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-magnify me-1"></i>Filtrele
                                </button>
                                <a href="{{ route('admin.archaeological-sites.index') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-refresh me-1"></i>Temizle
                                </a>
                                <a href="{{ route('admin.archaeological-sites.create', request()->only(['region_id', 'sub_region_id'])) }}" class="btn btn-success">
                                    <i class="mdi mdi-plus-circle me-1"></i>Yeni Ören Yeri
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Actions Bar -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h4 class="mb-0">
                            <i class="mdi mdi-castle text-danger me-2"></i>
                            Ören Yerleri
                            <span class="badge badge-danger-lighten ms-2">{{ $archaeologicalSites->total() }} Ören Yeri</span>
                        </h4>
                        <p class="text-muted mb-0">
                            @if(request('region_id') || request('sub_region_id'))
                                @php 
                                    $filterText = [];
                                    if(request('region_id')) {
                                        $selectedRegion = $regions->firstWhere('id', request('region_id'));
                                        $filterText[] = $selectedRegion->name . ' bölgesi';
                                    }
                                    if(request('sub_region_id')) {
                                        $selectedSubRegion = $subRegions->firstWhere('id', request('sub_region_id'));
                                        $filterText[] = $selectedSubRegion->name . ' alt bölgesi';
                                    }
                                @endphp
                                <strong>{{ implode(' - ', $filterText) }}</strong> ören yerlerini yönetin
                            @else
                                Tüm ören yerlerini yönetin
                            @endif
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.sub-regions.index') }}" class="btn btn-outline-primary me-2">
                                <i class="mdi mdi-arrow-left me-1"></i>Alt Bölgelere Dön
                            </a>
                            <a href="{{ route('admin.archaeological-sites.create', request()->only(['region_id', 'sub_region_id'])) }}" class="btn btn-success">
                                <i class="mdi mdi-plus-circle me-1"></i>Yeni Ören Yeri
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Archaeological Sites List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($archaeologicalSites->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="customCheck1">
                                            <label class="form-check-label" for="customCheck1">&nbsp;</label>
                                        </div>
                                    </th>
                                    <th>Ören Yeri</th>
                                    <th>Bölge</th>
                                    <th>Koordinatlar</th>
                                    <th>Özellikler</th>
                                    <th>İçerikler</th>
                                    <th>Durum</th>
                                    <th>Oluşturulma</th>
                                    <th style="width: 125px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($archaeologicalSites as $site)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="customCheck{{ $site->id }}">
                                                <label class="form-check-label" for="customCheck{{ $site->id }}">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($site->image)
                                                    <img src="{{ asset('storage/' . $site->image) }}" alt="Site Image" class="rounded me-3" height="48">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                        <i class="mdi mdi-castle text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h5 class="mb-1">
                                                        <a href="{{ route('admin.archaeological-sites.show', $site) }}" class="text-dark">
                                                            {{ $site->name }}
                                                        </a>
                                                    </h5>
                                                    <p class="mb-0 text-muted">{{ Str::limit($site->description, 60) }}</p>
                                                    
                                                    <!-- Dil Badges -->
                                                    <div class="mt-1">
                                                        @foreach($site->translations as $translation)
                                                            <span class="badge badge-soft-info me-1">{{ strtoupper($translation->locale) }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge me-2" style="background-color: {{ $site->subRegion->region->color_code }}; width: 16px; height: 16px; border-radius: 50%;"></span>
                                                    <span class="fw-semibold">{{ $site->subRegion->region->name }}</span>
                                                </div>
                                                <small class="text-muted">{{ $site->subRegion->name }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <small class="text-muted d-block">
                                                    <i class="mdi mdi-map-marker me-1"></i>
                                                    {{ number_format($site->latitude, 6) }}°
                                                </small>
                                                <small class="text-muted">
                                                    {{ number_format($site->longitude, 6) }}°
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @if($site->is_nearby_enabled)
                                                    <span class="badge badge-soft-success">
                                                        <i class="mdi mdi-radar me-1"></i>Yakındaki
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge badge-soft-info">
                                                    {{ $site->models3d->count() }} 3D
                                                </span>
                                                <span class="badge badge-soft-warning">
                                                    {{ $site->audioGuides->count() }} Ses
                                                </span>
                                                <span class="badge badge-soft-secondary">
                                                    {{ $site->qrCodes->count() }} QR
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($site->is_active)
                                                <span class="badge badge-success-lighten">
                                                    <i class="mdi mdi-check-circle me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge badge-danger-lighten">
                                                    <i class="mdi mdi-close-circle me-1"></i>Pasif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted" title="{{ $site->created_at->format('d.m.Y H:i') }}">
                                                {{ $site->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.archaeological-sites.show', $site) }}" class="btn btn-soft-primary btn-sm" title="Görüntüle">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.archaeological-sites.edit', $site) }}" class="btn btn-soft-success btn-sm" title="Düzenle">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-soft-danger btn-sm" title="Sil" 
                                                        onclick="confirmDelete('{{ $site->id }}', '{{ $site->name }}')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($archaeologicalSites->hasPages())
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="text-muted mb-0">
                                            {{ $archaeologicalSites->total() }} kayıttan {{ $archaeologicalSites->firstItem() }}-{{ $archaeologicalSites->lastItem() }} arası gösteriliyor
                                        </p>
                                    </div>
                                    <div>
                                        {{ $archaeologicalSites->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="mdi mdi-castle" style="font-size: 72px; color: #e3e6e9;"></i>
                        </div>
                        <h4 class="text-muted">
                            @if(request('region_id') || request('sub_region_id') || request('search'))
                                Arama kriterlerine uygun ören yeri bulunamadı
                            @else
                                Henüz ören yeri eklenmemiş
                            @endif
                        </h4>
                        <p class="text-muted mb-4">
                            @if(request('region_id') || request('sub_region_id') || request('search'))
                                Filtreleri değiştirerek tekrar deneyin veya yeni bir ören yeri ekleyin.
                            @else
                                İzmir Time Machine uygulaması için ilk ören yerini ekleyin.
                            @endif
                        </p>
                        <a href="{{ route('admin.archaeological-sites.create', request()->only(['region_id', 'sub_region_id'])) }}" class="btn btn-success">
                            <i class="mdi mdi-plus-circle me-1"></i>
                            İlk Ören Yerini Ekle
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Ören Yerini Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="mdi mdi-alert-circle text-warning" style="font-size: 48px;"></i>
                    <h4 class="mt-3">Emin misiniz?</h4>
                    <p class="text-muted">
                        <strong id="siteName"></strong> ören yerini silmek üzeresiniz. 
                        Bu işlem geri alınamaz ve tüm ilgili veriler silinecektir.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete me-1"></i>Evet, Sil
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function confirmDelete(siteId, siteName) {
        document.getElementById('siteName').textContent = siteName;
        document.getElementById('deleteForm').action = '{{ url("admin/archaeological-sites") }}/' + siteId;
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // Select all checkbox functionality
    document.getElementById('customCheck1').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Sub-region filtering based on region
    document.getElementById('region_id').addEventListener('change', function() {
        const regionId = this.value;
        const subRegionSelect = document.getElementById('sub_region_id');
        const subRegionOptions = subRegionSelect.querySelectorAll('option[data-region]');
        
        // Show/hide sub-region options based on selected region
        subRegionOptions.forEach(option => {
            if (!regionId || option.dataset.region === regionId) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
                if (option.selected) {
                    option.selected = false;
                }
            }
        });
        
        // Reset sub-region selection if no matching options
        if (regionId) {
            const visibleOptions = Array.from(subRegionOptions).filter(opt => opt.dataset.region === regionId);
            if (visibleOptions.length === 0) {
                subRegionSelect.value = '';
            }
        }
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endsection

