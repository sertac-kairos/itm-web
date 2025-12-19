@extends('layouts.vertical', ['title' => 'Bildirim Gönder - İzmir Time Machine', 'topbarTitle' => 'Bildirim Gönder'])

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.devices.index') }}">Cihazlar</a></li>
                        <li class="breadcrumb-item active">Bildirim Gönder</li>
                    </ol>
                </div>
                <h4 class="page-title">Bildirim Gönder</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Hızlı Koordinat Seçimi -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">
                        <i class="mdi mdi-map-marker me-2"></i>
                        Hızlı Koordinat Seçimi
                    </h4>
                    <p class="text-muted mb-0">Önceden tanımlanmış konumları hızlıca seçin</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-primary w-100" onclick="setCoordinates(38.4192, 27.1287, 'İzmir Merkez')">
                                <i class="mdi mdi-map-marker me-1"></i>
                                İzmir Merkez
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-success w-100" onclick="setCoordinates(38.4622, 27.0923, 'Bornova')">
                                <i class="mdi mdi-map-marker me-1"></i>
                                Bornova
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-warning w-100" onclick="setCoordinates(38.4075, 27.1425, 'Konak')">
                                <i class="mdi mdi-map-marker me-1"></i>
                                Konak
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-info w-100" onclick="setCoordinates(38.4333, 27.1500, 'Alsancak')">
                                <i class="mdi mdi-map-marker me-1"></i>
                                Alsancak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Harita ve Bildirim Alanı -->
    <div class="row">
        <!-- Harita Alanı -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="header-title">
                        <i class="mdi mdi-map me-2"></i>
                        Konum Seçimi
                    </h4>
                    <p class="text-muted mb-0">Haritaya tıklayarak bildirim göndermek istediğiniz konumu seçin</p>
                </div>
                <div class="card-body d-flex flex-column">
                    <div id="siteMap" style="height: 500px; width: 100%; border-radius: 8px; background-color: #f8f9fa; border: 1px solid #dee2e6; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; flex: 1;">
                        <div style="text-align: center; color: #6c757d;">
                            <i class="mdi mdi-map" style="font-size: 48px; margin-bottom: 10px;"></i>
                            <h5>Harita Yükleniyor...</h5>
                            <p>JavaScript çalışıyorsa bu mesaj değişecek</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Enlem</label>
                                    <input type="number" class="form-control" id="latitude" name="latitude" step="0.000001" min="-90" max="90" placeholder="38.4192" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Boylam</label>
                                    <input type="number" class="form-control" id="longitude" name="longitude" step="0.000001" min="-180" max="180" placeholder="27.1287" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bildirim Formu -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="header-title">
                        <i class="mdi mdi-map-marker-radius me-2"></i>
                        Konum Bazlı Bildirim
                    </h4>
                    <p class="text-muted mb-0">Haritadan alan seçerek bildirim gönder</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.devices.send-location') }}" method="POST" id="locationForm">
                        @csrf
                        <div class="mb-3">
                            <label for="radius" class="form-label">Yarıçap (metre) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="radius" name="radius" required min="100" max="50000" value="2000" step="100">
                                <span class="input-group-text">m</span>
                            </div>
                            <small class="form-text text-muted">100m - 50km arası (varsayılan: 2000m)</small>
                        </div>
                        <div class="mb-3">
                            <label for="title_location" class="form-label">Başlık <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title_location" name="title" required maxlength="255" placeholder="Bildirim başlığı">
                        </div>
                        <div class="mb-3">
                            <label for="message_location" class="form-label">Mesaj <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message_location" name="message" rows="4" required maxlength="1000" placeholder="Bildirim mesajı"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="data_location" class="form-label">Ek Veri</label>
                            <input type="text" class="form-control" id="data_location" name="data" maxlength="500" placeholder="JSON formatında ek veri (opsiyonel)">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="mdi mdi-send me-1"></i>
                                Seçili Alana Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Kullanıcı Gruplarına Bildirim -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">
                        <i class="mdi mdi-account-group me-2"></i>
                        Kullanıcı Gruplarına Bildirim
                    </h4>
                    <p class="text-muted mb-0">Belirli kullanıcı gruplarına hedefli bildirim gönder</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.devices.send-group') }}" method="POST" id="groupForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="group" class="form-label">Kullanıcı Grubu <span class="text-danger">*</span></label>
                                    <select class="form-select" id="group" name="group" required>
                                        <option value="">Grup seçin...</option>
                                        @foreach($userGroups as $key => $group)
                                            <option value="{{ $key }}" data-count="{{ $group['count'] }}" data-description="{{ $group['description'] }}">
                                                {{ $group['name'] }} ({{ $group['count'] }} kişi)
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text" id="groupDescription"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="title_group" class="form-label">Başlık <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title_group" name="title" required maxlength="255" placeholder="Bildirim başlığı">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="data_group" class="form-label">Ek Veri</label>
                                    <input type="text" class="form-control" id="data_group" name="data" maxlength="500" placeholder="JSON (opsiyonel)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="message_group" class="form-label">Mesaj <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message_group" name="message" rows="3" required maxlength="1000" placeholder="Bildirim mesajı"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-info">
                                <i class="mdi mdi-send me-1"></i>
                                Seçili Gruba Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Herkese Bildirim -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">
                        <i class="mdi mdi-broadcast me-2"></i>
                        Herkese Bildirim
                    </h4>
                    <p class="text-muted mb-0">Tüm cihazlara bildirim gönder</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.devices.send-all') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="title_all" class="form-label">Başlık <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title_all" name="title" required maxlength="255" placeholder="Bildirim başlığı">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="message_all" class="form-label">Mesaj <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message_all" name="message" rows="2" required maxlength="1000" placeholder="Bildirim mesajı"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="data_all" class="form-label">Ek Veri</label>
                                    <input type="text" class="form-control" id="data_all" name="data" maxlength="500" placeholder="JSON (opsiyonel)">
                                </div>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-send me-1"></i>
                                Tüm Cihazlara Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- İstatistikler -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">
                        <i class="mdi mdi-chart-bar me-2"></i>
                        Bildirim İstatistikleri
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $deviceStats['total_devices'] }}</h3>
                                <p class="text-muted mb-0">Toplam Cihaz</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success">{{ $deviceStats['active_devices'] }}</h3>
                                <p class="text-muted mb-0">Aktif Cihaz</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-info">{{ $deviceStats['online_devices'] }}</h3>
                                <p class="text-muted mb-0">Online Cihaz</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $deviceStats['recent_devices'] }}</h3>
                                <p class="text-muted mb-0">Son 7 Gün Aktif</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<!-- Google Maps will be loaded dynamically -->

