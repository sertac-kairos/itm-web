@extends('layouts.vertical', ['title' => $archaeologicalSite->name . ' - Ören Yeri Detayı', 'topbarTitle' => 'Ören Yeri Detayı'])

@section('content')

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.regions.index') }}">Bölgeler</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.archaeological-sites.index') }}">Ören Yerleri</a></li>
                    <li class="breadcrumb-item active">{{ $archaeologicalSite->name }}</li>
                </ol>
            </div>
            <h4 class="page-title">Ören Yeri Detayı</h4>
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
                            @if($archaeologicalSite->image)
                                <img src="{{ asset('storage/' . $archaeologicalSite->image) }}" alt="Site Image" class="rounded me-3" height="64">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                    <i class="mdi mdi-castle text-muted" style="font-size: 24px;"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="mb-1">
                                    <span class="badge me-2" style="background-color: {{ $archaeologicalSite->subRegion->region->color_code }}; width: 20px; height: 20px; border-radius: 50%;"></span>
                                    {{ $archaeologicalSite->name }}
                                </h4>
                                <p class="text-muted mb-1">
                                    <span class="fw-semibold">{{ $archaeologicalSite->subRegion->region->name }}</span> > 
                                    {{ $archaeologicalSite->subRegion->name }}
                                </p>
                                <div class="d-flex align-items-center">
                                    @if($archaeologicalSite->is_active)
                                        <span class="badge badge bg-success text-white me-2">
                                            <i class="mdi mdi-check-circle me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge badge bg-danger text-white me-2">
                                            <i class="mdi mdi-close-circle me-1"></i>Pasif
                                        </span>
                                    @endif
                                    @if($archaeologicalSite->is_nearby_enabled)
                                        <span class="badge badge bg-info text-white me-2">
                                            <i class="mdi mdi-radar me-1"></i>Yakındaki Yerler
                                        </span>
                                    @endif
                                    <span class="badge badge bg-warning text-white">
                                        <i class="mdi mdi-map-marker me-1"></i>
                                        {{ number_format($archaeologicalSite->latitude, 4) }}°, {{ number_format($archaeologicalSite->longitude, 4) }}°
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.archaeological-sites.index', [
                                'sub_region_id' => $archaeologicalSite->sub_region_id,
                                'region_id' => $archaeologicalSite->subRegion->region_id
                            ]) }}" class="btn btn-secondary me-2">
                                <i class="mdi mdi-arrow-left me-1"></i>Ören Yerlerine Dön
                            </a>
                            <a href="{{ route('admin.archaeological-sites.edit', $archaeologicalSite) }}" class="btn btn-primary">
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
                                <th>Ses Rehberi</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(config('translatable.locales') as $locale)
                                @php $translation = $archaeologicalSite->translate($locale, false) @endphp
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
                                        @if($translation && $translation->audio_guide_path && Storage::disk('public')->exists($translation->audio_guide_path))
                                            <div class="d-flex align-items-center">
                                                <audio controls class="me-2" style="width: 200px;">
                                                    <source src="{{ asset('storage/' . $translation->audio_guide_path) }}" type="audio/mpeg">
                                                    Tarayıcınız ses dosyasını desteklemiyor.
                                                </audio>
                                                <span class="badge badge bg-success text-white">
                                                    <i class="mdi mdi-volume-high me-1"></i>Mevcut
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="mdi mdi-volume-off me-1"></i>Ses dosyası yok
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($translation && $translation->name)
                                            <span class="badge badge bg-success text-white">
                                                <i class="mdi mdi-check me-1"></i>Mevcut
                                            </span>
                                        @else
                                            <span class="badge badge bg-warning text-white">
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
        <div class="card" style="display:none;">
            <div class="card-header">
                <h4 class="header-title">İçerik İstatistikleri</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-6">
                        <div class="text-center p-3">
                            <div class="mb-2">
                                <i class="mdi mdi-cube-scan" style="font-size: 32px; color: #39afd1;"></i>
                            </div>
                            <h4 class="mb-1">{{ $archaeologicalSite->models3d->count() }}</h4>
                            <p class="text-muted mb-0">3D Model</p>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <!-- 3D Modeller -->
        @if($archaeologicalSite->models3d->count() > 0)
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="header-title mb-0">3D Modeller</h4>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Model Adı</th>
                                    <th>Sıralama</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($archaeologicalSite->models3d as $model)
                                    <tr>
                                        <td>
                                            <h6 class="mb-1">{{ $model->name }}</h6>
                                            <p class="text-muted mb-0">{{ Str::limit($model->description, 80) }}</p>
                                        </td>
                                        <td>
                                            <span class="badge badge bg-dark text-white">{{ $model->sort_order }}</span>
                                        </td>
                                        <td>
                                            @if($model->is_active)
                                                <span class="badge badge bg-success text-white">Aktif</span>
                                            @else
                                                <span class="badge badge bg-danger text-white">Pasif</span>
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
                                <td class="text-end">#{{ $archaeologicalSite->id }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Ana Bölge:</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.regions.show', $archaeologicalSite->subRegion->region) }}" class="text-decoration-none">
                                        {{ $archaeologicalSite->subRegion->region->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Alt Bölge:</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.sub-regions.show', $archaeologicalSite->subRegion) }}" class="text-decoration-none">
                                        {{ $archaeologicalSite->subRegion->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Yakındaki Yerler:</td>
                                <td class="text-end">
                                    @if($archaeologicalSite->is_nearby_enabled)
                                        <span class="badge badge bg-success text-white">Aktif</span>
                                    @else
                                        <span class="badge badge bg-danger text-white">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Durum:</td>
                                <td class="text-end">
                                    @if($archaeologicalSite->is_active)
                                        <span class="badge badge bg-success text-white">Aktif</span>
                                    @else
                                        <span class="badge badge bg-danger text-white">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Oluşturulma:</td>
                                <td class="text-end">{{ $archaeologicalSite->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Güncellenme:</td>
                                <td class="text-end">{{ $archaeologicalSite->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Konum Bilgileri -->
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
                                <td class="text-end"><code>{{ number_format($archaeologicalSite->latitude, 6) }}°</code></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Boylam:</td>
                                <td class="text-end"><code>{{ number_format($archaeologicalSite->longitude, 6) }}°</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="https://www.google.com/maps?q={{ $archaeologicalSite->latitude }},{{ $archaeologicalSite->longitude }}" 
                       target="_blank" class="btn btn-sm btn-outline-primary w-100">
                        <i class="mdi mdi-map-marker me-1"></i>Google Maps'te Aç
                    </a>
                </div>
            </div>
        </div>

        <!-- Hızlı İşlemler -->
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Hızlı İşlemler</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.archaeological-sites.edit', $archaeologicalSite) }}" class="btn btn-primary">
                        <i class="mdi mdi-pencil me-1"></i>Ören Yerini Düzenle
                    </a>
                    
                    <button type="button" class="btn btn-outline-danger" 
                            onclick="confirmDelete('{{ $archaeologicalSite->id }}', '{{ $archaeologicalSite->name }}')">
                        <i class="mdi mdi-delete me-1"></i>Ören Yerini Sil
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

@section('script')
<script>
    function confirmDelete(siteId, siteName) {
        document.getElementById('siteName').textContent = siteName;
        document.getElementById('deleteForm').action = '{{ url("admin/archaeological-sites") }}/' + siteId;
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endsection

