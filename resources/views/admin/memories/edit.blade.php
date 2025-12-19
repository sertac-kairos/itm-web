@extends('layouts.vertical', ['title' => 'Hafıza Düzenle - İzmir Time Machine', 'topbarTitle' => 'Hafıza Düzenle'])

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.memories.index') }}">Hafıza İzmir</a></li>
                        <li class="breadcrumb-item active">Hafıza Düzenle</li>
                    </ol>
                </div>
                <h4 class="page-title">Hafıza Düzenle: {{ $memory->translate(app()->getLocale())->title ?? 'Başlıksız' }}</h4>
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

    <form action="{{ route('admin.memories.update', $memory) }}" method="POST" enctype="multipart/form-data">
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
                        <h4 class="header-title">Hafıza Bilgileri</h4>
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
                                                       value="{{ old("{$locale}.title", $memory->translate($locale, false)->title ?? '') }}" 
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
                                                          rows="8" 
                                                          placeholder="{{ $locale === 'tr' ? 'Türkçe içerik girin' : 'Enter content in ' . strtoupper($locale) }}"
                                                          {{ $locale === config('translatable.fallback_locale') ? 'required' : '' }}>{{ old("{$locale}.content", $memory->translate($locale, false)->content ?? '') }}</textarea>
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
                            <label for="image" class="form-label">Resim</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Maksimum dosya boyutu: 4MB
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="link" class="form-label">Link</label>
                            <input type="url" class="form-control @error('link') is-invalid @enderror" 
                                   id="link" name="link" value="{{ old('link', $memory->link) }}" 
                                   placeholder="https://example.com">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Opsiyonel. Geçerli bir URL girin.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="author" class="form-label">Yazar</label>
                            <input type="text" class="form-control @error('author') is-invalid @enderror" 
                                   id="author" name="author" value="{{ old('author', $memory->author) }}" 
                                   placeholder="Yazar adı">
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Opsiyonel. İçeriğin yazarı.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sıralama *</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $memory->sort_order) }}" 
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
                                       {{ old('is_active', $memory->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                Hafıza aktif olduğunda kullanıcılar tarafından görülebilir.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Image -->
                @if($memory->image)
                    <div class="card">
                        <div class="card-header">
                            <h4 class="header-title">Mevcut Resim</h4>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ $memory->image_url }}" alt="Mevcut Resim" 
                                 class="img-fluid rounded" style="max-height: 200px;">
                            <p class="text-muted mt-2 mb-0">Mevcut resim</p>
                        </div>
                    </div>
                @endif

                <!-- Image Preview -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Yeni Resim Önizleme</h4>
                    </div>
                    <div class="card-body text-center">
                        <div id="image-preview-container" class="d-none">
                            <img id="image-preview" src="" alt="Önizleme" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                        <div id="image-placeholder" class="text-muted py-4">
                            <i class="mdi mdi-image" style="font-size: 3rem;"></i>
                            <p class="mb-0 mt-2">Resim seçilmedi</p>
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
                            <a href="{{ route('admin.memories.index') }}" class="btn btn-secondary me-md-2">
                                <i class="mdi mdi-arrow-left me-1"></i>
                                İptal
                            </a>
                            <button type="submit" class="btn btn-warning">
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

@push('scripts')
<script>
    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewContainer = document.getElementById('image-preview-container');
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');
        
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.classList.add('d-none');
            placeholder.classList.remove('d-none');
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
        fetch('/admin/memories/translate', {
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
        fetch('/admin/memories/translate', {
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
@endpush
