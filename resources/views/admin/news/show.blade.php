@extends('layouts.vertical', ['title' => 'Haber/Etkinlik Detayı - İzmir Time Machine', 'topbarTitle' => 'Haber/Etkinlik Detayı'])

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Haberler ve Etkinlikler</a></li>
                        <li class="breadcrumb-item active">Haber/Etkinlik Detayı</li>
                    </ol>
                </div>
                <h4 class="page-title">Haber/Etkinlik Detayı: {{ $news->translate(app()->getLocale())->title ?? 'Başlıksız' }}</h4>
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
                            <td><span class="badge badge-soft-secondary">#{{ $news->id }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Tarih:</td>
                            <td>{{ $news->news_date->format('d.m.Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Sıralama:</td>
                            <td>{{ $news->sort_order }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Durum:</td>
                            <td>
                                @if($news->is_active)
                                    <span class="badge badge bg-success text-white">
                                        <i class="mdi mdi-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge badge bg-danger text-white">
                                        <i class="mdi mdi-close-circle me-1"></i>Pasif
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Oluşturulma:</td>
                            <td>{{ $news->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Güncellenme:</td>
                            <td>{{ $news->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Resim Sayısı:</td>
                            <td><span class="badge badge-soft-info">{{ $news->images_count }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Main Image -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Ana Resim</h4>
                </div>
                <div class="card-body text-center">
                    @if($news->main_image)
                        <img src="{{ $news->main_image }}" alt="Ana Resim" 
                             class="img-fluid rounded" style="max-height: 200px;">
                    @else
                        <div class="text-muted py-4">
                            <i class="mdi mdi-image" style="font-size: 3rem;"></i>
                            <p class="mb-0 mt-2">Ana resim yok</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.articles.edit', $news) }}" class="btn btn-info">
                            <i class="mdi mdi-pencil me-1"></i>
                            Düzenle
                        </a>
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Geri Dön
                        </a>
                        <button type="button" class="btn btn-danger" 
                                onclick="confirmDelete(this)"
                                data-id="{{ $news->id }}"
                                data-title="{{ $news->translate(app()->getLocale())->title ?? 'Yazı' }}">
                            <i class="mdi mdi-delete me-1"></i>
                            Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Multilingual Content -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Çok Dilli İçerik</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-bordered">
                        @foreach($news->translations as $translation)
                            <li class="nav-item">
                                <a href="#{{ $translation->locale }}" data-bs-toggle="tab" 
                                   aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                   class="nav-link {{ $loop->first ? 'active' : '' }}">
                                    <span class="d-none d-sm-block">{{ strtoupper($translation->locale) }}</span>
                                    <span class="d-block d-sm-none">{{ strtoupper($translation->locale) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content">
                        @foreach($news->translations as $translation)
                            <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $translation->locale }}">
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Başlık</label>
                                            <div class="form-control-plaintext">
                                                {{ $translation->title ?? 'Başlık yok' }}
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-medium">İçerik</label>
                                            <div class="border rounded p-3" style="min-height: 200px;">
                                                @if($translation->content)
                                                    {!! $translation->content !!}
                                                @else
                                                    <span class="text-muted">İçerik yok</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Images Gallery -->
            @if($news->images->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Resim Galerisi</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($news->images as $image)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <img src="{{ $image->image_url }}" alt="{{ $image->alt_text ?? 'Resim' }}" 
                                             class="card-img-top" style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <h6 class="card-title">Resim #{{ $image->id }}</h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    Alt Text: {{ $image->alt_text ?? 'Yok' }}<br>
                                                    Sıra: {{ $image->sort_order }}
                                                </small>
                                            </p>
                                            <form action="{{ route('admin.articles.delete-image', [$news, $image]) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Bu resmi silmek istediğinizden emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="mdi mdi-delete me-1"></i>
                                                    Resmi Sil
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-4">
                        <div class="text-muted">
                            <i class="mdi mdi-image-off" style="font-size: 3rem;"></i>
                            <h5 class="mt-2">Henüz resim eklenmemiş</h5>
                            <p>Bu habere/etkinliğe resim eklemek için düzenleme sayfasını kullanın.</p>
                        </div>
                    </div>
                </div>
            @endif
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
        
        if (confirm(`"${title}" başlıklı haberi/etkinliği silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/news/${id}`;
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
