@extends('layouts.vertical', ['title' => 'Yazı Düzenle - İzmir Time Machine', 'topbarTitle' => 'Yazı Düzenle'])

@section('css')
@endsection

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">Yazılar</a></li>
                        <li class="breadcrumb-item active">Yazı Düzenle</li>
                    </ol>
                </div>
                <h4 class="page-title">Yazı Düzenle: {{ $article->translate(app()->getLocale())->title ?? 'Başlıksız' }}</h4>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            <strong>Hata!</strong> Lütfen aşağıdaki hataları düzeltin:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @if(isset($returnUrl))
            <input type="hidden" name="return_url" value="{{ $returnUrl }}">
        @endif
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Yazı Bilgileri</h4>
                    </div>
                    <div class="card-body">
                        <!-- Language Tabs -->
                        <ul class="nav nav-tabs nav-bordered">
                            @foreach($locales as $locale)
                                <li class="nav-item">
                                    <a href="#{{ $locale }}" data-bs-toggle="tab" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" class="nav-link {{ $loop->first ? 'active' : '' }}">
                                        <span class="d-none d-sm-block">{{ strtoupper($locale) }}</span>
                                        <span class="d-block d-sm-none">{{ strtoupper($locale) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach($locales as $locale)
                                <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $locale }}">
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="title_{{ $locale }}" class="form-label">
                                                    Başlık {{ $locale === config('translatable.fallback_locale') ? '*' : '' }}
                                                    @if($locale === 'tr' && $loop->count > 1)
                                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="translateFromTurkish()">
                                                            <i class="mdi mdi-translate me-1"></i>İngilizceye Çevir
                                                        </button>
                                                    @endif
                                                </label>
                                                <input type="text" class="form-control @error("{$locale}.title") is-invalid @enderror" 
                                                       id="title_{{ $locale }}" name="{{ $locale }}[title]" 
                                                       value="{{ old("{$locale}.title", $article->translate($locale, false)->title ?? '') }}" 
                                                       placeholder="{{ $locale === 'tr' ? 'Türkçe başlık girin' : 'Enter title in ' . strtoupper($locale) }}"
                                                       {{ $locale === config('translatable.fallback_locale') ? 'required' : '' }}>
                                                @error("{$locale}.title")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="content_{{ $locale }}" class="form-label">
                                                    İçerik {{ $locale === config('translatable.fallback_locale') ? '*' : '' }}
                                                    @if($locale === 'tr' && $loop->count > 1)
                                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="translateContentFromTurkish()">
                                                            <i class="mdi mdi-translate me-1"></i>İngilizceye Çevir
                                                        </button>
                                                    @endif
                                                </label>
                                                <textarea class="form-control @error("{$locale}.content") is-invalid @enderror" 
                                                          id="content_{{ $locale }}" name="{{ $locale }}[content]" 
                                                          rows="15"
                                                          placeholder="{{ $locale === 'tr' ? 'İçerik girin...' : 'Enter content in ' . strtoupper($locale) . '...' }}"
                                                          {{ $locale === config('translatable.fallback_locale') ? 'required' : '' }}>{{ old("{$locale}.content", $article->translate($locale, false)->content ?? '') }}</textarea>
                                                @error("{$locale}.content")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text text-muted">
                                                    En az bir dilde başlık ve içerik girilmelidir.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- General Settings -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Genel Ayarlar</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="author" class="form-label">Yazar *</label>
                            <input type="text" class="form-control @error('author') is-invalid @enderror" 
                                   id="author" name="author" value="{{ old('author', $article->author) }}" 
                                   placeholder="Yazar adını girin" required>
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sıralama *</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $article->sort_order) }}" 
                                   min="0" required>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Düşük sayılar önce gösterilir.
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $article->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                Yazı aktif olduğunda kullanıcılar tarafından görülebilir.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Images -->
                @if($article->images->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h4 class="header-title">Mevcut Resimler</h4>
                        </div>
                        <div class="card-body">
                            @foreach($article->images as $image)
                                <div class="mb-3 p-2 border rounded">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $image->image_url }}" alt="{{ $image->alt_text ?? 'Resim' }}" 
                                             style="width: 60px; height: 60px; object-fit: cover;" class="rounded me-2">
                                        <div class="flex-grow-1">
                                            <small class="text-muted d-block">{{ $image->alt_text ?? 'Alt text yok' }}</small>
                                            <small class="text-muted">Sıra: {{ $image->sort_order }}</small>
                                        </div>
                                        <div>
                                            <form action="{{ route('admin.articles.delete-image', [$article, $image]) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Bu resmi silmek istediğinizden emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Resmi Sil">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add New Images -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Yeni Resim Ekle</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="images" class="form-label">Resimler</label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                   id="images" name="images[]" multiple accept="image/*">
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Birden fazla resim seçebilirsiniz. Maksimum dosya boyutu: 4MB
                            </div>
                        </div>

                        <div id="image-preview-container" class="d-none">
                            <h6 class="mb-2">Seçilen Resimler:</h6>
                            <div id="image-preview-list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary me-md-2">
                                <i class="mdi mdi-arrow-left me-1"></i>
                                İptal
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="mdi mdi-content-save me-1"></i>
                                Değişiklikleri Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>

    // Image preview functionality
    document.getElementById('images').addEventListener('change', function(e) {
        const container = document.getElementById('image-preview-container');
        const list = document.getElementById('image-preview-list');
        
        if (e.target.files.length > 0) {
            container.classList.remove('d-none');
            list.innerHTML = '';
            
            Array.from(e.target.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'mb-2 p-2 border rounded';
                    div.innerHTML = `
                        <div class="d-flex align-items-center">
                            <img src="${e.target.result}" alt="Preview" style="width: 50px; height: 50px; object-fit: cover;" class="rounded me-2">
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">${file.name}</small>
                                <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                            </div>
                        </div>
                        <div class="mt-2">
                            <input type="text" class="form-control form-control-sm" 
                                   name="image_alt_texts[${index}]" 
                                   placeholder="Alt text (opsiyonel)">
                        </div>
                    `;
                    list.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        } else {
            container.classList.add('d-none');
        }
    });

    // Translation functions
    function translateFromTurkish() {
        const turkishTitle = document.getElementById('title_tr');
        if (!turkishTitle || !turkishTitle.value.trim()) {
            alert('Lütfen önce Türkçe başlık girin.');
            return;
        }

        // Show loading
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Çevriliyor...';
        button.disabled = true;

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('CSRF token bulunamadı. Sayfayı yenileyin.');
            button.innerHTML = originalText;
            button.disabled = false;
            return;
        }

        // Make AJAX request to translate
        fetch('/admin/articles/translate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify({
                text: turkishTitle.value,
                from: 'tr',
                to: 'en'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.translated_text) {
                document.getElementById('title_en').value = data.translated_text;
                // Switch to English tab
                const englishTab = document.querySelector('a[href="#en"]');
                if (englishTab) {
                    englishTab.click();
                }
            } else {
                alert('Çeviri başarısız: ' + (data.message || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Translation error:', error);
            alert('Çeviri sırasında hata oluştu.');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }

    function translateContentFromTurkish() {
        const turkishContent = document.getElementById('content_tr');
        if (!turkishContent || !turkishContent.value.trim()) {
            alert('Lütfen önce Türkçe içerik girin.');
            return;
        }

        // Show loading
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Çevriliyor...';
        button.disabled = true;

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('CSRF token bulunamadı. Sayfayı yenileyin.');
            button.innerHTML = originalText;
            button.disabled = false;
            return;
        }

        // Make AJAX request to translate
        fetch('/admin/articles/translate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify({
                text: turkishContent.value,
                from: 'tr',
                to: 'en'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.translated_text) {
                document.getElementById('content_en').value = data.translated_text;
                // Switch to English tab
                const englishTab = document.querySelector('a[href="#en"]');
                if (englishTab) {
                    englishTab.click();
                }
            } else {
                alert('Çeviri başarısız: ' + (data.message || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Translation error:', error);
            alert('Çeviri sırasında hata oluştu.');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
</script>
@endsection
