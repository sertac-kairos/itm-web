@extends('layouts.vertical', ['title' => 'Bölge Düzenle - İzmir Time Machine', 'topbarTitle' => 'Bölge Düzenle'])

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')

<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.regions.index') }}">Bölgeler</a></li>
                    <li class="breadcrumb-item active">Düzenle</li>
                </ol>
            </div>
            <h4 class="page-title">Bölge Düzenle</h4>
        </div>
    </div>
</div>

<!-- Form -->
<form action="{{ route('admin.regions.update', $region) }}" method="POST" enctype="multipart/form-data">
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
                    <h4 class="header-title">Bölge Bilgileri</h4>
                    <p class="text-muted mb-0">Bölgenin temel özelliklerini belirleyin</p>
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
                                    @if($region->hasTranslation($locale))
                                        <i class="mdi mdi-check-circle text-success ms-1"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Dil İçerikleri -->
                    <div class="tab-content">
                        @foreach($locales as $index => $locale)
                            <div class="tab-pane {{ $index === 0 ? 'show active' : '' }}" id="{{ $locale }}-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="{{ $locale }}_name" class="form-label">
                                                Bölge Adı @if($locale === 'tr') <span class="text-danger">*</span> @endif
                                                <small class="text-muted">({{ strtoupper($locale) }}) @if($locale === 'tr') - Zorunlu @endif</small>
                                                @if($locale === 'tr' && $loop->count > 1)
                                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="translateFromTurkish()">
                                                        <i class="mdi mdi-translate me-1"></i>İngilizceye Çevir
                                                    </button>
                                                @endif
                                            </label>
                                            <input type="text" class="form-control @error($locale.'.name') is-invalid @enderror" 
                                                   id="{{ $locale }}_name" name="{{ $locale }}[name]" 
                                                   value="{{ old($locale.'.name', $region->translate($locale, false)?->name) }}">
                                            @error($locale.'.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="{{ $locale }}_subtitle" class="form-label">
                                                Alt Başlık
                                                <small class="text-muted">({{ strtoupper($locale) }})</small>
                                                @if($locale === 'tr' && $loop->count > 1)
                                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="translateSubtitleFromTurkish()">
                                                        <i class="mdi mdi-translate me-1"></i>İngilizceye Çevir
                                                    </button>
                                                @endif
                                            </label>
                                            <input type="text" class="form-control @error($locale.'.subtitle') is-invalid @enderror" 
                                                   id="{{ $locale }}_subtitle" name="{{ $locale }}[subtitle]" 
                                                   value="{{ old($locale.'.subtitle', $region->translate($locale, false)?->subtitle) }}">
                                            @error($locale.'.subtitle')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="{{ $locale }}_description" class="form-label">
                                                Açıklama 
                                                <small class="text-muted">({{ strtoupper($locale) }})</small>
                                                @if($locale === 'tr' && $loop->count > 1)
                                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="translateDescriptionFromTurkish()">
                                                        <i class="mdi mdi-translate me-1"></i>İngilizceye Çevir
                                                    </button>
                                                @endif
                                            </label>
                                            <textarea class="form-control @error($locale.'.description') is-invalid @enderror" 
                                                      id="{{ $locale }}_description" name="{{ $locale }}[description]" 
                                                      rows="4" placeholder="Bölge hakkında açıklama yazın...">{{ old($locale.'.description', $region->translate($locale, false)?->description) }}</textarea>
                                            @error($locale.'.description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="{{ $locale }}_audio_guide" class="form-label">
                                                Ses Rehberi 
                                                <small class="text-muted">({{ strtoupper($locale) }}) - MP3</small>
                                            </label>
                                            
                                            @php
                                                $translation = $region->translate($locale, false);
                                                $audioPath = $translation ? $translation->audio_guide_path : null;
                                            @endphp
                                            
                                            @if($audioPath && Storage::disk('public')->exists($audioPath))
                                                <div class="mb-2">
                                                    <div class="d-flex align-items-center bg-light p-2 rounded">
                                                        <i class="mdi mdi-volume-high text-primary me-2"></i>
                                                        <span class="me-auto">{{ basename($audioPath) }}</span>
                                                        <audio controls class="me-2">
                                                            <source src="{{ asset('storage/' . $audioPath) }}" type="audio/mpeg">
                                                            Tarayıcınız ses dosyasını desteklemiyor.
                                                        </audio>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <input type="file" 
                                                   class="form-control @error($locale.'.audio_guide') is-invalid @enderror"
                                                   id="{{ $locale }}_audio_guide" 
                                                   name="{{ $locale }}[audio_guide]"
                                                   accept=".mp3">
                                            <div class="form-text">
                                                <i class="mdi mdi-volume-high me-1"></i>
                                                MP3 formatında ses dosyası yükleyebilirsiniz (Maksimum: 10MB)
                                                @if($audioPath)
                                                    <br><small class="text-warning">Yeni dosya yüklerseniz mevcut dosya değiştirilir.</small>
                                                @endif
                                            </div>
                                            @error($locale.'.audio_guide')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Yan Panel -->
        <div class="col-lg-4">
            <!-- Görsel Ayarları -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Görsel Ayarları</h4>
                </div>
                <div class="card-body">
                    <!-- Mevcut Resim -->
                    @if($region->main_image)
                        <div class="mb-3">
                            <label class="form-label">Mevcut Resim</label>
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $region->main_image) }}" 
                                     alt="Region Image" class="img-fluid rounded" style="max-height: 200px;">
                                <small class="text-muted d-block mt-1">Yeni resim seçerek değiştirebilirsiniz</small>
                            </div>
                        </div>
                    @endif

                    <!-- Ana Resim -->
                    <div class="mb-3">
                        <label for="main_image" class="form-label">{{ $region->main_image ? 'Yeni Resim' : 'Ana Resim' }}</label>
                        <input type="file" class="form-control @error('main_image') is-invalid @enderror" 
                               id="main_image" name="main_image" accept="image/*">
                        <small class="text-muted">Maksimum 2MB. Önerilen boyut: 800x600px</small>
                        @error('main_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Renk Kodu -->
                    <div class="mb-3">
                        <label for="color_code" class="form-label">
                            Bölge Rengi <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color @error('color_code') is-invalid @enderror" 
                                   id="color_code" name="color_code" value="{{ old('color_code', $region->color_code) }}" required>
                            <input type="text" class="form-control @error('color_code') is-invalid @enderror" 
                                   id="color_code_text" value="{{ old('color_code', $region->color_code) }}" pattern="^#[0-9a-fA-F]{6}$">
                        </div>
                        <small class="text-muted">Haritada bu renk ile gösterilecek</small>
                        @error('color_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sıralama -->
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">
                            Sıralama <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                               id="sort_order" name="sort_order" value="{{ old('sort_order', $region->sort_order) }}" min="0" required>
                        <small class="text-muted">Küçük sayılar önce gösterilir</small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Koordinatlar -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="mdi mdi-map-marker me-1"></i>
                            Koordinatlar
                        </label>
                        <div class="row">
                            <div class="col-6">
                                <label for="latitude" class="form-label small">Enlem</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude', $region->latitude) }}" 
                                           step="0.00000001" min="-90" max="90" placeholder="38.4192">
                                    <button type="button" class="btn btn-outline-primary" onclick="openMapModal()" title="Haritadan Seç">
                                        <i class="mdi mdi-map"></i>
                                    </button>
                                </div>
                                <small class="text-muted">-90 ile +90 arası</small>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="longitude" class="form-label small">Boylam</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude', $region->longitude) }}" 
                                           step="0.00000001" min="-180" max="180" placeholder="27.1287">
                                    <button type="button" class="btn btn-outline-primary" onclick="openMapModal()" title="Haritadan Seç">
                                        <i class="mdi mdi-map"></i>
                                    </button>
                                </div>
                                <small class="text-muted">-180 ile +180 arası</small>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="openMapModal()">
                                <i class="mdi mdi-map-marker me-1"></i>
                                Haritadan Koordinat Seç
                            </button>
                            <small class="text-muted d-block mt-1">İzmir için örnek: 38.4192, 27.1287</small>
                        </div>
                    </div>

                    <!-- Hotspot Image -->
                    <div class="mb-3">
                        <label for="hotspot_image" class="form-label">
                            <i class="mdi mdi-image-multiple me-1"></i>
                            Hotspot Image (JSON)
                        </label>
                        <textarea class="form-control @error('hotspot_image') is-invalid @enderror" 
                                  id="hotspot_image" name="hotspot_image" rows="8" 
                                  placeholder='{"hotspots": [{"x": 100, "y": 200, "title": "Hotspot 1", "description": "Açıklama"}]}'>{{ old('hotspot_image', $region->hotspot_image ? json_encode($region->hotspot_image, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                        <div class="form-text">
                            <i class="mdi mdi-information-outline me-1"></i>
                            JSON formatında hotspot verilerini girin. Örnek: {"hotspots": [{"x": 100, "y": 200, "title": "Başlık", "description": "Açıklama"}]}
                        </div>
                        @error('hotspot_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Durum -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active', $region->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                        <small class="text-muted">Mobil uygulamada gösterilsin mi?</small>
                    </div>
                </div>
            </div>

            <!-- Önizleme -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Önizleme</h4>
                </div>
                <div class="card-body">
                    <div class="preview-card p-3 border rounded" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                        <div class="d-flex align-items-center mb-2">
                            <span class="region-color-preview me-2" style="background-color: {{ $region->color_code }}; width: 20px; height: 20px; border-radius: 50%; display: inline-block;"></span>
                            <h6 class="mb-0 region-name-preview">{{ $region->name ?: 'Bölge Adı' }}</h6>
                        </div>
                        <p class="text-muted mb-0 region-desc-preview small">{{ $region->description ?: 'Bölge açıklaması burada görünecek...' }}</p>
                        <div class="mt-2">
                            <span class="badge {{ $region->is_active ? 'badge-success-lighten' : 'badge-danger-lighten' }} region-status-preview">
                                {{ $region->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                            <span class="badge badge-dark-lighten region-order-preview">Sıra: {{ $region->sort_order }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Bölge Güncelleniyor</h6>
                            <p class="text-muted mb-0">En az bir dilde (Türkçe önerilir) içerik girmeniz yeterlidir</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.regions.index') }}" class="btn btn-secondary me-2">
                                <i class="mdi mdi-arrow-left me-1"></i>İptal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save me-1"></i>Değişiklikleri Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Harita Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">
                    <i class="mdi mdi-map-marker me-2"></i>
                    Koordinat Seçimi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div id="siteMap" style="height: 500px; width: 100%; border-radius: 8px; background-color: #f8f9fa; border: 1px solid #dee2e6; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; flex: 1;">
                            <div style="text-align: center; color: #6c757d;">
                                <i class="mdi mdi-map" style="font-size: 48px; margin-bottom: 10px;"></i>
                                <h5>Harita Yükleniyor...</h5>
                                <p>JavaScript çalışıyorsa bu mesaj değişecek</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Seçilen Koordinatlar</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Enlem</label>
                                    <input type="text" class="form-control" id="modalLatitude" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Boylam</label>
                                    <input type="text" class="form-control" id="modalLongitude" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tahmini Adres</label>
                                    <textarea class="form-control" id="modalAddress" rows="3" readonly placeholder="Adres yükleniyor..."></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success" onclick="selectCoordinates()">
                                        <i class="mdi mdi-check me-1"></i>
                                        Bu Koordinatları Seç
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetMap()">
                                        <i class="mdi mdi-refresh me-1"></i>
                                        Sıfırla
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hızlı Koordinat Seçimi -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">Hızlı Seçim</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setQuickCoordinates(38.4192, 27.1287, 'İzmir Merkez')">
                                        <i class="mdi mdi-map-marker me-1"></i>
                                        İzmir Merkez
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="setQuickCoordinates(38.4622, 27.0923, 'Bornova')">
                                        <i class="mdi mdi-map-marker me-1"></i>
                                        Bornova
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="setQuickCoordinates(38.4075, 27.1425, 'Konak')">
                                        <i class="mdi mdi-map-marker me-1"></i>
                                        Konak
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="setQuickCoordinates(38.4333, 27.1500, 'Alsancak')">
                                        <i class="mdi mdi-map-marker me-1"></i>
                                        Alsancak
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>
                    İptal
                </button>
                <button type="button" class="btn btn-primary" onclick="selectCoordinates()">
                    <i class="mdi mdi-check me-1"></i>
                    Koordinatları Seç
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<!-- Google Maps will be loaded dynamically -->
@endsection

@section('scripts')
<script>
    // Global variables for map
    let selectedLat = null;
    let selectedLng = null;

    // Open map modal
    window.openMapModal = function() {
        const modal = new bootstrap.Modal(document.getElementById('mapModal'));
        modal.show();
        
        setTimeout(function() {
            initSiteMap();
        }, 300);
    };

    // Initialize Google Map
    function initSiteMap() {
        // Load Google Maps API if not already loaded
        if (typeof google === 'undefined' || !google.maps || !google.maps.Map) {
            loadGoogleMapsAPI().then(() => {
                initGoogleMap();
            }).catch((error) => {
                console.error('Failed to load Google Maps API:', error);
            });
        } else {
            initGoogleMap();
        }
    }

    // Load Google Maps API
    function loadGoogleMapsAPI() {
        return new Promise((resolve, reject) => {
            if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
                resolve();
                return;
            }

            // Check if script is already being loaded
            if (document.querySelector('script[src*="maps.googleapis.com"]')) {
                // Wait for existing script to load
                const checkLoaded = setInterval(() => {
                    if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
                        clearInterval(checkLoaded);
                        resolve();
                    }
                }, 100);
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config("services.google_maps.api_key") }}&libraries=places&language=tr&region=TR&loading=async&callback=initGoogleMapsCallback';
            script.async = true;
            script.defer = true;
            
            // Global callback function
            window.initGoogleMapsCallback = () => {
                if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
                    resolve();
                } else {
                    reject(new Error('Google Maps API failed to initialize'));
                }
            };
            
            script.onerror = () => reject(new Error('Failed to load Google Maps API'));
            
            document.head.appendChild(script);
        });
    }

    // Global variables for map
    let currentMap = null;
    let currentMarker = null;

    // Initialize the actual Google Map
    function initGoogleMap() {
        const mapContainer = document.getElementById('siteMap');
        if (!mapContainer) {
            console.error('Map container not found');
            return;
        }

        // Clear loading content
        mapContainer.innerHTML = '';

        // Set İzmir center
        const izmirCenter = { lat: 38.4192, lng: 27.1287 };
        
        // Create Google Map
        currentMap = new google.maps.Map(mapContainer, {
            center: izmirCenter,
            zoom: 12,
            mapTypeControl: true,
            zoomControl: true,
            streetViewControl: true,
            fullscreenControl: true,
            mapTypeId: 'roadmap'
        });

        // Create search box
        createSearchBox();

        // Map click event
        google.maps.event.addListener(currentMap, 'click', function(event) {
            addMarker(event.latLng);
        });

        // Add existing markers if coordinates are provided
        const existingLat = document.getElementById('modalLatitude')?.value;
        const existingLng = document.getElementById('modalLongitude')?.value;
        
        if (existingLat && existingLng) {
            const position = { lat: parseFloat(existingLat), lng: parseFloat(existingLng) };
            addMarker(position);
        }
    }

    // Create autocomplete search for places
    function createSearchBox() {
        if (google?.maps?.places?.Autocomplete) {
            initializeClassicAutocomplete();
        } else {
            setTimeout(createSearchBox, 200);
        }
    }
    
    // Initialize classic Autocomplete
    function initializeClassicAutocomplete() {
        try {
            // Create input element
            const input = document.createElement('input');
            input.type = 'text';
            input.placeholder = '🔍 Mekan ara veya Enter\'a bas (örn: Konak, İzmir)';
            input.style.cssText = `
                width: 350px;
                height: 40px;
                position: absolute;
                top: 10px;
                left: 10px;
                z-index: 1000;
                border: 2px solid #4CAF50;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                padding: 0 12px;
                font-size: 14px;
                background: white;
                outline: none;
            `;
            
            // Add focus effect
            input.addEventListener('focus', () => {
                input.style.borderColor = '#2196F3';
                input.style.boxShadow = '0 3px 8px rgba(33, 150, 243, 0.4)';
            });
            
            input.addEventListener('blur', () => {
                input.style.borderColor = '#4CAF50';
                input.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';
            });
            
            // Add to map
            const mapContainer = document.getElementById('siteMap');
            mapContainer.style.position = 'relative';
            mapContainer.appendChild(input);
            
            // Create autocomplete
            const autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['establishment', 'geocode'],
                componentRestrictions: { country: 'tr' },
                fields: ['geometry', 'name', 'formatted_address', 'place_id']
            });
            
            autocomplete.bindTo('bounds', currentMap);
            
            // Listen for place selection
            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                
                // If no geometry, fetch full place details
                if (!place.geometry && place.place_id) {
                    const service = new google.maps.places.PlacesService(currentMap);
                    service.getDetails({
                        placeId: place.place_id,
                        fields: ['geometry', 'name', 'formatted_address', 'place_id']
                    }, (detailedPlace, status) => {
                        if (status === google.maps.places.PlacesServiceStatus.OK && detailedPlace) {
                            processPlace(detailedPlace);
                        }
                    });
                    return;
                }
                
                // If we have geometry, process immediately
                if (place.geometry && place.geometry.location) {
                    processPlace(place);
                } 
                // If no geometry and no place_id, geocode the input value
                else if (place.name && !place.place_id) {
                    geocodeAddress(place.name);
                }
            });
            
            // Geocode address from text
            function geocodeAddress(address) {
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ 
                    address: address,
                    componentRestrictions: { country: 'TR' },
                    region: 'TR'
                }, (results, status) => {
                    if (status === 'OK' && results[0]) {
                        const place = {
                            name: results[0].formatted_address,
                            formatted_address: results[0].formatted_address,
                            geometry: results[0].geometry
                        };
                        processPlace(place);
                    } else {
                        alert('Konum bulunamadı. Lütfen listeden bir öğe seçin veya daha spesifik bir arama yapın.');
                    }
                });
            }
            
            // Process place and add marker
            function processPlace(place) {
                addMarker(place.geometry.location);
                
                const addressField = document.getElementById('modalAddress');
                if (addressField) {
                    addressField.value = place.formatted_address || place.name;
                }
                
                currentMap.setCenter(place.geometry.location);
                if (place.geometry.viewport) {
                    currentMap.fitBounds(place.geometry.viewport);
                } else {
                    currentMap.setZoom(17);
                }
            }
            
        } catch (error) {
            console.error('Error initializing autocomplete:', error);
        }
    }

    // Add marker function
    function addMarker(position) {
        if (!currentMap) return;
        
        // Get lat/lng from position
        const lat = typeof position.lat === 'function' ? position.lat() : position.lat;
        const lng = typeof position.lng === 'function' ? position.lng() : position.lng;
        
        if (!lat || !lng) return;
        
        // Remove previous marker
        if (currentMarker) {
            currentMarker.setMap(null);
        }

        // Create new marker
        currentMarker = new google.maps.Marker({
            position: { lat, lng },
            map: currentMap,
            title: 'Seçilen Konum',
            animation: google.maps.Animation.DROP,
            icon: {
                url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(40, 40)
            }
        });
        
        // Add info window
        const infoWindow = new google.maps.InfoWindow({
            content: `<div style="padding: 8px;">
                <strong>Seçilen Konum</strong><br>
                <small>Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</small>
            </div>`
        });
        
        currentMarker.addListener('click', () => {
            infoWindow.open(currentMap, currentMarker);
        });
        
        setTimeout(() => infoWindow.open(currentMap, currentMarker), 300);
        
        // Update global coordinates
        selectedLat = lat;
        selectedLng = lng;
        
        // Update modal fields
        document.getElementById('modalLatitude').value = lat.toFixed(8);
        document.getElementById('modalLongitude').value = lng.toFixed(8);

        // Get address
        getAddressFromCoordinates(lat, lng);
    }

    // Set quick coordinates
    function setQuickCoordinates(lat, lng, name) {
        // Add marker to map
        addMarker({ lat, lng });
        
        // Center and zoom map
        if (currentMap) {
            currentMap.setCenter({ lat, lng });
            currentMap.setZoom(14);
        }
        
        showToast(`${name} koordinatları seçildi!`);
    }

    // Get address from coordinates using Google Geocoding API
    function getAddressFromCoordinates(lat, lng) {
        const addressField = document.getElementById('modalAddress');
        addressField.value = 'Adres yükleniyor...';
        
        // Use Google Geocoding API for reverse geocoding
        if (typeof google !== 'undefined' && google.maps) {
            const geocoder = new google.maps.Geocoder();
            const latlng = { lat: lat, lng: lng };
            
            geocoder.geocode({ location: latlng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    addressField.value = results[0].formatted_address;
                } else {
                    addressField.value = 'Adres bulunamadı';
                }
            });
        } else {
            // Fallback to OpenStreetMap Nominatim API
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        addressField.value = data.display_name;
                    } else {
                        addressField.value = 'Adres bulunamadı';
                    }
                })
                .catch(error => {
                    console.error('Address lookup error:', error);
                    addressField.value = 'Adres yüklenirken hata oluştu';
                });
        }
    }

    // Select coordinates and close modal
    function selectCoordinates() {
        if (selectedLat && selectedLng) {
            // Update form fields
            document.getElementById('latitude').value = selectedLat.toFixed(6);
            document.getElementById('longitude').value = selectedLng.toFixed(6);
            
            // Add visual feedback
            document.getElementById('latitude').classList.add('border-success');
            document.getElementById('longitude').classList.add('border-success');
            setTimeout(() => {
                document.getElementById('latitude').classList.remove('border-success');
                document.getElementById('longitude').classList.remove('border-success');
            }, 1000);
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('mapModal'));
            modal.hide();
            
            showToast('Koordinatlar başarıyla seçildi!');
        } else {
            alert('Lütfen haritadan bir konum seçin.');
        }
    }

    // Reset map
    function resetMap() {
        selectedLat = null;
        selectedLng = null;
        
        // Clear modal fields
        document.getElementById('modalLatitude').value = '';
        document.getElementById('modalLongitude').value = '';
        document.getElementById('modalAddress').value = '';
        
        // Clear current marker
        if (currentMarker) {
            currentMarker.setMap(null);
            currentMarker = null;
        }
        
        // Reset map to İzmir center
        if (currentMap) {
            currentMap.setCenter({ lat: 38.4192, lng: 27.1287 });
            currentMap.setZoom(12);
        }
    }

    // Toast notification function
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="mdi mdi-check-circle me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Make functions globally accessible
    // Attach functions to window object for global access
    window.initSiteMap = initSiteMap;
    window.addMarker = addMarker;
    window.resetMap = resetMap;
    window.setQuickCoordinates = setQuickCoordinates;
    window.selectCoordinates = selectCoordinates;
    window.showToast = showToast;

    document.addEventListener('DOMContentLoaded', function() {
        // Color picker synchronization
        const colorPicker = document.getElementById('color_code');
        const colorText = document.getElementById('color_code_text');
        const colorPreview = document.querySelector('.region-color-preview');

        function updateColor(value) {
            colorPicker.value = value;
            colorText.value = value;
            colorPreview.style.backgroundColor = value;
        }

        colorPicker.addEventListener('input', function() {
            updateColor(this.value);
        });

        colorText.addEventListener('input', function() {
            if (/^#[0-9a-fA-F]{6}$/.test(this.value)) {
                updateColor(this.value);
            }
        });

        // Preview updates
        function updatePreview() {
            const activeTab = document.querySelector('.tab-pane.active');
            const nameInput = activeTab.querySelector('input[type="text"]');
            const descInput = activeTab.querySelector('textarea');
            const sortOrder = document.getElementById('sort_order').value;
            const isActive = document.getElementById('is_active').checked;

            document.querySelector('.region-name-preview').textContent = nameInput.value || 'Bölge Adı';
            document.querySelector('.region-desc-preview').textContent = descInput.value || 'Bölge açıklaması burada görünecek...';
            document.querySelector('.region-order-preview').textContent = 'Sıra: ' + sortOrder;
            
            const statusBadge = document.querySelector('.region-status-preview');
            if (isActive) {
                statusBadge.textContent = 'Aktif';
                statusBadge.className = 'badge badge-success-lighten region-status-preview';
            } else {
                statusBadge.textContent = 'Pasif';
                statusBadge.className = 'badge badge-danger-lighten region-status-preview';
            }
        }

        // Event listeners for preview
        document.querySelectorAll('input[type="text"], textarea').forEach(function(input) {
            input.addEventListener('input', updatePreview);
        });

        document.getElementById('sort_order').addEventListener('input', updatePreview);
        document.getElementById('is_active').addEventListener('change', updatePreview);

        // Tab change event
        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', updatePreview);
        });

        // Image preview
        document.getElementById('main_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You can add image preview here if needed
                };
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            let hasError = false;
            let hasAtLeastOneName = false;
            let firstFilledInput = null;
            
            // Check if at least one language name is filled
            const nameInputs = document.querySelectorAll('input[name$="[name]"]');
            nameInputs.forEach(function(input) {
                input.classList.remove('is-invalid'); // Reset validation styling
                if (input.value.trim()) {
                    hasAtLeastOneName = true;
                    if (!firstFilledInput) {
                        firstFilledInput = input;
                    }
                }
            });

            if (!hasAtLeastOneName) {
                hasError = true;
                
                // Mark Turkish input as invalid and show Turkish tab
                const trName = document.querySelector('input[name="tr[name]"]');
                if (trName) {
                    trName.classList.add('is-invalid');
                    const trTab = document.querySelector('a[href="#tr-tab"]');
                    if (trTab) {
                        trTab.click();
                    }
                }
            }

            if (hasError) {
                e.preventDefault();
                alert('En az bir dilde bölge adı girilmelidir!');
            }
        });

        // Initialize preview
        updatePreview();
    });

    // Translation functions - Global scope for onclick handlers
    function translateFromTurkish() {
            const turkishName = document.getElementById('tr_name');
            if (!turkishName || !turkishName.value.trim()) {
                alert('Lütfen önce Türkçe bölge adı girin.');
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
            fetch('/admin/regions/translate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify({
                    text: turkishName.value,
                    from: 'tr',
                    to: 'en'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.translated_text) {
                    document.getElementById('en_name').value = data.translated_text;
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

    function translateDescriptionFromTurkish() {
        const turkishDescription = document.getElementById('tr_description');
        if (!turkishDescription || !turkishDescription.value.trim()) {
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
        fetch('/admin/regions/translate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify({
                text: turkishDescription.value,
                from: 'tr',
                to: 'en'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.translated_text) {
                document.getElementById('en_description').value = data.translated_text;
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

    function translateSubtitleFromTurkish() {
            const turkishSubtitle = document.getElementById('tr_subtitle');
            if (!turkishSubtitle || !turkishSubtitle.value.trim()) {
                alert('Lütfen önce Türkçe alt başlık girin.');
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
            fetch('/admin/regions/translate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify({
                    text: turkishSubtitle.value,
                    from: 'tr',
                    to: 'en'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.translated_text) {
                    document.getElementById('en_subtitle').value = data.translated_text;
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
</script>
@endsection