<script>
    let selectedLat = null;
    let selectedLng = null;

    // Simple console logging (debug panel removed)
    function debugLog(message, type = 'info') {
        console.log(`[${new Date().toLocaleTimeString()}] ${message}`);
    }

    // Initialize Google Map
    function initGoogleMap() {
        // Load Google Maps API if not already loaded
        if (typeof google === 'undefined' || !google.maps || !google.maps.Map) {
            loadGoogleMapsAPI().then(() => {
                initGoogleMapInstance();
            }).catch((error) => {
                console.error('Failed to load Google Maps API:', error);
            });
        } else {
            initGoogleMapInstance();
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

    // Global variables for map management
    let currentMap = null;
    let currentMarker = null;
    let searchBox = null;

    // Initialize the actual Google Map
    function initGoogleMapInstance() {
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
        const existingLat = document.getElementById('latitude')?.value;
        const existingLng = document.getElementById('longitude')?.value;
        
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
        
        // Update form fields
        const latField = document.getElementById('latitude');
        const lngField = document.getElementById('longitude');
        
        if (latField) {
            latField.value = lat.toFixed(8);
        }
        if (lngField) {
            lngField.value = lng.toFixed(8);
        }

        // Show toast
        showToast('Konum seçildi: ' + lat.toFixed(6) + ', ' + lng.toFixed(6));

        // Center map on marker
        currentMap.setCenter({ lat, lng });
    }

    function setCoordinates(lat, lng, name) {
        // Add marker to map
        addMarker({ lat, lng });
        
        // Center and zoom map
        if (currentMap) {
            currentMap.setCenter({ lat, lng });
            currentMap.setZoom(14);
        }
        
        // Fill form fields
        const titleInput = document.getElementById('title_location');
        const messageInput = document.getElementById('message_location');
        
        if (titleInput) titleInput.value = `${name} Bölgesi Bildirimi`;
        if (messageInput) messageInput.value = `${name} bölgesindeki kullanıcılara özel bildirim.`;
        
        showToast(`${name} koordinatları seçildi!`);
    }

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
        
        // Remove toast element after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            document.body.removeChild(toast);
        });
    }

    // Form validation
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            // Check if location form has coordinates
            if (form.id === 'locationForm') {
                if (!selectedLat || !selectedLng) {
                    e.preventDefault();
                    alert('Lütfen haritadan bir konum seçin.');
                    isValid = false;
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                if (isValid) {
                    alert('Lütfen tüm gerekli alanları doldurun.');
                }
            }
        });
    });

    // Initialize map when ready
    function tryInitMap() {
        initGoogleMap();
    }

    // Group selection handler
    document.getElementById('group').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const description = selectedOption.getAttribute('data-description');
        const count = selectedOption.getAttribute('data-count');
        
        const descriptionElement = document.getElementById('groupDescription');
        if (description && count) {
            descriptionElement.textContent = `${description} - ${count} kullanıcı`;
            descriptionElement.className = 'form-text text-info';
        } else {
            descriptionElement.textContent = '';
            descriptionElement.className = 'form-text';
        }
    });

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(tryInitMap, 100);
    });

    // Also try when window loads
    window.addEventListener('load', function() {
        if (!map) {
            setTimeout(tryInitMap, 100);
        }
    });

    // Fallback initialization
    setTimeout(function() {
        tryInitMap();
    }, 1000);

    // Make functions globally available
    window.setCoordinates = setCoordinates;
    window.showToast = showToast;
</script>
@endsection
