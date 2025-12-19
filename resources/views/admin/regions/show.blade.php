@extends('layouts.vertical', ['title' => 'Bölge Detayı - İzmir Time Machine', 'topbarTitle' => 'Bölge Detayı'])

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.regions.index') }}">Bölgeler</a></li>
                        <li class="breadcrumb-item active">Bölge Detayı</li>
                    </ol>
                </div>
                <h4 class="page-title">Bölge Detayı: {{ $region->translate(app()->getLocale())->name ?? 'İsimsiz Bölge' }}</h4>
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
                            <td><span class="badge badge-soft-secondary">#{{ $region->id }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Renk Kodu:</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="color-preview me-2" style="width: 20px; height: 20px; background-color: {{ $region->color_code }}; border-radius: 4px; border: 1px solid #ddd;"></div>
                                    <code>{{ $region->color_code }}</code>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Sıralama:</td>
                            <td>{{ $region->sort_order }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Koordinatlar:</td>
                            <td>
                                @if($region->latitude && $region->longitude)
                                    <span class="badge badge-soft-info">
                                        {{ number_format($region->latitude, 6) }}, {{ number_format($region->longitude, 6) }}
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        <i class="mdi mdi-map-marker me-1"></i>
                                        Harita konumu
                                    </small>
                                @else
                                    <span class="text-muted">Belirtilmemiş</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Durum:</td>
                            <td>
                                @if($region->is_active)
                                    <span class="badge badge-success-lighten">
                                        <i class="mdi mdi-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge badge-danger-lighten">
                                        <i class="mdi mdi-close-circle me-1"></i>Pasif
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Oluşturulma:</td>
                            <td>{{ $region->created_at->format('d.m.Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Güncellenme:</td>
                            <td>{{ $region->updated_at->format('d.m.Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Main Image -->
            @if($region->main_image)
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Ana Görsel</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $region->main_image) }}" 
                                 alt="Bölge Görseli" 
                                 class="img-fluid rounded" 
                                 style="max-height: 200px;">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.regions.edit', $region) }}" class="btn btn-success">
                            <i class="mdi mdi-pencil me-1"></i>
                            Düzenle
                        </a>
                        <a href="{{ route('admin.regions.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Geri Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Translations -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Çeviriler</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-bordered" role="tablist">
                        @foreach(config('translatable.locales') as $locale)
                            <li class="nav-item">
                                <a class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                   data-bs-toggle="tab" 
                                   href="#tab-{{ $locale }}" 
                                   role="tab">
                                    <span class="d-none d-sm-block">
                                        @switch($locale)
                                            @case('tr')
                                                🇹🇷 Türkçe
                                                @break
                                            @case('en')
                                                🇬🇧 English
                                                @break
                                            @case('de')
                                                🇩🇪 Deutsch
                                                @break
                                            @default
                                                {{ strtoupper($locale) }}
                                        @endswitch
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content">
                        @foreach(config('translatable.locales') as $locale)
                            <div class="tab-pane {{ $loop->first ? 'active' : '' }}" 
                                 id="tab-{{ $locale }}" 
                                 role="tabpanel">
                                <div class="p-3">
                                    @php
                                        $translation = $region->translate($locale);
                                    @endphp
                                    
                                    @if($translation)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Bölge Adı:</label>
                                                    <div class="form-control-plaintext">
                                                        {{ $translation->name ?? 'Girilmemiş' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Alt Başlık:</label>
                                                    <div class="form-control-plaintext">
                                                        {{ $translation->subtitle ?? 'Girilmemiş' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Açıklama:</label>
                                            <div class="form-control-plaintext">
                                                {{ $translation->description ?? 'Girilmemiş' }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i class="mdi mdi-translate text-muted" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2 text-muted">Bu dil için çeviri bulunamadı</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sub Regions -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="header-title">Alt Bölgeler</h4>
                    <a href="{{ route('admin.sub-regions.create') }}" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-plus me-1"></i>
                        Yeni Alt Bölge
                    </a>
                </div>
                <div class="card-body">
                    @if($region->subRegions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Alt Bölge Adı</th>
                                        <th>Sıralama</th>
                                        <th>Durum</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($region->subRegions as $subRegion)
                                        <tr>
                                            <td>
                                                <span class="badge badge-soft-secondary">#{{ $subRegion->id }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <h6 class="text-dark mb-1">
                                                        {{ $subRegion->translate(app()->getLocale())->name ?? 'İsimsiz' }}
                                                    </h6>
                                                    <p class="text-muted mb-0">
                                                        <small>
                                                            {{ $subRegion->translate(app()->getLocale())->subtitle ?? 'Alt başlık yok' }}
                                                        </small>
                                                    </p>
                                                </div>
                                            </td>
                                            <td>{{ $subRegion->sort_order }}</td>
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
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.sub-regions.show', $subRegion) }}" 
                                                       class="btn btn-soft-primary btn-sm" title="Görüntüle">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.sub-regions.edit', $subRegion) }}" 
                                                       class="btn btn-soft-success btn-sm" title="Düzenle">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-map-marker-off text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-2">Henüz alt bölge yok</h5>
                            <p class="text-muted">Bu bölge için henüz alt bölge tanımlanmamış.</p>
                            <a href="{{ route('admin.sub-regions.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i>
                                İlk Alt Bölgeyi Ekle
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Archaeological Sites -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="header-title">Arkeolojik Alanlar</h4>
                    <a href="{{ route('admin.archaeological-sites.create') }}" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-plus me-1"></i>
                        Yeni Alan
                    </a>
                </div>
                <div class="card-body">
                    @php
                        $archaeologicalSites = $region->archaeologicalSites ?? collect();
                    @endphp
                    
                    @if($archaeologicalSites->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Alan Adı</th>
                                        <th>Alt Bölge</th>
                                        <th>Durum</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($archaeologicalSites as $site)
                                        <tr>
                                            <td>
                                                <span class="badge badge-soft-secondary">#{{ $site->id }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <h6 class="text-dark mb-1">
                                                        {{ $site->translate(app()->getLocale())->name ?? 'İsimsiz' }}
                                                    </h6>
                                                    <p class="text-muted mb-0">
                                                        <small>
                                                            {{ $site->translate(app()->getLocale())->subtitle ?? 'Alt başlık yok' }}
                                                        </small>
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                @if($site->subRegion)
                                                    {{ $site->subRegion->translate(app()->getLocale())->name ?? 'İsimsiz' }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
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
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.archaeological-sites.show', $site) }}" 
                                                       class="btn btn-soft-primary btn-sm" title="Görüntüle">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.archaeological-sites.edit', $site) }}" 
                                                       class="btn btn-soft-success btn-sm" title="Düzenle">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-map-marker-off text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-2">Henüz arkeolojik alan yok</h5>
                            <p class="text-muted">Bu bölge için henüz arkeolojik alan tanımlanmamış.</p>
                            <a href="{{ route('admin.archaeological-sites.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i>
                                İlk Arkeolojik Alanı Ekle
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Tab functionality is handled by Bootstrap
    console.log('Region show page loaded');
</script>
@endpush
