@extends('layouts.vertical', ['title' => 'Onboarding Slide Düzenle - İzmir Time Machine', 'topbarTitle' => 'Onboarding Slide Düzenle'])

@section('content')
<div class="container-fluid">
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.onboarding-slides.index') }}">Onboarding Slides</a></li>
                        <li class="breadcrumb-item active">Slide Düzenle</li>
                    </ol>
                </div>
                <h4 class="page-title">Onboarding Slide Düzenle #{{ $onboardingSlide->id }}</h4>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.onboarding-slides.update', $onboardingSlide) }}" method="POST" enctype="multipart/form-data" onsubmit="return saveCanvasData()">
        @csrf
        @method('PUT')
        @if(isset($returnUrl))
            <input type="hidden" name="return_url" value="{{ $returnUrl }}">
        @endif
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h4 class="card-title mb-0">Çoklu Dil İçerik</h4></div>
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            @foreach($locales as $locale)
                                <li class="nav-item">
                                    <a class="nav-link @if($locale==='tr') active @endif" data-bs-toggle="tab" href="#{{ $locale }}" role="tab">
                                        <span class="d-none d-sm-block">{{ strtoupper($locale) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            @foreach($locales as $locale)
                                <div class="tab-pane @if($locale==='tr') active @endif" id="{{ $locale }}" role="tabpanel">
                                    <div class="mb-3">
                                        <label class="form-label" for="{{ $locale }}_title">Başlık @if($locale==='tr')<span class="text-danger">*</span>@endif</label>
                                        <input type="text" class="form-control @error($locale.'.title') is-invalid @enderror" id="{{ $locale }}_title" name="{{ $locale }}[title]" value="{{ old($locale.'.title', $onboardingSlide->translate($locale, false)->title ?? '') }}">
                                        @error($locale.'.title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="{{ $locale }}_description">Açıklama</label>
                                        <textarea class="form-control @error($locale.'.description') is-invalid @enderror" id="{{ $locale }}_description" name="{{ $locale }}[description]" rows="4">{{ old($locale.'.description', $onboardingSlide->translate($locale, false)->description ?? '') }}</textarea>
                                        @error($locale.'.description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    
                                    @php
                                        $translation = $onboardingSlide->translate($locale, false);
                                        $imageUrl = $translation && $translation->image ? $translation->image : null;
                                    @endphp
                                    @if($imageUrl)
                                        <div class="mb-3">
                                            <label class="form-label">Mevcut Görsel ({{ strtoupper($locale) }}):</label>
                                            <img src="{{ asset('storage/' . $imageUrl) }}" alt="Mevcut görsel" class="img-fluid rounded" style="max-height: 100px;">
                                            <div class="form-text">Bu dili editörde seçerek düzenleyebilirsiniz.</div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="alert alert-info"><i class="mdi mdi-information-outline me-1"></i> En az bir dilde başlık yeterli.</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h4 class="card-title mb-0">Medya</h4></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline me-1"></i> 
                                Her dil için ayrı görsel var. Fabric.js düzenleyicisi ile düzenleyebilirsiniz.
                            </div>
                            
                            <!-- Hidden inputs for edited canvas data for each language -->
                            @foreach($locales as $locale)
                                <input type="hidden" id="editedImageData_{{ $locale }}" name="{{ $locale }}[edited_image_data]">
                            @endforeach
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="sort_order">Sıra <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $onboardingSlide->sort_order) }}" min="0" required>
                            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $onboardingSlide->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            Görsel Düzenleyici
                        </h4>
                        <p class="text-muted mb-0">Mevcut görseli düzenleyin veya yeni görsel yükleyin</p>
                    </div>
                    <div class="card-body">
                        <!-- Language Selector -->
                        <div class="language-selector mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Düzenleme Dili Seçin</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Hangi dil için düzenleme yapıyorsunuz?</label>
                                            <select class="form-select" id="editingLanguage">
                                                @foreach($locales as $locale)
                                                    <option value="{{ $locale }}" @if($locale === 'tr') selected @endif>
                                                        {{ strtoupper($locale) }} - {{ $locale === 'tr' ? 'Türkçe' : ($locale === 'en' ? 'English' : 'Deutsch') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Bu dil için görsel yükleyin</label>
                                            <input type="file" class="form-control" id="currentLanguageImage" accept="image/*">
                                            <div class="form-text">Seçili dil için arkaplan görseli yükleyin</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Toolbar -->
                        <div class="image-editor-toolbar mb-3">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="selectTool" title="Seçim Aracı">
                                    <i class="mdi mdi-cursor-pointer"></i> Seç
                                </button>
                                <button type="button" class="btn btn-outline-info" id="textTool" title="Metin Ekle">
                                    <i class="mdi mdi-format-text"></i> Metin
                                </button>
                                <button type="button" class="btn btn-outline-info" id="imageTool" title="Görsel Ekle">
                                    <i class="mdi mdi-image-plus"></i> Görsel
                                </button>
                            </div>
                            
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-danger" id="deleteObject" title="Seçili Nesneyi Sil" style="display: none;">
                                    <i class="mdi mdi-delete"></i> Nesneyi Sil
                                </button>
                                <button type="button" class="btn btn-outline-danger" id="clearBtn" title="Tümü Sil">
                                    <i class="mdi mdi-delete-sweep"></i> Tümü Sil
                                </button>
                                <button type="button" class="btn btn-outline-success" id="saveToLanguage" title="Bu Dile Kaydet">
                                    <i class="mdi mdi-content-save"></i> Bu Dile Kaydet
                                </button>
                            </div>
                        </div>

                        <!-- Text Properties Panel -->
                        <div class="text-properties mb-3" id="textProperties" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Metin Özellikleri</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Font</label>
                                            <select class="form-select" id="textFont">
                                                <option value="Arial">Arial</option>
                                                <option value="Times New Roman">Times New Roman</option>
                                                <option value="Courier New">Courier New</option>
                                                <option value="Georgia">Georgia</option>
                                                <option value="Verdana">Verdana</option>
                                                <option value="Impact">Impact</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Boyut</label>
                                            <input type="range" class="form-range" id="textSize" min="12" max="72" value="24">
                                            <div class="text-center"><span id="textSizeValue">24</span></div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Renk</label>
                                            <input type="color" class="form-control form-control-color" id="textColor" value="#000000">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Kalın</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="textBold">
                                                <label class="form-check-label" for="textBold">Kalın</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Canvas Container -->
                        <div class="canvas-container text-center">
                            <canvas id="fabricCanvas" style="border: 2px dashed #ccc; border-radius: 8px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="text-end">
                    <a href="{{ route('admin.onboarding-slides.index') }}" class="btn btn-secondary me-2">İptal</a>
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.image-editor-toolbar {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
}

.form-range {
    height: 6px;
}

.form-control-color {
    width: 100%;
    height: 38px;
}

#fabricCanvas {
    cursor: crosshair;
}

.btn.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
</style>
@endsection

@section('scripts')
<!-- Fabric.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>

<script>
// Global function for form onsubmit
function saveCanvasData() {
    console.log('saveCanvasData global function çağrıldı!');
    
    // Get canvas and background image from global scope
    const canvas = window.fabricCanvas;
    const backgroundImage = window.fabricBackgroundImage;
    
    if (!canvas) {
        console.log('Canvas bulunamadı!');
        return true; // Let form submit anyway
    }
    
    try {
        // Save current language canvas state first
        if (typeof window.saveCurrentLanguageCanvas === 'function') {
            window.saveCurrentLanguageCanvas();
        }
        
        // Populate all language hidden inputs
        if (typeof window.languageCanvasData !== 'undefined' && window.languageCanvasData) {
            Object.keys(window.languageCanvasData).forEach(locale => {
                const hiddenInput = document.getElementById(`editedImageData_${locale}`);
                if (hiddenInput && window.languageCanvasData[locale]) {
                    hiddenInput.value = window.languageCanvasData[locale];
                    console.log(`${locale} dili için canvas data kaydedildi:`, window.languageCanvasData[locale].length);
                }
            });
        }
        
        console.log('Tüm diller için canvas data kaydedildi! Form gönderiliyor...');
        return true; // Allow form submission
    } catch (error) {
        console.error('Canvas kaydetme hatası:', error);
        return true; // Allow form submission even if there's an error
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Fabric.js canvas
    const canvas = new fabric.Canvas('fabricCanvas', {
        width: 400,
        height: 600,
        backgroundColor: '#ffffff',
        selection: true,
        preserveObjectStacking: true
    });

    let backgroundImage = null;
    
    // Make canvas and backgroundImage globally accessible
    window.fabricCanvas = canvas;
    window.fabricBackgroundImage = null;
    
    // Multi-language canvas state management
    let currentEditingLanguage = 'tr';
    let languageCanvasData = {};
    let languageBackgroundImages = {};
    
    // Make languageCanvasData globally accessible
    window.languageCanvasData = languageCanvasData;
    
    let currentTool = 'select';
    let history = [];
    let historyIndex = -1;

    // Tool selection
    const tools = ['select', 'text', 'image'];
    tools.forEach(tool => {
        const btn = document.getElementById(tool + 'Tool');
        if (btn) {
            btn.addEventListener('click', () => selectTool(tool));
        }
    });

    function selectTool(tool) {
        currentTool = tool;
        // Update button states
        tools.forEach(t => {
            const btn = document.getElementById(t + 'Tool');
            if (btn) {
                btn.classList.remove('active');
            }
        });
        const activeBtn = document.getElementById(tool + 'Tool');
        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        // Show/hide text properties panel
        if (tool === 'text') {
            document.getElementById('textProperties').style.display = 'block';
        } else {
            document.getElementById('textProperties').style.display = 'none';
        }

        // Set canvas cursor
        if (tool === 'select') {
            canvas.defaultCursor = 'default';
            canvas.selection = true;
        } else {
            canvas.defaultCursor = 'crosshair';
            canvas.selection = false;
        }
    }

    // Initialize select tool as active
    selectTool('select');

    // Language selector change handler
    document.getElementById('editingLanguage').addEventListener('change', function() {
        const newLanguage = this.value;
        console.log('Dil değiştirildi:', currentEditingLanguage, '->', newLanguage);
        
        // Save current language canvas state
        saveCurrentLanguageCanvas();
        
        // Switch to new language
        currentEditingLanguage = newLanguage;
        
        // Load new language canvas state
        loadLanguageCanvas();
    });

    // Current language image input handler
    document.getElementById('currentLanguageImage').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        
        const img = new Image();
        img.onload = function() {
            if (img.height <= img.width) {
                alert('Görsel dikey (portre) olmalıdır. Lütfen uygun bir görsel seçin.');
                document.getElementById('currentLanguageImage').value = '';
                return;
            }
            
            // Clear existing background
            if (backgroundImage) {
                canvas.remove(backgroundImage);
            }
            
            // Create Fabric.js image and scale it to fit canvas
            const fabricImage = new fabric.Image(img);
            const scale = Math.min(canvas.width / img.width, canvas.height / img.height);
            
            fabricImage.set({
                left: (canvas.width - img.width * scale) / 2,
                top: (canvas.height - img.height * scale) / 2,
                scaleX: scale,
                scaleY: scale,
                selectable: false,
                evented: false
            });
            
            backgroundImage = fabricImage;
            window.fabricBackgroundImage = backgroundImage; // Update global reference
            
            // Store background image for current language
            languageBackgroundImages[currentEditingLanguage] = fabricImage.toDataURL();
            
            canvas.add(backgroundImage);
            canvas.sendToBack(backgroundImage);
            canvas.renderAll();
            
            saveState();
        };
        img.src = URL.createObjectURL(file);
    });

    // Save to language button handler
    document.getElementById('saveToLanguage').addEventListener('click', function() {
        saveCurrentLanguageCanvas();
        alert(`${currentEditingLanguage.toUpperCase()} dili için canvas durumu kaydedildi!`);
    });

    // Save current language canvas state
    window.saveCurrentLanguageCanvas = function() {
        if (!canvas || !backgroundImage) return;
        
        try {
            const dataURL = canvas.toDataURL({
                format: 'png',
                quality: 1,
                multiplier: 2
            });
            
            languageCanvasData[currentEditingLanguage] = dataURL;
            // Also update global reference
            if (window.languageCanvasData) {
                window.languageCanvasData[currentEditingLanguage] = dataURL;
            }
            console.log(`${currentEditingLanguage} dili için canvas state kaydedildi:`, dataURL.length);
        } catch (error) {
            console.error('Canvas state kaydetme hatası:', error);
        }
    };

    // Load language canvas state
    function loadLanguageCanvas() {
        console.log(`${currentEditingLanguage} dili yükleniyor...`);
        
        // Clear canvas
        canvas.clear();
        canvas.backgroundColor = '#ffffff';
        backgroundImage = null;
        window.fabricBackgroundImage = null;
        
        // Load saved canvas data (base64 image) if exists
        if (languageCanvasData[currentEditingLanguage]) {
            console.log(`${currentEditingLanguage} için canvas data bulundu`);
            const img = new Image();
            img.onload = function() {
                const fabricImage = new fabric.Image(img);
                const scale = Math.min(canvas.width / img.width, canvas.height / img.height);
                
                fabricImage.set({
                    left: (canvas.width - img.width * scale) / 2,
                    top: (canvas.height - img.height * scale) / 2,
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
                
                console.log(`${currentEditingLanguage} dili için canvas data yüklendi`);
            };
            img.onerror = function() {
                console.error(`${currentEditingLanguage} için canvas data yüklenemedi`);
            };
            img.src = languageCanvasData[currentEditingLanguage];
        } else if (languageBackgroundImages[currentEditingLanguage]) {
            // Fallback to preloaded background image for this language
            console.log(`${currentEditingLanguage} için background image bulundu (fallback)`);
            fabric.Image.fromURL(languageBackgroundImages[currentEditingLanguage], function(img) {
                const scale = Math.min(canvas.width / img.width, canvas.height / img.height);
                
                img.set({
                    left: (canvas.width - img.width * scale) / 2,
                    top: (canvas.height - img.height * scale) / 2,
                    scaleX: scale,
                    scaleY: scale,
                    selectable: false,
                    evented: false
                });
                
                backgroundImage = img;
                window.fabricBackgroundImage = backgroundImage;
                canvas.add(backgroundImage);
                canvas.sendToBack(backgroundImage);
                canvas.renderAll();
                
                console.log(`${currentEditingLanguage} için background image canvas'a eklendi`);
            }, {
                crossOrigin: 'anonymous'
            });
        } else {
            console.log(`${currentEditingLanguage} için hiçbir görsel bulunamadı`);
        }
        
        canvas.renderAll();
    }

    // Load existing images for all languages
    @php
        $existingImages = [];
        foreach(config('translatable.locales') as $locale) {
            $translation = $onboardingSlide->translate($locale, false);
            if ($translation && $translation->image) {
                $existingImages[$locale] = asset('storage/' . $translation->image);
            }
        }
    @endphp
    
    @if(!empty($existingImages))
        // Pre-load existing images for all languages
        const existingImages = @json($existingImages);
        let loadedImagesCount = 0;
        const totalImagesToLoad = Object.keys(existingImages).length;
        
        Object.keys(existingImages).forEach(locale => {
            const imageUrl = existingImages[locale];
            // Store URL immediately, even if image fails to load
            languageBackgroundImages[locale] = imageUrl;
            
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                loadedImagesCount++;
                
                console.log(`${locale} dili için görsel yüklendi:`, imageUrl.substring(0, 50) + '...');
                
                // If this is the current editing language, show it on canvas
                if (locale === currentEditingLanguage) {
                    const fabricImage = new fabric.Image(img);
                    const scale = Math.min(canvas.width / img.width, canvas.height / img.height);
                    
                    fabricImage.set({
                        left: (canvas.width - img.width * scale) / 2,
                        top: (canvas.height - img.height * scale) / 2,
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
                    
                    // Save initial state for current language
                    saveCurrentLanguageCanvas();
                }
                
                // If all images loaded, log completion
                if (loadedImagesCount === totalImagesToLoad) {
                    console.log('Tüm diller için görseller yüklendi:', languageBackgroundImages);
                }
            };
            img.onerror = function() {
                console.error(`${locale} dili için görsel yüklenemedi:`, imageUrl);
                loadedImagesCount++;
                
                // If all images processed (even with errors), log completion
                if (loadedImagesCount === totalImagesToLoad) {
                    console.log('Tüm diller için görseller işlendi (bazıları hata verdi):', languageBackgroundImages);
                }
            };
            img.src = imageUrl;
        });
    @else
        console.log('Hiçbir dil için mevcut görsel bulunamadı.');
    @endif

    // Text tool
    document.getElementById('textTool').addEventListener('click', function() {
        const text = new fabric.IText('Metin Ekle', {
            left: 100,
            top: 100,
            fontFamily: document.getElementById('textFont').value,
            fontSize: parseInt(document.getElementById('textSize').value),
            fill: document.getElementById('textColor').value,
            fontWeight: document.getElementById('textBold').checked ? 'bold' : 'normal',
            selectable: true
        });
        canvas.add(text);
        canvas.setActiveObject(text);
        text.enterEditing();
        saveState();
    });

    // Image tool
    document.getElementById('imageTool').addEventListener('click', function() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    fabric.Image.fromURL(event.target.result, function(img) {
                        img.scaleToWidth(100);
                        img.set({
                            left: 100,
                            top: 100,
                            selectable: true
                        });
                        canvas.add(img);
                        canvas.setActiveObject(img);
                        canvas.renderAll();
                        saveState();
                    });
                };
                reader.readAsDataURL(file);
            }
        };
        input.click();
    });

    // Text property controls
    document.getElementById('textFont').addEventListener('change', function() {
        const activeObject = canvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
            activeObject.set('fontFamily', this.value);
            canvas.renderAll();
        }
    });

    document.getElementById('textSize').addEventListener('change', function() {
        const activeObject = canvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
            activeObject.set('fontSize', parseInt(this.value));
            canvas.renderAll();
        }
    });

    document.getElementById('textColor').addEventListener('change', function() {
        const activeObject = canvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
            activeObject.set('fill', this.value);
            canvas.renderAll();
        }
    });

    document.getElementById('textBold').addEventListener('change', function() {
        const activeObject = canvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
            activeObject.set('fontWeight', this.checked ? 'bold' : 'normal');
            canvas.renderAll();
        }
    });

    // Object selection - show/hide delete button
    canvas.on('selection:created', function(e) {
        document.getElementById('deleteObject').style.display = 'inline-block';
    });

    canvas.on('selection:updated', function(e) {
        document.getElementById('deleteObject').style.display = 'inline-block';
    });

    canvas.on('selection:cleared', function() {
        document.getElementById('deleteObject').style.display = 'none';
    });

    // Delete object
    document.getElementById('deleteObject').addEventListener('click', function() {
        const activeObject = canvas.getActiveObject();
        if (activeObject && activeObject !== backgroundImage) {
            canvas.remove(activeObject);
            canvas.renderAll();
            document.getElementById('deleteObject').style.display = 'none';
            saveState();
        }
    });

    // Clear canvas
    document.getElementById('clearBtn').addEventListener('click', function() {
        if (confirm('Tüm nesneleri silmek istediğinizden emin misiniz?')) {
            const objects = canvas.getObjects();
            objects.forEach(obj => {
                if (obj !== backgroundImage) {
                    canvas.remove(obj);
                }
            });
            canvas.renderAll();
            saveState();
        }
    });

    // History management
    function saveState() {
        const json = canvas.toJSON();
        history = history.slice(0, historyIndex + 1);
        history.push(json);
        historyIndex = history.length - 1;
        
        // Limit history size
        if (history.length > 20) {
            history.shift();
            historyIndex--;
        }
    }

    // Initialize with empty state
    saveState();
});
</script>
@endsection



