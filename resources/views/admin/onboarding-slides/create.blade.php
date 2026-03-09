@extends('layouts.vertical', ['title' => 'Onboarding Slide Ekle - İzmir Time Machine', 'topbarTitle' => 'Onboarding Slide Ekle'])

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
                        <li class="breadcrumb-item active">Yeni Slide Ekle</li>
                    </ol>
                </div>
                <h4 class="page-title">Yeni Onboarding Slide Ekle</h4>
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

    <form action="{{ route('admin.onboarding-slides.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return saveCanvasData()">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h4 class="card-title mb-0">Çoklu Dil İçerik</h4></div>
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            @foreach($locales as $locale)
                                <li class="nav-item">
                                    <a class="nav-link @if($loop->first) active @endif" data-bs-toggle="tab" href="#{{ $locale }}" role="tab">
                                        <span class="d-none d-sm-block">{{ strtoupper($locale) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            @foreach($locales as $locale)
                                <div class="tab-pane @if($loop->first) active @endif" id="{{ $locale }}" role="tabpanel">
                                    <div class="mb-3">
                                        <label class="form-label" for="{{ $locale }}_title">Başlık @if($locale==='tr')<span class="text-danger">*</span>@endif</label>
                                        <input type="text" class="form-control @error($locale.'.title') is-invalid @enderror" id="{{ $locale }}_title" name="{{ $locale }}[title]" value="{{ old($locale.'.title') }}">
                                        @error($locale.'.title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="{{ $locale }}_description">Açıklama</label>
                                        <textarea class="form-control @error($locale.'.description') is-invalid @enderror" id="{{ $locale }}_description" name="{{ $locale }}[description]" rows="4">{{ old($locale.'.description') }}</textarea>
                                        @error($locale.'.description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="alert alert-info"><i class="mdi mdi-information-outline me-1"></i> En az bir dilde başlık girin. Görselleri aşağıdaki editörden yükleyin.</div>
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
                                Görselleri aşağıdaki Fabric.js editöründen yükleyin ve düzenleyin.
                            </div>
                            
                            <!-- Hidden inputs for edited canvas data for each language -->
                            @foreach($locales as $locale)
                                <input type="hidden" id="editedImageData_{{ $locale }}" name="{{ $locale }}[edited_image_data]">
                            @endforeach
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="sort_order">Sıra <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order',0) }}" min="0" required>
                            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simplified Image Editor -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-image-edit me-2"></i>
                            Görsel Düzenleyici
                        </h4>
                        <p class="text-muted mb-0">Görseli yükleyin ve metin/resim ekleyin</p>
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
                                                <option value="Helvetica">Helvetica</option>
                                                <option value="Times New Roman">Times New Roman</option>
                                                <option value="Georgia">Georgia</option>
                                                <option value="Verdana">Verdana</option>
                                                <option value="Courier New">Courier New</option>
                                                <option value="Impact">Impact</option>
                                                <option value="Comic Sans MS">Comic Sans MS</option>
                                                <option value="Tahoma">Tahoma</option>
                                                <option value="Trebuchet MS">Trebuchet MS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Boyut</label>
                                            <select class="form-select" id="textSize">
                                                <option value="12">12px</option>
                                                <option value="14">14px</option>
                                                <option value="16">16px</option>
                                                <option value="18">18px</option>
                                                <option value="20">20px</option>
                                                <option value="24" selected>24px</option>
                                                <option value="28">28px</option>
                                                <option value="32">32px</option>
                                                <option value="36">36px</option>
                                                <option value="48">48px</option>
                                                <option value="64">64px</option>
                                                <option value="72">72px</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Renk</label>
                                            <input type="color" class="form-control form-control-color" id="textColor" value="#000000" title="Metin rengi seçin">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Kalın</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" id="textBold">
                                                <label class="form-check-label" for="textBold">Kalın</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Canvas Container -->
                        <div class="canvas-container-wrapper">
                            <div class="canvas-container">
                                <canvas id="fabricCanvas" width="400" height="600" style="border: 2px solid #ddd; max-width: 100%; height: auto; cursor: crosshair;"></canvas>
                            </div>
                            <div class="canvas-info mt-2">
                                <small class="text-muted">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    Canvas boyutu: 400x600px (Dikey format) | Format: JPEG (Quality: 75%) - Optimize edildi
                                </small>
                            </div>
                            <div class="alert alert-info mt-3">
                                <i class="mdi mdi-lightbulb-on me-2"></i>
                                <strong>Önemli:</strong> Form gönderildiğinde editördeki görselin son hali (metin ve eklenen görseller dahil) otomatik olarak kaydedilecektir. Görseller optimize edilmiş kalitede kaydedilir.
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.onboarding-slides.index') }}" class="btn btn-light">
                            <i class="mdi mdi-arrow-left me-1"></i> İptal
                        </a>
                        <button type="submit" class="btn btn-info">
                            <i class="mdi mdi-check me-1"></i> Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('styles')
<style>
.image-editor-toolbar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.image-editor-toolbar .btn-group {
    margin-right: 5px;
}

.canvas-container-wrapper {
    text-align: center;
}

.canvas-container {
    display: inline-block;
    position: relative;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.text-properties {
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
    
    // Save current language canvas first
    if (typeof window.saveCurrentLanguageCanvas === 'function') {
        window.saveCurrentLanguageCanvas();
    }
    
    // Save all language canvas data to hidden inputs
    let hasAtLeastOneCanvas = false;
    
    const locales = @json(config('translatable.locales'));
    const canvasData = window.languageCanvasData || {};
    
    for (let locale of locales) {
        const hiddenInput = document.getElementById('editedImageData_' + locale);
        if (hiddenInput && canvasData[locale]) {
            hiddenInput.value = canvasData[locale];
            hasAtLeastOneCanvas = true;
            console.log('Canvas kaydedildi, dil:', locale, 'boyut:', canvasData[locale].length);
        }
    }
    
    if (!hasAtLeastOneCanvas) {
        console.log('Hiç canvas verisi yok!');
        // Check if at least one language has title filled
        let hasTitle = false;
        for (let locale of locales) {
            const titleInput = document.getElementById(locale + '_title');
            if (titleInput && titleInput.value.trim()) {
                hasTitle = true;
                break;
            }
        }
        
        if (!hasTitle) {
            alert('En az bir dilde başlık girmelisiniz!');
            return false;
        }
        
        // Warn user that no image was uploaded
        if (confirm('Hiçbir dil için görsel yüklenmedi. Devam etmek istiyor musunuz?')) {
            return true;
        } else {
            return false;
        }
    }
    
    console.log('Tüm canvas verileri kaydedildi, form gönderiliyor...');
    return true; // Allow form submission
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM yüklendi, Fabric.js başlatılıyor...');
    
    // Initialize Fabric.js canvas
    const canvas = new fabric.Canvas('fabricCanvas', {
        width: 400,
        height: 600,
        backgroundColor: '#ffffff',
        selection: true,
        preserveObjectStacking: true
    });
    
    console.log('Fabric.js canvas başlatıldı:', canvas);

    let backgroundImage = null;
    
    // Make canvas and backgroundImage globally accessible
    window.fabricCanvas = canvas;
    window.fabricBackgroundImage = null;
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

    // Language management
    let currentEditingLanguage = 'tr';
    let languageCanvasData = {}; // Store canvas data for each language
    let languageBackgroundImages = {}; // Store background images for each language
    
    // Make languageCanvasData globally accessible
    window.languageCanvasData = languageCanvasData;
    
    // Language selector change handler
    document.getElementById('editingLanguage').addEventListener('change', function() {
        saveCurrentLanguageCanvas();
        currentEditingLanguage = this.value;
        loadLanguageCanvas();
        console.log('Dil değiştirildi:', currentEditingLanguage);
    });
    
    // Current language image input handler
    document.getElementById('currentLanguageImage').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        
        console.log('Görsel yükleniyor:', file.name, 'dil:', currentEditingLanguage);
        
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
            window.fabricBackgroundImage = backgroundImage;
            languageBackgroundImages[currentEditingLanguage] = backgroundImage;
            
            canvas.add(backgroundImage);
            canvas.sendToBack(backgroundImage);
            canvas.renderAll();
            
            console.log('Background image eklendi, dil:', currentEditingLanguage);
            saveState();
        };
        img.src = URL.createObjectURL(file);
    });
    
    // Save current language canvas data
    window.saveCurrentLanguageCanvas = function() {
        if (!canvas || !backgroundImage) return;
        
        try {
            const dataURL = canvas.toDataURL({
                format: 'jpeg',
                quality: 0.75,
                multiplier: 1
            });
            languageCanvasData[currentEditingLanguage] = dataURL;
            // Also update global reference
            if (window.languageCanvasData) {
                window.languageCanvasData[currentEditingLanguage] = dataURL;
            }
            console.log('Canvas kaydedildi, dil:', currentEditingLanguage, 'boyut:', dataURL.length);
        } catch (error) {
            console.error('Canvas kaydetme hatası:', error);
        }
    };
    
    // Load language canvas data
    function loadLanguageCanvas() {
        // Clear canvas
        canvas.clear();
        canvas.backgroundColor = '#ffffff';
        backgroundImage = null;
        window.fabricBackgroundImage = null;
        
        // Load saved data if exists
        if (languageCanvasData[currentEditingLanguage]) {
            const img = new Image();
            img.onload = function() {
                canvas.loadFromDataURL(languageCanvasData[currentEditingLanguage], function() {
                    // Find background image
                    const objects = canvas.getObjects();
                    for (let obj of objects) {
                        if (obj.selectable === false && obj.evented === false) {
                            backgroundImage = obj;
                            window.fabricBackgroundImage = backgroundImage;
                            break;
                        }
                    }
                    canvas.renderAll();
                    console.log('Canvas yüklendi, dil:', currentEditingLanguage);
                });
            };
            img.src = languageCanvasData[currentEditingLanguage];
        }
        
        canvas.renderAll();
    }
    
    // Save to language button handler
    document.getElementById('saveToLanguage').addEventListener('click', function() {
        saveCurrentLanguageCanvas();
        
        // Update hidden input for current language
        const hiddenInput = document.getElementById('editedImageData_' + currentEditingLanguage);
        if (hiddenInput && languageCanvasData[currentEditingLanguage]) {
            hiddenInput.value = languageCanvasData[currentEditingLanguage];
            console.log('Hidden input güncellendi, dil:', currentEditingLanguage);
        }
        
        alert(currentEditingLanguage.toUpperCase() + ' dili için canvas kaydedildi!');
    });

    // Text tool
    document.getElementById('textTool').addEventListener('click', function() {
        console.log('Text tool tıklandı');
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
        console.log('Metin canvas\'a eklendi');
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



