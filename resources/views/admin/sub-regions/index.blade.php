@extends('layouts.vertical', ['title' => 'Alt Bölgeler Yönetimi - İzmir Time Machine', 'topbarTitle' => 'Alt Bölgeler'])

@section('content')

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.regions.index') }}">Bölgeler</a></li>
                    <li class="breadcrumb-item active">Alt Bölgeler</li>
                </ol>
            </div>
            <h4 class="page-title">Alt Bölgeler Yönetimi</h4>
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
                <form method="GET" action="{{ route('admin.sub-regions.index') }}">
                    <div class="row align-items-end">
                        <div class="col-lg-3">
                            <label for="region_id" class="form-label">Bölgeye Göre Filtrele</label>
                            <select class="form-select" id="region_id" name="region_id">
                                <option value="">Tüm Bölgeler</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label for="search" class="form-label">Arama</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Alt bölge adı veya açıklama...">
                        </div>
                        <div class="col-lg-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-magnify me-1"></i>Filtrele
                                </button>
                                <a href="{{ route('admin.sub-regions.index') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-refresh me-1"></i>Temizle
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-2 text-end">
                            <a href="{{ route('admin.sub-regions.create', request()->only('region_id')) }}" class="btn btn-success">
                                <i class="mdi mdi-plus-circle me-1"></i>Yeni Alt Bölge
                            </a>
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
                            <i class="mdi mdi-map-marker-path text-success me-2"></i>
                            Alt Bölgeler
                            <span class="badge badge-success-lighten ms-2">{{ $subRegions->total() }} Alt Bölge</span>
                        </h4>
                        <p class="text-muted mb-0">
                            @if(request('region_id'))
                                @php $selectedRegion = $regions->firstWhere('id', request('region_id')) @endphp
                                <strong>{{ $selectedRegion->name }}</strong> bölgesinin alt bölgelerini yönetin
                            @else
                                Tüm bölgelerin alt bölgelerini yönetin
                            @endif
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.regions.index') }}" class="btn btn-outline-primary me-2">
                                <i class="mdi mdi-arrow-left me-1"></i>Bölgelere Dön
                            </a>
                            <a href="{{ route('admin.sub-regions.create', request()->only('region_id')) }}" class="btn btn-success">
                                <i class="mdi mdi-plus-circle me-1"></i>Yeni Alt Bölge
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sub-Regions List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($subRegions->count() > 0)
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
                                    <th>Alt Bölge</th>
                                    <th>Ana Bölge</th>
                                    <th>Koordinatlar</th>
                                    <th>İçerikler</th>
                                    <th>Sıralama</th>
                                    <th>Durum</th>
                                    <th>Oluşturulma</th>
                                    <th style="width: 125px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subRegions as $subRegion)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="customCheck{{ $subRegion->id }}">
                                                <label class="form-check-label" for="customCheck{{ $subRegion->id }}">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($subRegion->image)
                                                    <img src="{{ asset('storage/' . $subRegion->image) }}" alt="SubRegion Image" class="rounded me-3" height="48">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                        <i class="mdi mdi-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h5 class="mb-1">
                                                        <a href="{{ route('admin.sub-regions.show', $subRegion) }}" class="text-dark">
                                                            {{ $subRegion->name }}
                                                        </a>
                                                    </h5>
                                                    <p class="mb-0 text-muted">{{ Str::limit($subRegion->description, 60) }}</p>
                                                    
                                                    <!-- Dil Badges -->
                                                    <div class="mt-1">
                                                        @foreach($subRegion->translations as $translation)
                                                            <span class="badge badge-soft-info me-1">{{ strtoupper($translation->locale) }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge me-2" style="background-color: {{ $subRegion->region->color_code }}; width: 16px; height: 16px; border-radius: 50%;"></span>
                                                <span class="fw-semibold">{{ $subRegion->region->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($subRegion->latitude && $subRegion->longitude)
                                                <div class="text-center">
                                                    <small class="text-muted d-block">
                                                        <i class="mdi mdi-map-marker me-1"></i>
                                                        {{ number_format($subRegion->latitude, 6) }}°
                                                    </small>
                                                    <small class="text-muted">
                                                        {{ number_format($subRegion->longitude, 6) }}°
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge badge-soft-primary">
                                                    {{ $subRegion->archaeologicalSites->count() }} Sit
                                                </span>
                                                <span class="badge badge-soft-info">
                                                    {{ $subRegion->models3d->count() }} 3D
                                                </span>
                                                <span class="badge badge-soft-warning">
                                                    {{ $subRegion->audioGuides->count() }} Ses
                                                </span>
                                                <span class="badge badge-soft-secondary">
                                                    {{ $subRegion->qrCodes->count() }} QR
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-dark-lighten">{{ $subRegion->sort_order }}</span>
                                        </td>
                                        <td>
                                            @if($subRegion->is_active)
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
                                            <span class="text-muted" title="{{ $subRegion->created_at->format('d.m.Y H:i') }}">
                                                {{ $subRegion->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.sub-regions.show', $subRegion) }}" class="btn btn-soft-primary btn-sm" title="Görüntüle">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.sub-regions.edit', $subRegion) }}" class="btn btn-soft-success btn-sm" title="Düzenle">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-soft-danger btn-sm" title="Sil" 
                                                        onclick="confirmDelete('{{ $subRegion->id }}', '{{ $subRegion->name }}')">
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
                    @if($subRegions->hasPages())
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="text-muted mb-0">
                                            {{ $subRegions->total() }} kayıttan {{ $subRegions->firstItem() }}-{{ $subRegions->lastItem() }} arası gösteriliyor
                                        </p>
                                    </div>
                                    <div>
                                        {{ $subRegions->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="mdi mdi-map-marker-off" style="font-size: 72px; color: #e3e6e9;"></i>
                        </div>
                        <h4 class="text-muted">
                            @if(request('region_id'))
                                Bu bölgeye ait alt bölge bulunamadı
                            @else
                                Henüz alt bölge eklenmemiş
                            @endif
                        </h4>
                        <p class="text-muted mb-4">
                            @if(request('region_id'))
                                Seçili bölge için alt bölge ekleyebilir veya filtreyi değiştirebilirsiniz.
                            @else
                                İzmir Time Machine uygulaması için ilk alt bölgenizi ekleyin.
                            @endif
                        </p>
                        <a href="{{ route('admin.sub-regions.create', request()->only('region_id')) }}" class="btn btn-success">
                            <i class="mdi mdi-plus-circle me-1"></i>
                            İlk Alt Bölgeyi Ekle
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
                <h5 class="modal-title" id="deleteModalLabel">Alt Bölgeyi Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="mdi mdi-alert-circle text-warning" style="font-size: 48px;"></i>
                    <h4 class="mt-3">Emin misiniz?</h4>
                    <p class="text-muted">
                        <strong id="subRegionName"></strong> alt bölgesini silmek üzeresiniz. 
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
    function confirmDelete(subRegionId, subRegionName) {
        document.getElementById('subRegionName').textContent = subRegionName;
        document.getElementById('deleteForm').action = '{{ url("admin/sub-regions") }}/' + subRegionId;
        
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

