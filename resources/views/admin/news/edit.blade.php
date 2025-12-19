@extends('layouts.vertical', ['title' => 'Haber/Etkinlik Düzenle - İzmir Time Machine', 'topbarTitle' => 'Haber/Etkinlik Düzenle'])

@section('css')
@vite(['node_modules/quill/dist/quill.core.css', 'node_modules/quill/dist/quill.snow.css', 'node_modules/quill/dist/quill.bubble.css'])
@endsection

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Haberler ve Etkinlikler</a></li>
                        <li class="breadcrumb-item active">Haber/Etkinlik Düzenle</li>
                    </ol>
                </div>
                <h4 class="page-title">Haber/Etkinlik Düzenle: {{ $news->translate(app()->getLocale())->title ?? 'Başlıksız' }}</h4>
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

    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
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
                        <h4 class="header-title">Haber/Etkinlik Bilgileri</h4>
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
                                                       value="{{ old("{$locale}.title", $news->translate($locale, false)->title ?? '') }}" 
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
                                                <div id="content_editor_{{ $locale }}" style="height: 300px;" class="@error("{$locale}.content") is-invalid @enderror"></div>
                                                <textarea class="form-control d-none" 
                                                          id="content_{{ $locale }}" name="{{ $locale }}[content]" 
                                                          {{ $locale === config('translatable.fallback_locale') ? 'required' : '' }}>{{ old("{$locale}.content", $news->translate($locale, false)->content ?? '') }}</textarea>
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
                            <label for="news_date" class="form-label">Tarih *</label>
                            <input type="date" class="form-control @error('news_date') is-invalid @enderror" 
                                   id="news_date" name="news_date" value="{{ old('news_date', $news->news_date->format('Y-m-d')) }}" 
                                   required>
                            @error('news_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sıralama *</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $news->sort_order) }}" 
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
                                       {{ old('is_active', $news->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                Haber/Etkinlik aktif olduğunda kullanıcılar tarafından görülebilir.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Images -->
                @if($news->images->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h4 class="header-title">Mevcut Resimler</h4>
                        </div>
                        <div class="card-body">
                            @foreach($news->images as $image)
                                <div class="mb-3 p-2 border rounded">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $image->image_url }}" alt="{{ $image->alt_text ?? 'Resim' }}" 
                                             style="width: 60px; height: 60px; object-fit: cover;" class="rounded me-2">
                                        <div class="flex-grow-1">
                                            <small class="text-muted d-block">{{ $image->alt_text ?? 'Alt text yok' }}</small>
                                            <small class="text-muted">Sıra: {{ $image->sort_order }}</small>
                                        </div>
                                        <div>
                                            <form action="{{ route('admin.news.delete-image', [$news, $image]) }}" 
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
                            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary me-md-2">
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
    // Make locales available globally for the Quill component
    window.articleLocales = @json($locales);
    console.log('Locales set:', window.articleLocales);
</script>
@vite(['resources/js/components/articles-quilljs.js'])
<script>
    // Fallback: Simple text editor if Quill fails
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Fallback script loaded');
        
        setTimeout(function() {
            const editors = document.querySelectorAll('[id^="content_editor_"]');
            console.log('Found editors for fallback:', editors.length);
            
            editors.forEach(function(editor) {
                if (editor.innerHTML.includes('Rich Text Editör Yükleniyor')) {
                    console.log('Editor still loading, applying fallback');
                    editor.innerHTML = `
                        <div style="border: 1px solid #ddd; padding: 10px; background: white;">
                            <p style="color: #666; margin: 0;">Quill.js yüklenemedi. Basit editör kullanılıyor.</p>
                            <textarea style="width: 100%; height: 200px; border: none; resize: vertical; outline: none;" 
                                      placeholder="İçerik girin..."></textarea>
                        </div>
                    `;
                    
                    // Update hidden textarea when content changes
                    const textarea = editor.querySelector('textarea');
                    const hiddenTextarea = document.getElementById(editor.id.replace('content_editor_', 'content_'));
                    
                    if (textarea && hiddenTextarea) {
                        textarea.addEventListener('input', function() {
                            hiddenTextarea.value = this.value;
                        });
                    }
                }
            });
        }, 3000); // Wait 3 seconds before applying fallback
    });

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
        fetch('/admin/news/translate', {
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
        fetch('/admin/news/translate', {
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
                // Update Quill editor if it exists
                if (window.quillEditors && window.quillEditors.en) {
                    window.quillEditors.en.setContents(data.translated_text);
                }
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
