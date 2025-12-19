@extends('layouts.vertical', ['title' => $subRegion->name . ' - Alt Bölge Detayı', 'topbarTitle' => 'Alt Bölge Detayı'])

@section('content')

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.regions.index') }}">Bölgeler</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sub-regions.index') }}">Alt Bölgeler</a></li>
                    <li class="breadcrumb-item active">{{ $subRegion->name }}</li>
                </ol>
            </div>
            <h4 class="page-title">Alt Bölge Detayı</h4>
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
                        <div class="d-flex align-items-center">
                            @if($subRegion->image)
                                <img src="{{ asset('storage/' . $subRegion->image) }}" alt="SubRegion Image" class="rounded me-3" height="64">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                    <i class="mdi mdi-image text-muted" style="font-size: 24px;"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="mb-1">
                                    <span class="badge me-2" style="background-color: {{ $subRegion->region->color_code }}; width: 20px; height: 20px; border-radius: 50%;"></span>
                                    {{ $subRegion->name }}
                                </h4>
                                <p class="text-muted mb-1">{{ $subRegion->region->name }} bölgesine bağlı alt bölge</p>
                                <div class="d-flex align-items-center">
                                    @if($subRegion->is_active)
                                        <span class="badge badge-success-lighten me-2">
                                            <i class="mdi mdi-check-circle me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge badge-danger-lighten me-2">
                                            <i class="mdi mdi-close-circle me-1"></i>Pasif
                                        </span>
                                    @endif
                                    <span class="badge badge-dark-lighten me-2">Sıra: {{ $subRegion->sort_order }}</span>
                                    @if($subRegion->latitude && $subRegion->longitude)
                                        <span class="badge badge-info-lighten">
                                            <i class="mdi mdi-map-marker me-1"></i>
                                            {{ number_format($subRegion->latitude, 4) }}°, {{ number_format($subRegion->longitude, 4) }}°
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.sub-regions.index', ['region_id' => $subRegion->region_id]) }}" class="btn btn-secondary me-2">
                                <i class="mdi mdi-arrow-left me-1"></i>Alt Bölgelere Dön
                            </a>
                            <a href="{{ route('admin.sub-regions.edit', $subRegion) }}" class="btn btn-primary">
                                <i class="mdi mdi-pencil me-1"></i>Düzenle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sol Kolon - Temel Bilgiler -->
    <div class="col-lg-8">
        <!-- Çeviriler -->
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Dil Çevirileri</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Dil</th>
                                <th>Ad</th>
                                <th>Açıklama</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(config('translatable.locales') as $locale)
                                @php $translation = $subRegion->translate($locale, false) @endphp
                                <tr>
                                    <td>
                                        <span class="badge badge-soft-info">{{ strtoupper($locale) }}</span>
                                    </td>
                                    <td>
                                        @if($translation && $translation->name)
                                            <strong>{{ $translation->name }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($translation && $translation->description)
                                            {{ Str::limit($translation->description, 100) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($translation && $translation->name)
                                            <span class="badge badge-success-lighten">
                                                <i class="mdi mdi-check me-1"></i>Mevcut
                                            </span>
                                        @else
                                            <span class="badge badge-warning-lighten">
                                                <i class="mdi mdi-close me-1"></i>Eksik
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- İçerik İstatistikleri -->
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">İçerik İstatistikleri</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="text-center p-3">
                            <div class="mb-2">
                                <i class="mdi mdi-castle" style="font-size: 32px; color: #fa5c7c;"></i>
                            </div>
                            <h4 class="mb-1">{{ $subRegion->archaeologicalSites->count() }}</h4>
                            <p class="text-muted mb-0">Arkeolojik Sit</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="text-center p-3">
                            <div class="mb-2">
                                <i class="mdi mdi-cube-scan" style="font-size: 32px; color: #39afd1;"></i>
                            </div>
                            <h4 class="mb-1">{{ $subRegion->models3d->count() }}</h4>
                            <p class="text-muted mb-0">3D Model</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="text-center p-3">
                            <div class="mb-2">
                                <i class="mdi mdi-volume-high" style="font-size: 32px; color: #ffbc00;"></i>
                            </div>
                            <h4 class="mb-1">{{ $subRegion->audioGuides->count() }}</h4>
                            <p class="text-muted mb-0">Ses Rehberi</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="text-center p-3">
                            <div class="mb-2">
                                <i class="mdi mdi-qrcode" style="font-size: 32px; color: #6c757d;"></i>
                            </div>
                            <h4 class="mb-1">{{ $subRegion->qrCodes->count() }}</h4>
                            <p class="text-muted mb-0">QR Kod</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Arkeolojik Sitler -->
        @if($subRegion->archaeologicalSites->count() > 0)
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="header-title mb-0">Arkeolojik Sitler</h4>
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i class="mdi mdi-plus me-1"></i>Yeni Site Ekle
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Site Adı</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subRegion->archaeologicalSites as $site)
                                    <tr>
                                        <td>
                                            <h6 class="mb-1">{{ $site->name }}</h6>
                                            <p class="text-muted mb-0">{{ Str::limit($site->description, 80) }}</p>
                                        </td>
                                        <td>
                                            @if($site->is_active)
                                                <span class="badge badge-success-lighten">Aktif</span>
                                            @else
                                                <span class="badge badge-danger-lighten">Pasif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="#" class="btn btn-soft-primary" title="Görüntüle">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-soft-success" title="Düzenle">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Sağ Kolon - Detaylar -->
    <div class="col-lg-4">
        <!-- Temel Bilgiler -->
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Temel Bilgiler</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-semibold">ID:</td>
                                <td class="text-end">#{{ $subRegion->id }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Ana Bölge:</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.regions.show', $subRegion->region) }}" class="text-decoration-none">
                                        {{ $subRegion->region->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Sıralama:</td>
                                <td class="text-end">{{ $subRegion->sort_order }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Durum:</td>
                                <td class="text-end">
                                    @if($subRegion->is_active)
                                        <span class="badge badge-success-lighten">Aktif</span>
                                    @else
                                        <span class="badge badge-danger-lighten">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Oluşturulma:</td>
                                <td class="text-end">{{ $subRegion->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Güncellenme:</td>
                                <td class="text-end">{{ $subRegion->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Konum Bilgileri -->
        @if($subRegion->latitude && $subRegion->longitude)
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Konum Bilgileri</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">Enlem:</td>
                                    <td class="text-end"><code>{{ number_format($subRegion->latitude, 6) }}°</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Boylam:</td>
                                    <td class="text-end"><code>{{ number_format($subRegion->longitude, 6) }}°</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="https://www.google.com/maps?q={{ $subRegion->latitude }},{{ $subRegion->longitude }}" 
                           target="_blank" class="btn btn-sm btn-outline-primary w-100">
                            <i class="mdi mdi-map-marker me-1"></i>Google Maps'te Aç
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Hızlı İşlemler -->
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Hızlı İşlemler</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.sub-regions.edit', $subRegion) }}" class="btn btn-primary">
                        <i class="mdi mdi-pencil me-1"></i>Alt Bölgeyi Düzenle
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="mdi mdi-castle me-1"></i>Arkeolojik Sit Ekle
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="mdi mdi-cube-scan me-1"></i>3D Model Ekle
                    </a>
                    <a href="#" class="btn btn-outline-warning">
                        <i class="mdi mdi-volume-high me-1"></i>Ses Rehberi Ekle
                    </a>
                    <button type="button" class="btn btn-outline-danger" 
                            onclick="confirmDelete('{{ $subRegion->id }}', '{{ $subRegion->name }}')">
                        <i class="mdi mdi-delete me-1"></i>Alt Bölgeyi Sil
                    </button>
                </div>
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

@section('script')
<script>
    function confirmDelete(subRegionId, subRegionName) {
        document.getElementById('subRegionName').textContent = subRegionName;
        document.getElementById('deleteForm').action = '{{ url("admin/sub-regions") }}/' + subRegionId;
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endsection

