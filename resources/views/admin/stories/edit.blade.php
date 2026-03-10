@extends('layouts.vertical', ['title' => 'Hikaye Düzenle - İzmir Time Machine', 'topbarTitle' => 'Hikaye Düzenle'])

@section('content')
<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.stories.index') }}">Hikayeler</a></li>
                    <li class="breadcrumb-item active">Hikaye Düzenle</li>
                </ol>
            </div>
            <h4 class="page-title">Hikaye Düzenle</h4>
        </div>
    </div>
</div>

<!-- Form -->
<form action="{{ route('admin.stories.update', $story) }}" method="POST" enctype="multipart/form-data" onsubmit="return saveCanvasData()">
    @csrf
    @method('PUT')
    @if(isset($returnUrl))
        <input type="hidden" name="return_url" value="{{ $returnUrl }}">
    @endif
    
    <div class="row">
        <!-- Ana Bilgiler -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Hikaye Bilgileri</h4>
                    <p class="text-muted mb-0">Hikayanin temel özelliklerini belirleyin</p>
                </div>
                <div class="card-body">
                    <!-- Dil Sekmeleri -->
                    <ul class="nav nav-tabs nav-bordered mb-3" role="tablist">
                        @foreach($locales as $index => $locale)
                            <li class="nav-item" role="presentation">
                                <a href="#{{ $locale }}-tab" data-bs-toggle="tab" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                   class="nav-link {{ $index === 0 ? 'active' : '' }}" role="tab">
                                    <i class="mdi mdi-translate me-1"></i>
                                    {{ strtoupper($locale) }}
                                    @if($locale === 'tr') <span class="text-muted">(Varsayılan)</span> @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Dil İçerikleri -->
                    <div class="tab-content">
                        @foreach($locales as $index => $locale)
                            <div class="tab-pane {{ $index === 0 ? 'show active' : '' }}" id="{{ $locale }}-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="title_{{ $locale }}" class="form-label">
                                                Başlık *
                                                @if($locale === 'tr' && $loop->count > 1)
                                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="translateFromTurkish()">
                                                        <i class="mdi mdi-translate me-1"></i>İngilizceye Çevir
                                                    </button>
                                                @endif
                                            </label>
                                            <input type="text" class="form-control @error("{$locale}.title") is-invalid @enderror" 
                                                   id="title_{{ $locale }}" name="{{ $locale }}[title]" 
                                                   value="{{ old("{$locale}.title", $story->translate($locale, false)?->title) }}" {{ $index === 0 ? 'required' : '' }}>
                                            @error("{$locale}.title")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description_{{ $locale }}" class="form-label">
                                                Açıklama
                                                @if($locale === 'tr' && $loop->count > 1)
                                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="translateContentFromTurkish()">
                                                        <i class="mdi mdi-translate me-1"></i>İngilizceye Çevir
                                                    </button>
                                                @endif
                                            </label>
                                            <textarea class="form-control @error("{$locale}.description") is-invalid @enderror" 
                                                      id="description_{{ $locale }}" name="{{ $locale }}[description]" 
                                                      rows="4">{{ old("{$locale}.description", $story->translate($locale, false)?->description) }}</textarea>
                                            @error("{$locale}.description")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Bu dil için düzenlenen görsel</label>
                                            <div class="form-text">Aşağıdaki editörde kaydedildiğinde otomatik eklenecek.</div>
                                            <input type="hidden" id="editedImageData_{{ $locale }}" name="{{ $locale }}[edited_image_data]">
                                            
                                            @php
                                                $translation = $story->translate($locale, false);
                                                $existingImage = $translation && $translation->image ? asset('storage/' . $translation->image) : null;
                                            @endphp
                                            
                                            @if($existingImage)
                                                <div class="mt-2">
                                                    <label class="form-label">Mevcut Görsel:</label>
                                                    <div>
                                                        <img src="{{ $existingImage }}" 
                                                             alt="{{ strtoupper($locale) }} mevcut görsel" 
                                                             class="img-thumbnail" 
                                                             style="max-width: 200px; max-height: 300px; cursor: pointer;"
                                                             onclick="loadExistingImageToCanvas('{{ $locale }}', '{{ $existingImage }}')"
                                                             title="Görseli editöre yüklemek için tıklayın">
                                                    </div>
                                                    <small class="text-muted">Görseli editöre yüklemek için tıklayın</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($errors->has('translations'))
                        <div class="alert alert-danger">
                            <i class="mdi mdi-alert-circle me-1"></i>
                            {{ $errors->first('translations') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mevcut Düzenlenmiş Görseller -->
        @if($story->edited_images)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="header-title">Mevcut Düzenlenmiş Görseller</h4>
                            <p class="text-muted mb-0">Daha önce düzenlenmiş görseller</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($story->edited_images as $locale => $imagePath)
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">{{ strtoupper($locale) }} Dili</h6>
                                                <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ strtoupper($locale) }} düzenlenmiş görsel" 
                                                     class="img-fluid rounded" style="max-height: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Görsel Düzenleyici (Fabric.js) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title mb-0">Görsel Düzenleyici</h4>
                        <p class="text-muted mb-0">Dil seç, arkaplan yükle, metin/görsel ekle ve kaydet.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 align-items-end mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Düzenleme Dili</label>
                                <select class="form-select" id="editingLanguage">
                                    @foreach($locales as $locale)
                                        <option value="{{ $locale }}" @if($locale==='tr') selected @endif>{{ strtoupper($locale) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Arkaplan Görsel Yükle</label>
                                <input type="file" class="form-control" id="currentLanguageImage" accept="image/*">
                            </div>
                            <div class="col-md-4 text-md-end">
                                <button type="button" class="btn btn-success" id="saveToLanguage"><i class="mdi mdi-content-save me-1"></i> Bu Dile Kaydet</button>
                            </div>
                        </div>

                        <div class="image-editor-toolbar mb-3">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="selectTool"><i class="mdi mdi-cursor-pointer"></i> Seç</button>
                                <button type="button" class="btn btn-outline-info" id="textTool"><i class="mdi mdi-format-text"></i> Metin</button>
                                <button type="button" class="btn btn-outline-info" id="imageTool"><i class="mdi mdi-image-plus"></i> Görsel</button>
                                <button type="button" class="btn btn-outline-danger" id="clearBtn"><i class="mdi mdi-delete-sweep"></i> Tümü Sil</button>
                                <button type="button" class="btn btn-outline-danger" id="deleteObject" style="display:none"><i class="mdi mdi-delete"></i> Nesneyi Sil</button>
                            </div>
                        </div>

                        <div class="row g-3 mb-3" id="textProperties" style="display:none;">
                            <div class="col-md-3">
                                <label class="form-label">Font</label>
                                <select class="form-select" id="textFont">
                                    <option value="Labrada">Labrada</option>
                                    <option value="Montserrat">Montserrat</option>
                                    <option value="Arial">Arial</option>
                                    <option value="Helvetica">Helvetica</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Verdana">Verdana</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Boyut</label>
                                <select class="form-select" id="textSize">
                                    <option value="16">16</option>
                                    <option value="20">20</option>
                                    <option value="24" selected>24</option>
                                    <option value="32">32</option>
                                    <option value="48">48</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Renk</label>
                                <input type="color" class="form-control form-control-color" id="textColor" value="#000000">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kalın</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="textBold">
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <canvas id="fabricCanvas" width="400" height="600" style="border:2px solid #ddd;border-radius:8px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yan Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Genel Ayarlar</h4>
                    <p class="text-muted mb-0">Hikaye ayarları</p>
                </div>
                <div class="card-body">
                    <!-- Küçük Resim -->
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Küçük Resim</label>
                        <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                               id="thumbnail" name="thumbnail" accept="image/*">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Maksimum 2MB, önerilen boyut: 300x300px</small>
                        
                        @if($story->thumbnail)
                            <div class="mt-2">
                                <label class="form-label">Mevcut Küçük Resim:</label>
                                <div>
                                    <img src="{{ asset('storage/' . $story->thumbnail) }}" alt="Mevcut küçük resim" 
                                         class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sıralama -->
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sıralama</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                               id="sort_order" name="sort_order" value="{{ old('sort_order', $story->sort_order) }}" min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Küçük sayı önce görünür</small>
                    </div>

                    <!-- 3D Model -->
                    <div class="mb-3">
                        <label for="model_3d_id" class="form-label">3D Model</label>
                        <select class="form-select @error('model_3d_id') is-invalid @enderror" 
                                id="model_3d_id" name="model_3d_id">
                            <option value="">3D Model Seçin (Opsiyonel)</option>
                            @foreach($models3d as $model3d)
                                <option value="{{ $model3d->id }}" {{ old('model_3d_id', $story->model_3d_id) == $model3d->id ? 'selected' : '' }}>
                                    {{ $model3d->translate(app()->getLocale())->name ?? $model3d->translate('tr')->name ?? 'Model #' . $model3d->id }}
                                </option>
                            @endforeach
                        </select>
                        @error('model_3d_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Bu hikayeye bağlı 3D model seçin</small>
                    </div>

                    <!-- Durum -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $story->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                        <small class="form-text text-muted">Pasif hikayeler mobil uygulamada görünmez</small>
                    </div>
                </div>
            </div>

            <!-- İşlem Butonları -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i>
                            Hikaye Güncelle
                        </button>
                        <a href="{{ route('admin.stories.index') }}" class="btn btn-light">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            İptal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('styles')
<style>
@font-face {
    font-family: 'Labrada';
    src: url('{{ asset('fonts/Labrada-Regular.ttf') }}') format('truetype');
    font-weight: 400;
    font-style: normal;
}
@font-face {
    font-family: 'Labrada';
    src: url('{{ asset('fonts/Labrada-Bold.ttf') }}') format('truetype');
    font-weight: 700;
    font-style: normal;
}
@font-face {
    font-family: 'Labrada';
    src: url('{{ asset('fonts/Labrada-Italic.ttf') }}') format('truetype');
    font-weight: 400;
    font-style: italic;
}
@font-face {
    font-family: 'Labrada';
    src: url('{{ asset('fonts/Labrada-BoldItalic.ttf') }}') format('truetype');
    font-weight: 700;
    font-style: italic;
}
@font-face {
    font-family: 'Labrada';
    src: url('{{ asset('fonts/Labrada-Light.ttf') }}') format('truetype');
    font-weight: 300;
    font-style: normal;
}
@font-face {
    font-family: 'Labrada';
    src: url('{{ asset('fonts/Labrada-Medium.ttf') }}') format('truetype');
    font-weight: 500;
    font-style: normal;
}
@font-face {
    font-family: 'Labrada';
    src: url('{{ asset('fonts/Labrada-SemiBold.ttf') }}') format('truetype');
    font-weight: 600;
    font-style: normal;
}
@font-face {
    font-family: 'Labrada';
    src: url('{{ asset('fonts/Labrada-ExtraBold.ttf') }}') format('truetype');
    font-weight: 800;
    font-style: normal;
}
@font-face {
    font-family: 'Labrada';
    src: url('{{ asset('fonts/Labrada-Black.ttf') }}') format('truetype');
    font-weight: 900;
    font-style: normal;
}

@font-face {
    font-family: 'Montserrat';
    src: url('{{ asset('fonts/Montserrat-Regular.ttf') }}') format('truetype');
    font-weight: 400;
    font-style: normal;
}
@font-face {
    font-family: 'Montserrat';
    src: url('{{ asset('fonts/Montserrat-Bold.ttf') }}') format('truetype');
    font-weight: 700;
    font-style: normal;
}
@font-face {
    font-family: 'Montserrat';
    src: url('{{ asset('fonts/Montserrat-Italic.ttf') }}') format('truetype');
    font-weight: 400;
    font-style: italic;
}
@font-face {
    font-family: 'Montserrat';
    src: url('{{ asset('fonts/Montserrat-BoldItalic.ttf') }}') format('truetype');
    font-weight: 700;
    font-style: italic;
}
@font-face {
    font-family: 'Montserrat';
    src: url('{{ asset('fonts/Montserrat-Light.ttf') }}') format('truetype');
    font-weight: 300;
    font-style: normal;
}
@font-face {
    font-family: 'Montserrat';
    src: url('{{ asset('fonts/Montserrat-Medium.ttf') }}') format('truetype');
    font-weight: 500;
    font-style: normal;
}
@font-face {
    font-family: 'Montserrat';
    src: url('{{ asset('fonts/Montserrat-SemiBold.ttf') }}') format('truetype');
    font-weight: 600;
    font-style: normal;
}
@font-face {
    font-family: 'Montserrat';
    src: url('{{ asset('fonts/Montserrat-ExtraBold.ttf') }}') format('truetype');
    font-weight: 800;
    font-style: normal;
}
@font-face {
    font-family: 'Montserrat';
    src: url('{{ asset('fonts/Montserrat-Black.ttf') }}') format('truetype');
    font-weight: 900;
    font-style: normal;
}
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<script>
function saveCanvasData() {
    if (typeof saveCurrentLanguageCanvas === 'function') { saveCurrentLanguageCanvas(); }
    const locales = @json($locales);
    let ok = false;
    locales.forEach(function(locale){
        const hidden = document.getElementById('editedImageData_'+locale);
        if (hidden && hidden.value && hidden.value.startsWith('data:image/')) { ok = true; }
    });
    return true; // allow submit even if no canvas, server will validate title
}

document.addEventListener('DOMContentLoaded', function(){
    // Preload custom fonts for Fabric.js
    const customFonts = [
        '{{ asset('fonts/Labrada-Regular.ttf') }}',
        '{{ asset('fonts/Labrada-Bold.ttf') }}',
        '{{ asset('fonts/Montserrat-Regular.ttf') }}',
        '{{ asset('fonts/Montserrat-Bold.ttf') }}'
    ];
    
    customFonts.forEach(fontUrl => {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'font';
        link.type = 'font/ttf';
        link.href = fontUrl;
        link.crossOrigin = 'anonymous';
        document.head.appendChild(link);
    });

    const canvas = new fabric.Canvas('fabricCanvas', { width: 400, height: 600, backgroundColor: '#ffffff', selection: true, preserveObjectStacking: true });
    let backgroundImage = null;
    window.fabricCanvas = canvas;
    window.fabricBackgroundImage = null;
    let currentTool = 'select';
    const tools = ['select','text','image'];
    tools.forEach(function(t){ const b=document.getElementById(t+'Tool'); if(b){ b.addEventListener('click', function(){ selectTool(t); }); }});
    function selectTool(t){ currentTool=t; tools.forEach(function(x){ const b=document.getElementById(x+'Tool'); if(b){ b.classList.remove('active'); } }); const ab=document.getElementById(t+'Tool'); if(ab){ ab.classList.add('active'); }
        document.getElementById('textProperties').style.display = (t==='text') ? 'flex' : 'none'; canvas.selection = (t==='select'); }
    selectTool('select');

    let currentEditingLanguage = 'tr';
    const languageCanvasData = {};
    const languageBackgroundImages = {};
    
    // Load existing edited images if available - after canvas is initialized
    @if($story->edited_images && count($story->edited_images) > 0)
        const existingImages = @json($story->edited_images);
        let loadedImagesCount = 0;
        const totalImages = Object.keys(existingImages).length;
        
        Object.keys(existingImages).forEach(locale => {
            const imageUrl = existingImages[locale];
            // Store the image URL for this language
            languageBackgroundImages[locale] = imageUrl;
            // Load the full canvas data (the edited image) as base64
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                // Convert the image to base64 data URL and store it
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = img.width;
                tempCanvas.height = img.height;
                const tempCtx = tempCanvas.getContext('2d');
                tempCtx.drawImage(img, 0, 0);
                try {
                    const dataURL = tempCanvas.toDataURL('image/png');
                    languageCanvasData[locale] = dataURL;
                    // Also store in hidden input
                    const hidden = document.getElementById('editedImageData_' + locale);
                    if (hidden) {
                        hidden.value = dataURL;
                    }
                } catch(e) {
                    console.error('Error converting image to base64:', e);
                }
                
                loadedImagesCount++;
                // Load the current language canvas after all images are loaded
                if (loadedImagesCount === totalImages) {
                    setTimeout(function() {
                        loadLanguageCanvas();
                    }, 100);
                }
            };
            img.onerror = function() {
                console.error('Error loading image for locale:', locale, imageUrl);
                loadedImagesCount++;
                if (loadedImagesCount === totalImages) {
                    setTimeout(function() {
                        loadLanguageCanvas();
                    }, 100);
                }
            };
            img.src = imageUrl;
        });
    @else
        // No existing images, load empty canvas
        setTimeout(function() {
            loadLanguageCanvas();
        }, 100);
    @endif

    document.getElementById('editingLanguage').addEventListener('change', function(){ saveCurrentLanguageCanvas(); currentEditingLanguage=this.value; loadLanguageCanvas(); });
    document.getElementById('currentLanguageImage').addEventListener('change', function(){ 
        const file=this.files[0]; 
        if(!file) return; 
        const img=new Image(); 
        img.onload=function(){ 
            if(img.height<=img.width){ 
                alert('Görsel dikey olmalıdır.'); 
                return; 
            }
            if(backgroundImage){ 
                canvas.remove(backgroundImage); 
            }
            const fabricImage=new fabric.Image(img); 
            const scale=Math.min(canvas.width/img.width, canvas.height/img.height);
            fabricImage.set({ 
                left:(canvas.width - img.width*scale)/2, 
                top:(canvas.height - img.height*scale)/2, 
                scaleX:scale, 
                scaleY:scale, 
                selectable:false, 
                evented:false 
            });
            backgroundImage=fabricImage; 
            window.fabricBackgroundImage=backgroundImage; 
            // store URL for this language so switching loads it
            languageBackgroundImages[currentEditingLanguage] = img.src;
            canvas.add(backgroundImage); 
            canvas.sendToBack(backgroundImage); 
            canvas.renderAll(); 
        };
        img.src=URL.createObjectURL(file); 
    });

    window.saveCurrentLanguageCanvas = function(){ 
        try { 
            const dataURL = canvas.toDataURL({ format:'png', quality:1, multiplier:2 }); 
            languageCanvasData[currentEditingLanguage]=dataURL; 
            const hidden=document.getElementById('editedImageData_'+currentEditingLanguage); 
            if(hidden){ hidden.value=dataURL; } 
        } catch(e){
            console.error('Error saving canvas:', e);
        } 
    };
    
    function loadLanguageCanvas(){
        canvas.clear();
        canvas.backgroundColor='#ffffff';
        backgroundImage=null; 
        window.fabricBackgroundImage=null;
        
        if(languageCanvasData[currentEditingLanguage]){
            // Load saved canvas data (base64 image - the full edited image)
            const img = new Image(); 
            img.crossOrigin = 'anonymous';
            img.onload = function(){ 
                const fabricImage = new fabric.Image(img); 
                const scale = Math.min(canvas.width/img.width, canvas.height/img.height); 
                fabricImage.set({ 
                    left: (canvas.width - img.width*scale)/2, 
                    top: (canvas.height - img.height*scale)/2, 
                    scaleX: scale, 
                    scaleY: scale, 
                    selectable: false, 
                    evented: false 
                }); 
                backgroundImage = fabricImage; 
                window.fabricBackgroundImage = backgroundImage; 
                canvas.add(backgroundImage); 
                canvas.sendToBack(backgroundImage); 
                canvas.renderAll(); 
            }; 
            img.onerror = function() {
                console.error('Error loading canvas data for locale:', currentEditingLanguage);
            };
            img.src = languageCanvasData[currentEditingLanguage];
        } else if (languageBackgroundImages[currentEditingLanguage]) {
            // fallback to preloaded background image for this language
            fabric.Image.fromURL(languageBackgroundImages[currentEditingLanguage], function(img){
                if (!img) {
                    console.error('Failed to load image from URL:', languageBackgroundImages[currentEditingLanguage]);
                    return;
                }
                const scale=Math.min(canvas.width/img.width, canvas.height/img.height);
                img.set({ 
                    left:(canvas.width - img.width*scale)/2, 
                    top:(canvas.height - img.height*scale)/2, 
                    scaleX:scale, 
                    scaleY:scale, 
                    selectable:false, 
                    evented:false 
                });
                backgroundImage=img; 
                window.fabricBackgroundImage=backgroundImage; 
                canvas.add(backgroundImage); 
                canvas.sendToBack(backgroundImage); 
                canvas.renderAll();
            }, { crossOrigin: 'anonymous' });
        }
        canvas.renderAll();
    }
    
    // Function to load existing image to canvas
    window.loadExistingImageToCanvas = function(locale, imageUrl) {
        // Switch to the correct language if needed
        if (currentEditingLanguage !== locale) {
            saveCurrentLanguageCanvas();
            currentEditingLanguage = locale;
            document.getElementById('editingLanguage').value = locale;
        }
        
        // Load the image
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function() {
            // Clear canvas first
            canvas.clear();
            canvas.backgroundColor = '#ffffff';
            
            if (backgroundImage) {
                canvas.remove(backgroundImage);
            }
            
            const fabricImage = new fabric.Image(img);
            const scale = Math.min(canvas.width/img.width, canvas.height/img.height);
            fabricImage.set({
                left: (canvas.width - img.width*scale)/2,
                top: (canvas.height - img.height*scale)/2,
                scaleX: scale,
                scaleY: scale,
                selectable: false,
                evented: false
            });
            
            backgroundImage = fabricImage;
            window.fabricBackgroundImage = backgroundImage;
            languageBackgroundImages[locale] = imageUrl;
            
            // Convert to base64 and store
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = img.width;
            tempCanvas.height = img.height;
            const tempCtx = tempCanvas.getContext('2d');
            tempCtx.drawImage(img, 0, 0);
            try {
                const dataURL = tempCanvas.toDataURL('image/png');
                languageCanvasData[locale] = dataURL;
                const hidden = document.getElementById('editedImageData_' + locale);
                if (hidden) {
                    hidden.value = dataURL;
                }
            } catch(e) {
                console.error('Error converting image to base64:', e);
            }
            
            canvas.add(backgroundImage);
            canvas.sendToBack(backgroundImage);
            canvas.renderAll();
            
            alert('Görsel editöre yüklendi. Düzenleyebilirsiniz.');
        };
        img.onerror = function() {
            alert('Görsel yüklenirken hata oluştu.');
        };
        img.src = imageUrl;
    };

    document.getElementById('saveToLanguage').addEventListener('click', function(){ window.saveCurrentLanguageCanvas(); alert(currentEditingLanguage.toUpperCase()+' dili kaydedildi'); });
    document.getElementById('textTool').addEventListener('click', function(){ const text=new fabric.IText('Metin', { left:100, top:100, fontFamily:document.getElementById('textFont').value, fontSize:parseInt(document.getElementById('textSize').value), fill:document.getElementById('textColor').value, fontWeight:document.getElementById('textBold').checked?'bold':'normal' }); canvas.add(text); canvas.setActiveObject(text); text.enterEditing(); });
    document.getElementById('imageTool').addEventListener('click', function(){ const input=document.createElement('input'); input.type='file'; input.accept='image/*'; input.onchange=function(e){ const file=e.target.files[0]; if(file){ const reader=new FileReader(); reader.onload=function(ev){ fabric.Image.fromURL(ev.target.result, function(img){ img.scaleToWidth(100); img.set({ left:100, top:100, selectable:true }); canvas.add(img); canvas.setActiveObject(img); canvas.renderAll(); }); }; reader.readAsDataURL(file); } }; input.click(); });
    document.getElementById('textFont').addEventListener('change', function(){ const obj=canvas.getActiveObject(); if(obj && obj.type==='i-text'){ obj.set('fontFamily', this.value); canvas.renderAll(); } });
    document.getElementById('textSize').addEventListener('change', function(){ const obj=canvas.getActiveObject(); if(obj && obj.type==='i-text'){ obj.set('fontSize', parseInt(this.value)); canvas.renderAll(); } });
    document.getElementById('textColor').addEventListener('change', function(){ const obj=canvas.getActiveObject(); if(obj && obj.type==='i-text'){ obj.set('fill', this.value); canvas.renderAll(); } });
    document.getElementById('textBold').addEventListener('change', function(){ const obj=canvas.getActiveObject(); if(obj && obj.type==='i-text'){ obj.set('fontWeight', this.checked?'bold':'normal'); canvas.renderAll(); } });
    canvas.on('selection:created', function(){ document.getElementById('deleteObject').style.display='inline-block'; });
    canvas.on('selection:updated', function(){ document.getElementById('deleteObject').style.display='inline-block'; });
    canvas.on('selection:cleared', function(){ document.getElementById('deleteObject').style.display='none'; });
    document.getElementById('deleteObject').addEventListener('click', function(){ const obj=canvas.getActiveObject(); if(obj && obj!==backgroundImage){ canvas.remove(obj); canvas.renderAll(); this.style.display='none'; } });
    document.getElementById('clearBtn').addEventListener('click', function(){ if(confirm('Tüm nesneleri silinsin mi?')){ const objs=canvas.getObjects(); objs.forEach(function(o){ if(o!==backgroundImage){ canvas.remove(o); } }); canvas.renderAll(); } });

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
        fetch('/admin/stories/translate', {
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
                const englishTab = document.querySelector('a[href="#en-tab"]');
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
        const turkishContent = document.getElementById('description_tr');
        if (!turkishContent || !turkishContent.value.trim()) {
            alert('Lütfen önce Türkçe açıklama girin.');
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
        fetch('/admin/stories/translate', {
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
                document.getElementById('description_en').value = data.translated_text;
                // Switch to English tab
                const englishTab = document.querySelector('a[href="#en-tab"]');
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
});
</script>
@endsection
