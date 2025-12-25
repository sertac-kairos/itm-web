@extends('layouts.vertical', ['title' => 'Haberler ve Etkinlikler Yönetimi - İzmir Time Machine', 'topbarTitle' => 'Haberler ve Etkinlikler'])

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Haberler ve Etkinlikler</li>
                    </ol>
                </div>
                <h4 class="page-title">Haberler ve Etkinlikler Yönetimi</h4>
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
                                <i class="mdi mdi-newspaper text-info me-2" style="font-size: 1.5rem;"></i>
                                <div>
                                        <h5 class="mb-0">Haberler ve Etkinlikler</h5>
                                        <p class="text-muted mb-0">Toplam {{ $news->total() }} haber/etkinlik</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.news.create') }}" class="btn btn-info">
                                <i class="mdi mdi-plus-circle me-1"></i>
                                    Yeni Haber/Etkinlik Ekle
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
                    <form method="GET" action="{{ route('admin.news.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Arama</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Başlık veya içerik ara...">
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Tarih Başlangıç</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Tarih Bitiş</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Durum</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tümü</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Pasif</option>
                            </select>
                        </div>
                        <div class="col-md-1">
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

    <!-- News Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <a href="{{ route('admin.news.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            ID
                                            @if(request('sort') === 'id')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Ana Resim</th>
                                    <th>
                                        <a href="{{ route('admin.news.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'title', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Başlık & İçerik
                                            @if(request('sort') === 'title')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.news.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'news_date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Tarih
                                            @if(request('sort') === 'news_date')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Resim Sayısı</th>
                                    <th>
                                        <a href="{{ route('admin.news.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'is_active', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Durum
                                            @if(request('sort') === 'is_active')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.news.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'sort_order', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Sıra
                                            @if(request('sort') === 'sort_order' || !request('sort'))
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.news.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Oluşturulma
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
                                @forelse($news as $newsItem)
                                    <tr>
                                        <td>
                                            <span class="badge badge-soft-secondary">#{{ $newsItem->id }}</span>
                                        </td>
                                        <td>
                                            @if($newsItem->main_image)
                                                <img src="{{ $newsItem->main_image }}" alt="Ana Resim" 
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
                                                    {{ $newsItem->translate(app()->getLocale())->title ?? 'Başlık yok' }}
                                                </h5>
                                                <p class="text-muted mb-0">
                                                    {{ Str::limit(strip_tags($newsItem->translate(app()->getLocale())->content ?? 'İçerik yok'), 100) }}
                                                </p>
                                                <div class="mt-1">
                                                    @foreach($newsItem->translations as $translation)
                                                        <span class="badge badge-soft-primary me-1">{{ strtoupper($translation->locale) }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-medium">{{ $newsItem->news_date->format('d.m.Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-info">{{ $newsItem->images_count }}</span>
                                        </td>
                                        <td>
                                            @if($newsItem->is_active)
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
                                            <span class="badge badge-soft-secondary">{{ $newsItem->sort_order }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted" title="{{ $newsItem->created_at->format('d.m.Y H:i') }}">
                                                {{ $newsItem->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.news.show', $newsItem) }}" class="btn btn-soft-primary btn-sm" title="Görüntüle">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.news.edit', $newsItem) }}" class="btn btn-soft-success btn-sm" title="Düzenle">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-soft-danger btn-sm" title="Sil" 
                                                        onclick="confirmDelete(this)"
                                                        data-id="{{ $newsItem->id }}"
                                                        data-title="{{ $newsItem->translate(app()->getLocale())->title ?? 'Haber' }}">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-newspaper-outline" style="font-size: 3rem;"></i>
                                                    <h5 class="mt-2">Henüz haber/etkinlik eklenmemiş</h5>
                                                    <p>İlk haberi/etkinliği eklemek için "Yeni Haber/Etkinlik Ekle" butonuna tıklayın.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($news->hasPages())
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="text-muted mb-0">
                                            {{ $news->total() }} kayıttan {{ $news->firstItem() }}-{{ $news->lastItem() }} arası gösteriliyor
                                        </p>
                                    </div>
                                    <div>
                                        {{ $news->links() }}
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
    function confirmDelete(button) {
        const id = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');
        
        if (confirm(`"${title}" başlıklı haberi/etkinliği silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/news/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush