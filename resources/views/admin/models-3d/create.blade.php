@extends('layouts.vertical', ['title' => 'Yeni 3D Model Ekle - İzmir Time Machine', 'topbarTitle' => '3D Model Ekleme'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Yeni 3D Model Ekle</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.models-3d.index') }}">3D Modeller</a></li>
                        <li class="breadcrumb-item active">Yeni Ekle</li>
                    </ol>
                </div>
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

    <form action="{{ route('admin.models-3d.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <!-- Ana Bilgiler -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">3D Model Bilgileri</h4>
                    </div>
                    <div class="card-body">
                        <!-- Çoklu Dil Sekmesi -->
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            @foreach($locales as $locale)
                                <li class="nav-item">
                                    <a class="nav-link @if($loop->first) active @endif" 
                                       data-bs-toggle="tab" href="#{{ $locale }}" role="tab">
                                        <span class="d-block d-sm-none">
                                            @switch($locale)
                                                @case('tr') 🇹🇷 @break
                                                @case('en') 🇬🇧 @break
                                                @case('ar') 🇸🇦 @break
                                                @case('de') 🇩🇪 @break
                                            @endswitch
                                        </span>
                                        <span class="d-none d-sm-block">
                                            @switch($locale)
                                                @case('tr') Türkçe @break
                                                @case('en') English @break
                                                @case('ar') العربية @break
                                                @case('de') Deutsch @break
                                            @endswitch
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content p-3 text-muted">
                            @foreach($locales as $locale)
                                <div class="tab-pane @if($loop->first) active @endif" id="{{ $locale }}" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="{{ $locale }}_name">
                                                    Model Adı
                                                    @if($locale === 'tr') <span class="text-danger">*</span> @endif
                                                </label>
                                                <input type="text" 
                                                       class="form-control @error($locale.'.name') is-invalid @enderror"
                                                       id="{{ $locale }}_name" 
                                                       name="{{ $locale }}[name]"
                                                       value="{{ old($locale.'.name') }}"
                                                       placeholder="3D model adını girin">
                                                @error($locale.'.name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="{{ $locale }}_description">Model Açıklaması</label>
                                                <textarea class="form-control @error($locale.'.description') is-invalid @enderror"
                                                          id="{{ $locale }}_description" 
                                                          name="{{ $locale }}[description]"
                                                          rows="4"
                                                          placeholder="Model açıklamasını girin">{{ old($locale.'.description') }}</textarea>
                                                @error($locale.'.description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-info">
                            <i class="mdi mdi-information me-2"></i>
                            En az bir dilde model adı girilmesi yeterlidir.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yan Panel -->
            <div class="col-lg-4">
                <!-- Ören Yeri Seçimi -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Ören Yeri Seçimi</h4>
                    </div>
                    <div class="card-body">
                        <!-- Bölge Seçimi -->
                        <div class="mb-3">
                            <label class="form-label" for="region_id">Bölge <span class="text-danger">*</span></label>
                            <select class="form-select" id="region_id">
                                <option value="">Bölge Seçin</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" 
                                            {{ old('region_id', $selectedRegionId ?? '') == $region->id ? 'selected' : '' }}
                                            data-color="{{ $region->color_code }}">
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Alt Bölge Seçimi -->
                        <div class="mb-3">
                            <label class="form-label" for="sub_region_id">Alt Bölge <span class="text-danger">*</span></label>
                            <select class="form-select" id="sub_region_id">
                                <option value="">Önce Bölge Seçin</option>
                            </select>
                        </div>
                        
                        <!-- Ören Yeri Seçimi -->
                        <div class="mb-3">
                            <label class="form-label" for="archaeological_site_id">
                                Ören Yeri <span class="text-danger">*</span>
                                <span id="edit-site-link" style="display: none;">
                                    <a href="#" id="edit-site-btn" class="btn btn-sm btn-outline-primary ms-2" title="Ören Yerini Düzenle" target="_blank">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                </span>
                            </label>
                            <select class="form-select @error('archaeological_site_id') is-invalid @enderror" 
                                    id="archaeological_site_id" 
                                    name="archaeological_site_id" required>
                                <option value="">Önce Alt Bölge Seçin</option>
                            </select>
                            @error('archaeological_site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Sketchfab Bilgileri -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Sketchfab Model Bilgileri</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="sketchfab_model_id">Sketchfab Model ID <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('sketchfab_model_id') is-invalid @enderror" 
                                   id="sketchfab_model_id" 
                                   name="sketchfab_model_id" 
                                   value="{{ old('sketchfab_model_id') }}"
                                   placeholder="örn: 3d15db8ec99744e5ba91dfcad10e1d4b"
                                   required>
                            <div class="form-text">
                                Sketchfab model URL'sinden alınan model ID'si
                                <br><small>URL: https://sketchfab.com/3d-models/model-name-<strong>ID</strong></small>
                            </div>
                            @error('sketchfab_model_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="sketchfab_thumbnail_url">Thumbnail URL (İsteğe Bağlı)</label>
                            <input type="url" 
                                   class="form-control @error('sketchfab_thumbnail_url') is-invalid @enderror" 
                                   id="sketchfab_thumbnail_url" 
                                   name="sketchfab_thumbnail_url" 
                                   value="{{ old('sketchfab_thumbnail_url') }}"
                                   placeholder="https://media.sketchfab.com/models/...">
                            <div class="form-text">
                                Model önizleme resmi URL'si (boş bırakılabilir)
                            </div>
                            @error('sketchfab_thumbnail_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Ayarlar -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Ayarlar</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="sort_order">Sıra <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" 
                                   name="sort_order" 
                                   value="{{ old('sort_order', 0) }}" 
                                   min="0" 
                                   required>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Butonları -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.models-3d.index') }}" class="btn btn-light">
                                <i class="mdi mdi-arrow-left me-1"></i> İptal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check me-1"></i> Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hierarchical dropdown data
        const regionData = @json($regionData);
        const regionSelect = document.getElementById('region_id');
        const subRegionSelect = document.getElementById('sub_region_id');
        const archaeologicalSiteSelect = document.getElementById('archaeological_site_id');
        const editSiteLink = document.getElementById('edit-site-link');
        const editSiteBtn = document.getElementById('edit-site-btn');
        
        // Current selected values from query parameters or old input
        const currentSiteId = {{ old('archaeological_site_id', $selectedArchaeologicalSiteId ?? 0) }};
        const currentSubRegionId = {{ old('sub_region_id', $selectedSubRegionId ?? 0) }};
        const currentRegionId = {{ old('region_id', $selectedRegionId ?? 0) }};

        // Update sub-regions when region changes
        regionSelect.addEventListener('change', function() {
            const regionId = parseInt(this.value);
            const selectedRegion = regionData.find(r => r.id === regionId);
            
            // Clear sub-region and archaeological site options
            subRegionSelect.innerHTML = '<option value="">Alt Bölge Seçin</option>';
            archaeologicalSiteSelect.innerHTML = '<option value="">Ören Yeri Seçin</option>';
            editSiteLink.style.display = 'none';
            
            if (selectedRegion && selectedRegion.subRegions && selectedRegion.subRegions.length > 0) {
                // Add sub-region options
                selectedRegion.subRegions.forEach(subRegion => {
                    const option = document.createElement('option');
                    option.value = subRegion.id;
                    option.textContent = subRegion.name;
                    option.dataset.sites = JSON.stringify(subRegion.archaeologicalSites || []);
                    
                    // Select if this is the current sub-region
                    if (currentSubRegionId == subRegion.id) {
                        option.selected = true;
                    }
                    
                    subRegionSelect.appendChild(option);
                });
                
                // Trigger sub-region change if we have a selected sub-region
                if (currentSubRegionId && regionId == currentRegionId) {
                    subRegionSelect.dispatchEvent(new Event('change'));
                }
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Bu bölgeye ait aktif alt bölge yok';
                option.disabled = true;
                subRegionSelect.appendChild(option);
            }
        });

        // Update archaeological sites when sub-region changes
        subRegionSelect.addEventListener('change', function() {
            const subRegionId = parseInt(this.value);
            const selectedOption = this.options[this.selectedIndex];
            const sites = selectedOption.dataset.sites ? JSON.parse(selectedOption.dataset.sites) : [];
            
            // Clear archaeological site options
            archaeologicalSiteSelect.innerHTML = '<option value="">Ören Yeri Seçin</option>';
            editSiteLink.style.display = 'none';
            
            if (sites && sites.length > 0) {
                // Add archaeological site options
                sites.forEach(site => {
                    const option = document.createElement('option');
                    option.value = site.id;
                    option.textContent = site.name;
                    
                    // Select if this is the current site
                    if (currentSiteId == site.id) {
                        option.selected = true;
                        updateEditLink(site.id);
                    }
                    
                    archaeologicalSiteSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Bu alt bölgeye ait aktif ören yeri yok';
                option.disabled = true;
                archaeologicalSiteSelect.appendChild(option);
            }
        });

        // Update edit link when archaeological site changes
        archaeologicalSiteSelect.addEventListener('change', function() {
            const siteId = parseInt(this.value);
            if (siteId) {
                updateEditLink(siteId);
            } else {
                editSiteLink.style.display = 'none';
            }
        });

        // Update edit link function
        function updateEditLink(siteId) {
            if (siteId) {
                // Build edit URL using route pattern
                const baseUrl = '{{ url("admin/archaeological-sites") }}';
                editSiteBtn.href = baseUrl + '/' + siteId + '/edit';
                editSiteLink.style.display = 'inline-block';
            } else {
                editSiteLink.style.display = 'none';
            }
        }

        // Initialize on page load - trigger region change if we have current values
        if (currentRegionId) {
            regionSelect.value = currentRegionId;
            regionSelect.dispatchEvent(new Event('change'));
        }

        // Sketchfab Model ID validation
        const modelIdInput = document.getElementById('sketchfab_model_id');
        
        modelIdInput.addEventListener('input', function() {
            const value = this.value.trim();
            if (value && value.length !== 32) {
                this.setCustomValidity('Sketchfab Model ID 32 karakter uzunluğunda olmalıdır');
            } else {
                this.setCustomValidity('');
            }
        });

        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const locales = @json($locales);
            let hasAtLeastOneName = false;

            locales.forEach(function(locale) {
                const nameField = document.getElementById(locale + '_name');
                if (nameField && nameField.value.trim() !== '') {
                    hasAtLeastOneName = true;
                }
            });

            if (!hasAtLeastOneName) {
                e.preventDefault();
                alert('En az bir dilde model adı girilmelidir.');
                // Focus on Turkish name field
                document.getElementById('tr_name').focus();
                return false;
            }
            
            // Validate hierarchical selection
            if (!archaeologicalSiteSelect.value) {
                e.preventDefault();
                alert('Lütfen bir ören yeri seçin.');
                archaeologicalSiteSelect.focus();
                return false;
            }
        });
    });
</script>
@endsection
