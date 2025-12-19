@extends('layouts.vertical', ['title' => 'Onboarding Yönetimi', 'topbarTitle' => 'Onboarding'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Onboarding Slaytları</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Onboarding</li>
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
                    <h4 class="card-title mb-0">Slayt Listesi</h4>
                    <a href="{{ route('admin.onboarding-slides.create') }}" class="btn btn-info">
                        <i class="mdi mdi-plus me-1"></i> Yeni Slayt Ekle
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.onboarding-slides.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Arama</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Başlık ara...">
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
                                <a href="{{ route('admin.onboarding-slides.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-refresh"></i> Temizle
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Görsel</th>
                                    <th>Başlık</th>
                                    <th>Sıra</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($slides as $slide)
                                    <tr>
                                        <td>
                                            @if($slide->image)
                                                <img src="{{ asset('storage/' . $slide->image) }}" class="rounded" style="width:56px;height:100px;object-fit:cover;">
                                            @else
                                                <div class="bg-light rounded" style="width:56px;height:100px;"></div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $slide->title ?: 'Başlık yok' }}</strong>
                                            @if($slide->description)
                                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($slide->description, 60) }}</div>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $slide->sort_order }}</span></td>
                                        <td>
                                            @if($slide->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Pasif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.onboarding-slides.show', $slide) }}" class="btn btn-soft-info btn-sm"><i class="mdi mdi-eye"></i></a>
                                                <a href="{{ route('admin.onboarding-slides.edit', $slide) }}" class="btn btn-soft-warning btn-sm"><i class="mdi mdi-pencil"></i></a>
                                                <button type="button" class="btn btn-soft-danger btn-sm" onclick="confirmDelete('{{ $slide->id }}', '{{ $slide->title ?: 'Slayt' }}')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Kayıt bulunamadı</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>Toplam {{ $slides->total() }} kayıt</div>
                        <div>{{ $slides->links() }}</div>
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
                        <h5 class="modal-title" id="deleteModalLabel">Slaytı Sil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="mdi mdi-alert-circle text-warning" style="font-size: 48px;"></i>
                            <h4 class="mt-3">Emin misiniz?</h4>
                            <p class="text-muted">
                                <strong id="slideName"></strong> slaytını silmek üzeresiniz. Bu işlem geri alınamaz.
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
  function confirmDelete(slideId, name) {
    document.getElementById('slideName').textContent = name;
    document.getElementById('deleteForm').action = '{{ url("admin/onboarding-slides") }}' + '/' + slideId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
  }
</script>
@endsection



