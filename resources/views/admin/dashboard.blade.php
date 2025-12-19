@extends('layouts.vertical', ['title' => 'İzmir Time Machine - Admin Panel', 'topbarTitle' => 'Admin Dashboard'])

@section('content')

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
            <h4 class="page-title">İzmir Time Machine - Admin Panel</h4>
        </div>
    </div>
</div>


<!-- Hızlı İşlemler -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="header-title">Hızlı İşlemler</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.regions.create') }}" class="btn btn-primary w-100">
                            <i class="mdi mdi-plus-circle me-2"></i>
                            Yeni Bölge Ekle
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.sub-regions.create') }}" class="btn btn-success w-100">
                            <i class="mdi mdi-plus-circle me-2"></i>
                            Alt Bölge Ekle
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.archaeological-sites.create') }}" class="btn btn-danger w-100">
                            <i class="mdi mdi-plus-circle me-2"></i>
                            Ören Yeri Ekle
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.onboarding-slides.create') }}" class="btn btn-info w-100">
                            <i class="mdi mdi-plus-circle me-2"></i>
                            Onboarding Ekle
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.stories.create') }}" class="btn btn-purple w-100">
                            <i class="mdi mdi-plus-circle me-2"></i>
                            Hikaye Ekle
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.articles.create') }}" class="btn btn-info w-100">
                            <i class="mdi mdi-plus-circle me-2"></i>
                            Yazı Ekle
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.memories.create') }}" class="btn btn-warning w-100">
                            <i class="mdi mdi-plus-circle me-2"></i>
                            Hafıza Ekle
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.devices.index') }}" class="btn btn-info w-100">
                            <i class="mdi mdi-view-list me-2"></i>
                            Cihazları Gör
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.regions.index') }}" class="btn btn-warning w-100">
                            <i class="mdi mdi-view-list me-2"></i>
                            Tüm Bölgeleri Gör
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.archaeological-sites.index') }}" class="btn btn-outline-danger w-100">
                            <i class="mdi mdi-view-list me-2"></i>
                            Tüm Ören Yerlerini Gör
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.models-3d.create') }}" class="btn btn-success w-100">
                            <i class="mdi mdi-cube-outline me-2"></i>
                            3D Model Ekle
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.stories.index') }}" class="btn btn-outline-purple w-100">
                            <i class="mdi mdi-view-list me-2"></i>
                            Tüm Hikayeleri Gör
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-info w-100">
                            <i class="mdi mdi-view-list me-2"></i>
                            Tüm Yazıları Gör
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.memories.index') }}" class="btn btn-outline-warning w-100">
                            <i class="mdi mdi-view-list me-2"></i>
                            Tüm İçerikleri Gör
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.models-3d.index') }}" class="btn btn-outline-success w-100">
                            <i class="mdi mdi-view-grid me-2"></i>
                            Tüm 3D Modelleri Gör
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Son Eklenen Bölgeler</h4>
            </div>
            <div class="card-body">
                @if($recent_regions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-centered mb-0">
                            <thead>
                                <tr>
                                    <th>Bölge</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_regions as $region)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="badge" style="background-color: {{ $region->color_code }}; width: 20px; height: 20px; border-radius: 50%;"></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $region->name }}</h6>
                                                    <p class="mb-0 text-muted small">{{ Str::limit($region->description, 50) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($region->is_active)
                                                <span class="badge badge-success-lighten">Aktif</span>
                                            @else
                                                <span class="badge badge-danger-lighten">Pasif</span>
                                            @endif
                                        </td>
                                        <td>{{ $region->created_at->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('admin.regions.edit', $region) }}" class="btn btn-xs btn-primary">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-map-marker-off text-muted" style="font-size: 48px;"></i>
                        <h5 class="text-muted mt-2">Henüz bölge eklenmemiş</h5>
                        <p class="text-muted">İlk bölgenizi eklemek için butona tıklayın.</p>
                        <a href="{{ route('admin.regions.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle me-1"></i> Bölge Ekle
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Sistem Bilgileri -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Sistem Durumu</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-muted">Dil Desteği</h5>
                            <h3 class="text-success">
                                <i class="mdi mdi-translate me-1"></i>
                                {{ count(config('translatable.locales')) }} Dil
                            </h3>
                            <p class="text-muted mb-0">
                                {{ implode(', ', array_map('strtoupper', config('translatable.locales'))) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-muted">Varsayılan Dil</h5>
                            <h3 class="text-info">
                                <i class="mdi mdi-flag me-1"></i>
                                {{ strtoupper(config('translatable.fallback_locale')) }}
                            </h3>
                            <p class="text-muted mb-0">Türkçe</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-muted">Veritabanı</h5>
                            <h3 class="text-success">
                                <i class="mdi mdi-database-check me-1"></i>
                                Aktif
                            </h3>
                            <p class="text-muted mb-0">MySQL Bağlantısı</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-muted">API Durumu</h5>
                            <h3 class="text-success">
                                <i class="mdi mdi-api me-1"></i>
                                Çalışıyor
                            </h3>
                            <p class="text-muted mb-0">Mobil App Hazır</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    // Dashboard işlemleri için JavaScript kodu buraya eklenebilir
    
    // Kart animasyonları
    $('.card').hover(function() {
        $(this).addClass('shadow-lg').removeClass('shadow');
    }, function() {
        $(this).removeClass('shadow-lg').addClass('shadow');
    });
</script>
@endsection