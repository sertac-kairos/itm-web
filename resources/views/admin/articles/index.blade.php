@extends('layouts.vertical', ['title' => 'Yazılar Yönetimi - İzmir Time Machine', 'topbarTitle' => 'Yazılar'])

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Yazılar</li>
                    </ol>
                </div>
                <h4 class="page-title">Yazılar Yönetimi</h4>
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
                                <i class="mdi mdi-file-document-edit text-info me-2" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h5 class="mb-0">Yazılar</h5>
                                    <p class="text-muted mb-0">Toplam {{ $articles->total() }} yazı</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.articles.create') }}" class="btn btn-info">
                                <i class="mdi mdi-plus-circle me-1"></i>
                                Yeni Yazı Ekle
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
                    <form method="GET" action="{{ route('admin.articles.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Arama</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Başlık veya içerik ara...">
                        </div>
                        <div class="col-md-3">
                            <label for="author" class="form-label">Yazar</label>
                            <input type="text" class="form-control" id="author" name="author" 
                                   value="{{ request('author') }}" placeholder="Yazar ara...">
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

    <!-- Articles Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Ana Resim</th>
                                    <th>Başlık & İçerik</th>
                                    <th>Yazar</th>
                                    <th>Resim Sayısı</th>
                                    <th>Durum</th>
                                    <th>Sıra</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($articles as $article)
                                    <tr>
                                        <td>
                                            <span class="badge badge-soft-secondary">#{{ $article->id }}</span>
                                        </td>
                                        <td>
                                            @if($article->main_image)
                                                <img src="{{ $article->main_image }}" alt="Ana Resim" 
                                                     class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="mdi mdi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <h5 class="text-dark mb-1">
                                                    {{ $article->translate(app()->getLocale())->title ?? 'Başlık yok' }}
                                                </h5>
                                                <p class="text-muted mb-0">
                                                    {{ Str::limit(strip_tags($article->translate(app()->getLocale())->content ?? 'İçerik yok'), 100) }}
                                                </p>
                                                <div class="mt-1">
                                                    @foreach($article->translations as $translation)
                                                        <span class="badge badge-soft-primary me-1">{{ strtoupper($translation->locale) }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-medium">{{ $article->author }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-info">{{ $article->images_count }}</span>
                                        </td>
                                        <td>
                                            @if($article->is_active)
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
                                            <span class="badge badge-soft-secondary">{{ $article->sort_order }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted" title="{{ $article->created_at->format('d.m.Y H:i') }}">
                                                {{ $article->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-soft-primary btn-sm" title="Görüntüle">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-soft-success btn-sm" title="Düzenle">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-soft-danger btn-sm" title="Sil" 
                                                        onclick="confirmDelete(this)"
                                                        data-id="{{ $article->id }}"
                                                        data-title="{{ $article->translate(app()->getLocale())->title ?? 'Yazı' }}">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-file-document-outline" style="font-size: 3rem;"></i>
                                                <h5 class="mt-2">Henüz yazı eklenmemiş</h5>
                                                <p>İlk yazıyı eklemek için "Yeni Yazı Ekle" butonuna tıklayın.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($articles->hasPages())
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="text-muted mb-0">
                                            {{ $articles->total() }} kayıttan {{ $articles->firstItem() }}-{{ $articles->lastItem() }} arası gösteriliyor
                                        </p>
                                    </div>
                                    <div>
                                        {{ $articles->links() }}
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

@push('scripts')
<script>
    // CSRF token'ı JavaScript değişkenine al
    const csrfToken = '{{ csrf_token() }}';
    
    function confirmDelete(button) {
        // Data attribute'larından değerleri al
        const id = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');
        
        if (confirm(`"${title}" başlıklı yazıyı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/articles/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
