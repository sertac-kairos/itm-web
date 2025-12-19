@extends('layouts.vertical', ['title' => 'İstatistikler - İzmir Time Machine', 'topbarTitle' => 'İstatistikler'])

@section('content')

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">İstatistikler</li>
                </ol>
            </div>
            <h4 class="page-title">İstatistikler</h4>
        </div>
    </div>
</div>

<!-- Temel İstatistikler -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Temel İstatistikler</h4>
                <p class="text-muted mb-0">Sistemdeki toplam içerik ve kullanıcı sayıları</p>
            </div>
            <div class="card-body">
                <div class="row row-cols-xxl-4 row-cols-lg-3 row-cols-md-2 row-cols-1">
                    <div class="col">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-map-marker-multiple widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Toplam Bölgeler">Toplam Bölgeler</h5>
                                <h3 class="mt-3 mb-3">{{ $stats['regions_count'] }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i> {{ $stats['active_regions_count'] }}</span>
                                    <span class="text-nowrap">Aktif Bölge</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-map-marker widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Alt Bölgeler">Alt Bölgeler</h5>
                                <h3 class="mt-3 mb-3">{{ $stats['sub_regions_count'] }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Toplam Alt Bölge</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-ancient-sword widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Ören Yerleri">Ören Yerleri</h5>
                                <h3 class="mt-3 mb-3">{{ $stats['archaeological_sites_count'] }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Antik Kalıntılar</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-cellphone widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Cihazlar">Cihazlar</h5>
                                <h3 class="mt-3 mb-3">{{ $stats['devices_count'] }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Mobil Cihazlar</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-book-open-page-variant widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Hikayeler">Hikayeler</h5>
                                <h3 class="mt-3 mb-3">{{ $stats['stories_count'] }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Tarihi Hikayeler</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-file-document-edit widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Yazılar">Yazılar</h5>
                                <h3 class="mt-3 mb-3">{{ $stats['articles_count'] }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Makaleler</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-brain widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="Hafıza İzmir">Hafıza İzmir</h5>
                                <h3 class="mt-3 mb-3">{{ $stats['memories_count'] }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Tarihi Hafızalar</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-cube widget-icon"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0" title="3D Modeller">3D Modeller</h5>
                                <h3 class="mt-3 mb-3">{{ $stats['models_3d_count'] }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">3D Görselleştirme</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kullanıcı Aktivite İstatistikleri -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Kullanıcı Aktivite İstatistikleri</h4>
                <p class="text-muted mb-0">Kullanıcı aktiviteleri ve cihaz kayıtları</p>
            </div>
            <div class="card-body">
                <div class="row row-cols-xxl-4 row-cols-lg-3 row-cols-md-2 row-cols-1">
                    <div class="col">
                        <div class="card widget-flat bg-primary text-white">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-account-clock widget-icon"></i>
                                </div>
                                <h5 class="text-white-50 fw-normal mt-0">Son 1 Saat</h5>
                                <h3 class="mt-3 mb-3 text-white">{{ $advancedStats['active_users_last_hour'] }}</h3>
                                <p class="mb-0 text-white-50">
                                    <span class="text-nowrap">Aktif Kullanıcı</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat bg-success text-white">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-account-multiple widget-icon"></i>
                                </div>
                                <h5 class="text-white-50 fw-normal mt-0">Son 24 Saat</h5>
                                <h3 class="mt-3 mb-3 text-white">{{ $advancedStats['active_users_last_24h'] }}</h3>
                                <p class="mb-0 text-white-50">
                                    <span class="text-nowrap">Aktif Kullanıcı</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat bg-info text-white">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-account-group widget-icon"></i>
                                </div>
                                <h5 class="text-white-50 fw-normal mt-0">Son 7 Gün</h5>
                                <h3 class="mt-3 mb-3 text-white">{{ $advancedStats['total_active_users'] }}</h3>
                                <p class="mb-0 text-white-50">
                                    <span class="text-nowrap">Toplam Aktif</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat bg-warning text-white">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-cellphone-plus widget-icon"></i>
                                </div>
                                <h5 class="text-white-50 fw-normal mt-0">Bugün</h5>
                                <h3 class="mt-3 mb-3 text-white">{{ $advancedStats['new_devices_today'] }}</h3>
                                <p class="mb-0 text-white-50">
                                    <span class="text-nowrap">Yeni Cihaz</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- İçerik Oluşturma İstatistikleri -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">İçerik Oluşturma İstatistikleri</h4>
                <p class="text-muted mb-0">Belirli zaman dilimlerinde oluşturulan içerik sayıları</p>
            </div>
            <div class="card-body">
                <div class="row row-cols-xxl-3 row-cols-lg-3 row-cols-md-2 row-cols-1">
                    <div class="col">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-calendar-today widget-icon text-primary"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0">Bugün</h5>
                                <h3 class="mt-3 mb-3 text-primary">{{ $advancedStats['content_created_today'] }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Yeni İçerik</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-calendar-week widget-icon text-success"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0">Bu Hafta</h5>
                                <h3 class="mt-3 mb-3 text-success">{{ $advancedStats['content_created_this_week'] }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Yeni İçerik</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="mdi mdi-calendar-month widget-icon text-info"></i>
                                </div>
                                <h5 class="text-muted fw-normal mt-0">Bu Ay</h5>
                                <h3 class="mt-3 mb-3 text-info">{{ $advancedStats['content_created_this_month'] }}</h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Yeni İçerik</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafikler -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Cihaz Kayıt Trendi</h4>
                <p class="text-muted mb-0">Son 7 günlük cihaz kayıt sayıları</p>
            </div>
            <div class="card-body">
                <canvas id="devicesChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">İçerik Oluşturma Trendi</h4>
                <p class="text-muted mb-0">Son 7 günlük içerik oluşturma sayıları</p>
            </div>
            <div class="card-body">
                <canvas id="contentChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Son Aktiviteler ve En Aktif Bölgeler -->
<div class="row">
    <!-- Son Aktiviteler -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Son Aktiviteler</h4>
                <p class="text-muted mb-0">Sistemdeki son oluşturulan içerikler</p>
            </div>
            <div class="card-body">
                @if($advancedStats['recent_activity']->count() > 0)
                    <div class="timeline">
                        @foreach($advancedStats['recent_activity'] as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="mdi {{ $activity['icon'] }}"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ $activity['name'] }}</h6>
                                <p class="timeline-text text-muted">
                                    {{ ucfirst(str_replace('_', ' ', $activity['type'])) }} oluşturuldu
                                </p>
                                <small class="text-muted">{{ $activity['created_at']->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-information-outline fs-48 text-muted"></i>
                        <p class="text-muted mt-2">Henüz aktivite verisi bulunmuyor</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- En Aktif Bölgeler -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">En Aktif Bölgeler</h4>
                <p class="text-muted mb-0">Alt bölge sayısına göre sıralanmış bölgeler</p>
            </div>
            <div class="card-body">
                @if($advancedStats['most_active_regions']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-centered mb-0">
                            <thead>
                                <tr>
                                    <th>Bölge Adı</th>
                                    <th>Alt Bölge</th>
                                    <th>Ören Yeri</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($advancedStats['most_active_regions'] as $region)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <div class="avatar-xs">
                                                    <span class="avatar-title rounded-circle" style="background-color: {{ $region->color_code }};"></span>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $region->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $region->sub_regions_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $region->archaeological_sites_count ?? 0 }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-information-outline fs-48 text-muted"></i>
                        <p class="text-muted mt-2">Henüz bölge verisi bulunmuyor</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cihaz Kayıt Trendi Grafiği
    const devicesCtx = document.getElementById('devicesChart').getContext('2d');
    new Chart(devicesCtx, {
        type: 'line',
        data: {
            labels: @json(array_column($chartData['devices_over_time'], 'date')),
            datasets: [{
                label: 'Yeni Cihazlar',
                data: @json(array_column($chartData['devices_over_time'], 'count')),
                borderColor: '#188ae2',
                backgroundColor: 'rgba(24, 138, 226, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // İçerik Oluşturma Trendi Grafiği
    const contentCtx = document.getElementById('contentChart').getContext('2d');
    new Chart(contentCtx, {
        type: 'bar',
        data: {
            labels: @json(array_column($chartData['content_creation_trend'], 'date')),
            datasets: [{
                label: 'Yeni İçerikler',
                data: @json(array_column($chartData['content_creation_trend'], 'count')),
                backgroundColor: '#10c469',
                borderColor: '#10c469',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection
