@extends('layouts.vertical', ['title' => 'Bölgeler Yönetimi - İzmir Time Machine', 'topbarTitle' => 'Bölgeler'])

@section('content')

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Bölgeler</li>
                </ol>
            </div>
            <h4 class="page-title">Bölgeler Yönetimi</h4>
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

<!-- Actions Bar -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h4 class="mb-0">
                            <i class="mdi mdi-map-marker-multiple text-primary me-2"></i>
                            Tüm Bölgeler
                            <span class="badge badge bg-primary text-white ms-2">{{ $regions->total() }} Bölge</span>
                        </h4>
                        <p class="text-muted mb-0">İzmir Time Machine uygulaması için tanımlı bölgeleri yönetin</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('admin.regions.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle me-1"></i>
                            Yeni Bölge Ekle
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Regions List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($regions->count() > 0)
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
                                    <th>
                                        <a href="{{ route('admin.regions.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Bölge
                                            @if(request('sort') === 'name' || request('sort') === 'id')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Renk</th>
                                    <th>Alt Bölgeler</th>
                                    <th>
                                        <a href="{{ route('admin.regions.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'latitude', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Koordinatlar
                                            @if(request('sort') === 'latitude' || request('sort') === 'longitude')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.regions.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'sort_order', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Sıralama
                                            @if(request('sort') === 'sort_order' || !request('sort'))
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.regions.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'is_active', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Durum
                                            @if(request('sort') === 'is_active')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.regions.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Oluşturulma
                                            @if(request('sort') === 'created_at')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="width: 125px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regions as $region)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="customCheck{{ $region->id }}">
                                                <label class="form-check-label" for="customCheck{{ $region->id }}">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($region->main_image)
                                                    <img src="{{ asset('storage/' . $region->main_image) }}" alt="Region Image" class="rounded me-3" height="48">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                        <i class="mdi mdi-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h5 class="mb-1">
                                                        <a href="{{ route('admin.regions.show', $region) }}" class="text-dark">
                                                            {{ $region->name }}
                                                        </a>
                                                    </h5>
                                                    <p class="mb-0 text-muted">{{ Str::limit($region->description, 80) }}</p>
                                                    
                                                    <!-- Dil Badges -->
                                                    <div class="mt-1">
                                                        @foreach($region->translations as $translation)
                                                            <span class="badge badge-soft-info me-1">{{ strtoupper($translation->locale) }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge me-2" style="background-color: {{ $region->color_code }}; width: 24px; height: 24px; border-radius: 50%;"></span>
                                                <code>{{ $region->color_code }}</code>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge bg-primary text-white">
                                                {{ $region->subRegions->count() }} Alt Bölge
                                            </span>
                                        </td>
                                        <td>
                                            @if($region->latitude && $region->longitude)
                                                <small class="text-muted">
                                                    {{ number_format($region->latitude, 6) }}, {{ number_format($region->longitude, 6) }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge bg-dark text-white">{{ $region->sort_order }}</span>
                                        </td>
                                        <td>
                                            @if($region->is_active)
                                                <span class="badge bg-success text-white">
                                                    <i class="mdi mdi-check-circle me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white">
                                                    <i class="mdi mdi-close-circle me-1"></i>Pasif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted" title="{{ $region->created_at->format('d.m.Y H:i') }}">
                                                {{ $region->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.regions.show', $region) }}" class="btn btn-soft-primary btn-sm" title="Görüntüle">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.regions.edit', $region) }}" class="btn btn-soft-success btn-sm" title="Düzenle">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-soft-danger btn-sm" title="Sil" 
                                                        onclick="confirmDelete('{{ $region->id }}', '{{ $region->name }}')">
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
                    @if($regions->hasPages())
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="text-muted mb-0">
                                            {{ $regions->total() }} kayıttan {{ $regions->firstItem() }}-{{ $regions->lastItem() }} arası gösteriliyor
                                        </p>
                                    </div>
                                    <div>
                                        {{ $regions->links() }}
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
                        <h4 class="text-muted">Henüz bölge eklenmemiş</h4>
                        <p class="text-muted mb-4">
                            İzmir Time Machine uygulaması için ilk bölgenizi ekleyin.<br>
                            Her bölge farklı dillerde içerik yönetimine sahip olacak.
                        </p>
                        <a href="{{ route('admin.regions.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle me-1"></i>
                            İlk Bölgeyi Ekle
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
                <h5 class="modal-title" id="deleteModalLabel">Bölgeyi Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="mdi mdi-alert-circle text-warning" style="font-size: 48px;"></i>
                    <h4 class="mt-3">Emin misiniz?</h4>
                    <p class="text-muted">
                        <strong id="regionName"></strong> bölgesini silmek üzeresiniz. 
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
    function confirmDelete(regionId, regionName) {
        document.getElementById('regionName').textContent = regionName;
        document.getElementById('deleteForm').action = '{{ url("admin/regions") }}/' + regionId;
        
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