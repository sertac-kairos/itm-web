@extends('layouts.vertical', ['title' => 'Hafıza İzmir Yönetimi - İzmir Time Machine', 'topbarTitle' => 'Hafıza İzmir'])

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Hafıza İzmir</li>
                    </ol>
                </div>
                <h4 class="page-title">Hafıza İzmir Yönetimi</h4>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Actions Bar -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-brain text-warning me-2" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h5 class="mb-0">Hafıza İzmir</h5>
                                    <p class="text-muted mb-0">Toplam {{ $memories->total() }} hafıza</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.memories.create') }}" class="btn btn-warning">
                                <i class="mdi mdi-plus-circle me-1"></i>
                                Yeni İçerik Ekle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.memories.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Arama</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Başlık veya içerik ara...">
                        </div>
                        <div class="col-md-3">
                            <label for="has_link" class="form-label">Link Durumu</label>
                            <select class="form-select" id="has_link" name="has_link">
                                <option value="">Tümü</option>
                                <option value="yes" {{ request('has_link') === 'yes' ? 'selected' : '' }}>Link Var</option>
                                <option value="no" {{ request('has_link') === 'no' ? 'selected' : '' }}>Link Yok</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Durum</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tümü</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Pasif</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-magnify me-1"></i>
                                    Ara
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Memories Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <a href="{{ route('admin.memories.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            ID
                                            @if(request('sort') === 'id')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Resim</th>
                                    <th>
                                        <a href="{{ route('admin.memories.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'title', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Başlık & İçerik
                                            @if(request('sort') === 'title')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Link</th>
                                    <th>
                                        <a href="{{ route('admin.memories.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'is_active', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Durum
                                            @if(request('sort') === 'is_active')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.memories.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'sort_order', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Sıra
                                            @if(request('sort') === 'sort_order' || !request('sort'))
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.memories.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Tarih
                                            @if(request('sort') === 'created_at')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($memories as $memory)
                                    <tr>
                                        <td>
                                            <span class="badge badge-soft-secondary">#{{ $memory->id }}</span>
                                        </td>
                                        <td>
                                            @if($memory->image)
                                                <img src="{{ $memory->image_url }}" alt="Resim" 
                                                     class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="mdi mdi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <h5 class="text-dark mb-1">
                                                    {{ $memory->translate(app()->getLocale())->title ?? 'Başlık yok' }}
                                                </h5>
                                                <p class="text-muted mb-0">
                                                    {{ Str::limit($memory->translate(app()->getLocale())->content ?? 'İçerik yok', 100) }}
                                                </p>
                                                <div class="mt-1">
                                                    @foreach($memory->translations as $translation)
                                                        <span class="badge badge-soft-primary me-1">{{ strtoupper($translation->locale) }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($memory->hasLink())
                                                <a href="{{ $memory->formatted_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="mdi mdi-link me-1"></i>
                                                    Link
                                                </a>
                                            @else
                                                <span class="text-muted">Link yok</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($memory->is_active)
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
                                            <span class="badge badge-soft-secondary">{{ $memory->sort_order }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted" title="{{ $memory->created_at->format('d.m.Y H:i') }}">
                                                {{ $memory->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.memories.show', $memory) }}" class="btn btn-soft-primary btn-sm" title="Görüntüle">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.memories.edit', $memory) }}" class="btn btn-soft-success btn-sm" title="Düzenle">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-soft-danger btn-sm" title="Sil" 
                                                        onclick="confirmDelete(this)"
                                                        data-id="{{ $memory->id }}"
                                                        data-title="{{ $memory->translate(app()->getLocale())->title ?? 'Hafıza' }}">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-brain-outline" style="font-size: 3rem;"></i>
                                                <h5 class="mt-2">Henüz hafıza eklenmemiş</h5>
                                                <p>İlk hafızayı eklemek için "Yeni İçerik Ekle" butonuna tıklayın.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($memories->hasPages())
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="text-muted mb-0">
                                            {{ $memories->total() }} kayıttan {{ $memories->firstItem() }}-{{ $memories->lastItem() }} arası gösteriliyor
                                        </p>
                                    </div>
                                    <div>
                                        {{ $memories->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="mdi mdi-alert-circle-outline text-warning me-2"></i>
                    Silme Onayı
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="mdi mdi-trash-can-outline text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 mb-2">Hafızayı Silmek İstediğinizden Emin misiniz?</h5>
                    <p class="text-muted mb-3" id="deleteItemTitle">Bu işlem geri alınamaz.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>İptal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="mdi mdi-delete me-1"></i>Evet, Sil
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // CSRF token'ı JavaScript değişkenine al
    const csrfToken = '{{ csrf_token() }}';
    let currentDeleteId = null;
    let currentDeleteTitle = null;

    function confirmDelete(button) {
        // Data attribute'larından değerleri al
        currentDeleteId = button.getAttribute('data-id');
        currentDeleteTitle = button.getAttribute('data-title');
        
        // Modal'daki başlığı güncelle
        document.getElementById('deleteItemTitle').innerHTML = 
            `<strong>"${currentDeleteTitle}"</strong> başlıklı hafıza kalıcı olarak silinecek.`;
        
        // Modal'ı göster
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // Silme onayı
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentDeleteId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/memories/${currentDeleteId}`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Modal kapandığında değişkenleri temizle
        document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function() {
            currentDeleteId = null;
            currentDeleteTitle = null;
        });
    });
</script>
@endpush
