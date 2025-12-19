@extends('layouts.vertical', ['title' => '3D Modeller - İzmir Time Machine', 'topbarTitle' => '3D Model Yönetimi'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">3D Modeller</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">3D Modeller</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">3D Model Listesi</h4>
                    <a href="{{ route('admin.models-3d.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus me-1"></i> Yeni 3D Model Ekle
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.models-3d.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Arama</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="3D model adı ara...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ören Yeri</label>
                                <select class="form-select" name="archaeological_site_id">
                                    <option value="">Tüm Ören Yerleri</option>
                                    @foreach($archaeologicalSites as $site)
                                        <option value="{{ $site->id }}" {{ request('archaeological_site_id') == $site->id ? 'selected' : '' }}>
                                            {{ $site->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Durum</label>
                                <select class="form-select" name="status">
                                    <option value="">Tümü</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Pasif</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="mdi mdi-filter-variant"></i> Filtrele
                                </button>
                                <a href="{{ route('admin.models-3d.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-refresh"></i> Temizle
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Models Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Önizleme</th>
                                    <th>Ad</th>
                                    <th>Ören Yeri</th>
                                    <th>Sıra</th>
                                    <th>Durum</th>
                                    <th>Oluşturulma</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($models3d as $model)
                                    <tr>
                                        <td>
                                            @if($model->sketchfab_thumbnail_url)
                                                <img src="{{ $model->sketchfab_thumbnail_url }}" 
                                                     alt="{{ $model->name }}" 
                                                     class="rounded"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="mdi mdi-cube-outline text-muted" style="font-size: 24px;"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-1">{{ $model->name ?: 'İsimsiz Model' }}</h6>
                                                @if($model->description)
                                                    <small class="text-muted">{{ Str::limit($model->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($model->archaeologicalSite)
                                                <span class="badge bg-info">{{ $model->archaeologicalSite->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $model->sort_order }}</span>
                                        </td>
                                        <td>
                                            @if($model->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Pasif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $model->created_at->format('d.m.Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.models-3d.show', $model) }}" 
                                                   class="btn btn-info btn-sm" title="Görüntüle">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.models-3d.edit', $model) }}" 
                                                   class="btn btn-warning btn-sm" title="Düzenle">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" title="Sil"
                                                        onclick="confirmDelete('{{ $model->id }}', '{{ $model->name ?: 'İsimsiz Model' }}')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="mdi mdi-cube-outline text-muted mb-2" style="font-size: 48px;"></i>
                                                <h6 class="text-muted">Henüz 3D model eklenmemiş</h6>
                                                <a href="{{ route('admin.models-3d.create') }}" class="btn btn-primary mt-2">
                                                    <i class="mdi mdi-plus me-1"></i> İlk 3D Modeli Ekle
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            Toplam {{ $models3d->total() }} kayıt bulundu
                        </div>
                        <div>
                            {{ $models3d->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                    <h5 class="modal-title" id="deleteModalLabel">3D Modeli Sil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="mdi mdi-alert-circle text-warning" style="font-size: 48px;"></i>
                        <h4 class="mt-3">Emin misiniz?</h4>
                        <p class="text-muted">
                            <strong id="modelName"></strong> 3D modelini silmek üzeresiniz.
                            Bu işlem geri alınamaz.
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
</div>
@endsection

@section('scripts')
<script>
  function confirmDelete(modelId, name) {
    document.getElementById('modelName').textContent = name;
    document.getElementById('deleteForm').action = '{{ url("admin/models-3d") }}' + '/' + modelId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
  }
</script>
@endsection
