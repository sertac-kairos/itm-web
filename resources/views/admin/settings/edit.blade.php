@extends('layouts.vertical', ['title' => 'Site Ayarları', 'topbarTitle' => 'Site Ayarları'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Site Ayarları</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Ayarlar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

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

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Genel</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="qr_enabled" name="qr_enabled" value="1"
                                    {{ old('qr_enabled', $settings['qr_enabled']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="qr_enabled">QR Kod Özelliği Aktif</label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="x_url">X Adresi</label>
                                <input type="url" class="form-control @error('x_url') is-invalid @enderror" id="x_url"
                                    name="x_url" value="{{ old('x_url', $settings['x_url']) }}"
                                    placeholder="https://x.com/...">
                                @error('x_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="linkedin_url">LinkedIn Adresi</label>
                                <input type="url" class="form-control @error('linkedin_url') is-invalid @enderror"
                                    id="linkedin_url" name="linkedin_url"
                                    value="{{ old('linkedin_url', $settings['linkedin_url']) }}"
                                    placeholder="https://linkedin.com/...">
                                @error('linkedin_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="instagram_url">Instagram Adresi</label>
                                <input type="url" class="form-control @error('instagram_url') is-invalid @enderror"
                                    id="instagram_url" name="instagram_url"
                                    value="{{ old('instagram_url', $settings['instagram_url']) }}"
                                    placeholder="https://instagram.com/...">
                                @error('instagram_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-0">
                                <label class="form-label" for="email_address">Mail Adresi</label>
                                <input type="email" class="form-control @error('email_address') is-invalid @enderror"
                                    id="email_address" name="email_address"
                                    value="{{ old('email_address', $settings['email_address']) }}"
                                    placeholder="info@domain.com">
                                @error('email_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Proje Hakkında</h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                @foreach($locales as $locale)
                                    <li class="nav-item">
                                        <a class="nav-link @if($loop->first) active @endif" data-bs-toggle="tab"
                                            href="#{{ $locale }}" role="tab">
                                            <span class="d-none d-sm-block">{{ strtoupper($locale) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content p-3 text-muted">
                                @foreach($locales as $locale)
                                    <div class="tab-pane @if($loop->first) active @endif" id="{{ $locale }}" role="tabpanel">
                                        <div class="mb-0">
                                            <label class="form-label" for="about_project_{{ $locale }}">Proje Hakkında
                                                ({{ strtoupper($locale) }})</label>
                                            <textarea class="form-control" id="about_project_{{ $locale }}"
                                                name="about_project[{{ $locale }}]"
                                                rows="6">{{ old('about_project.' . $locale, $aboutProject[$locale]) }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Timeline Görsel Ayarları</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="timeline_image_json">Timeline Görsel JSON</label>
                                <textarea class="form-control @error('timeline_image_json') is-invalid @enderror"
                                    id="timeline_image_json" name="timeline_image_json" rows="8"
                                    placeholder='{"images": [{"url": "path/to/image1.jpg", "title": "Başlık 1", "description": "Açıklama 1"}, {"url": "path/to/image2.jpg", "title": "Başlık 2", "description": "Açıklama 2"}]}'>{{ old('timeline_image_json', $settings['timeline_image_json'] ?? '') }}</textarea>
                                @error('timeline_image_json')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Timeline görsellerini JSON formatında girin. Geçerli JSON formatı:
                                    {"images": [{"url": "görsel_yolu", "title": "başlık", "description":
                                    "açıklama"}]}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Gizlilik Politikası</h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                @foreach($locales as $locale)
                                    <li class="nav-item">
                                        <a class="nav-link @if($loop->first) active @endif" data-bs-toggle="tab"
                                            href="#privacy_{{ $locale }}" role="tab">
                                            <span class="d-none d-sm-block">{{ strtoupper($locale) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content p-3 text-muted">
                                @foreach($locales as $locale)
                                    <div class="tab-pane @if($loop->first) active @endif" id="privacy_{{ $locale }}"
                                        role="tabpanel">
                                        <div class="mb-0">
                                            <label class="form-label" for="privacy_policy_{{ $locale }}">Gizlilik Politikası
                                                ({{ strtoupper($locale) }})</label>
                                            <textarea class="form-control" id="privacy_policy_{{ $locale }}"
                                                name="privacy_policy[{{ $locale }}]"
                                                rows="12">{{ old('privacy_policy.' . $locale, $privacyPolicy[$locale] ?? '') }}</textarea>
                                            <small class="text-muted">HTML etiketleri kullanabilirsiniz (&lt;h3&gt;, &lt;p&gt;,
                                                &lt;ul&gt;, &lt;li&gt;, &lt;a&gt; vb.)</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Kurum Logoları</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="izmir_kalkinma_ajansi_logo">İzmir Kalkınma Ajansı
                                            Logo</label>
                                        <input type="file"
                                            class="form-control @error('izmir_kalkinma_ajansi_logo') is-invalid @enderror"
                                            id="izmir_kalkinma_ajansi_logo" name="izmir_kalkinma_ajansi_logo"
                                            accept="image/*">
                                        @error('izmir_kalkinma_ajansi_logo')<div class="invalid-feedback">{{ $message }}
                                        </div>@enderror
                                        @if($settings['izmir_kalkinma_ajansi_logo'])
                                            <div class="mt-2">
                                                <small class="text-muted">Mevcut logo:</small><br>
                                                <img src="{{ Storage::url($settings['izmir_kalkinma_ajansi_logo']) }}"
                                                    alt="İzmir Kalkınma Ajansı" class="img-thumbnail"
                                                    style="max-height: 100px;">
                                            </div>
                                        @endif
                                        <small class="text-muted">Maksimum dosya boyutu: 1MB</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="sanayi_teknoloji_bakanligi_logo">Sanayi ve Teknoloji
                                            Bakanlığı Logo</label>
                                        <input type="file"
                                            class="form-control @error('sanayi_teknoloji_bakanligi_logo') is-invalid @enderror"
                                            id="sanayi_teknoloji_bakanligi_logo" name="sanayi_teknoloji_bakanligi_logo"
                                            accept="image/*">
                                        @error('sanayi_teknoloji_bakanligi_logo')<div class="invalid-feedback">
                                        {{ $message }}</div>@enderror
                                        @if($settings['sanayi_teknoloji_bakanligi_logo'])
                                            <div class="mt-2">
                                                <small class="text-muted">Mevcut logo:</small><br>
                                                <img src="{{ Storage::url($settings['sanayi_teknoloji_bakanligi_logo']) }}"
                                                    alt="Sanayi ve Teknoloji Bakanlığı" class="img-thumbnail"
                                                    style="max-height: 100px;">
                                            </div>
                                        @endif
                                        <small class="text-muted">Maksimum dosya boyutu: 1MB</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label" for="hafiza_izmir_logo">Hafıza İzmir Logo</label>
                                        <input type="file"
                                            class="form-control @error('hafiza_izmir_logo') is-invalid @enderror"
                                            id="hafiza_izmir_logo" name="hafiza_izmir_logo" accept="image/*">
                                        @error('hafiza_izmir_logo')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($settings['hafiza_izmir_logo'])
                                            <div class="mt-2">
                                                <small class="text-muted">Mevcut logo:</small><br>
                                                <img src="{{ Storage::url($settings['hafiza_izmir_logo']) }}" alt="Hafıza İzmir"
                                                    class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                        @endif
                                        <small class="text-muted">Maksimum dosya boyutu: 1MB</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Uygulama Ayarları</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h5 class="mb-3">Zaman Yolculuğu</h5>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="time_travel_slider_active"
                                            name="time_travel_slider_active" value="1" {{ old('time_travel_slider_active', $settings['time_travel_slider_active']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="time_travel_slider_active">Zaman Yolculuğu
                                            Slider Aktif</label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Zaman Yolculuğu Slider Bölgeleri</label>
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label text-muted small">Mevcut Bölgeler</label>
                                                <div id="available-regions" class="border rounded p-2"
                                                    style="min-height: 200px; max-height: 300px; overflow-y: auto;">
                                                    @php
                                                        $oldRegionIds = old('time_travel_slider_regions', $selectedRegionIds);
                                                        $oldRegionIds = is_array($oldRegionIds) ? array_map('intval', $oldRegionIds) : [];
                                                    @endphp
                                                    @foreach($regions as $region)
                                                        @if(!in_array((int) $region->id, $oldRegionIds))
                                                            <div class="region-item border rounded p-2 mb-1 bg-light"
                                                                data-id="{{ $region->id }}" style="cursor: grab;">
                                                                <i class="mdi mdi-drag-vertical me-1"></i>{{ $region->name }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label text-muted small">Seçilen Bölgeler (Sıralı)</label>
                                                <div id="selected-regions" class="border rounded p-2 border-primary"
                                                    style="min-height: 200px; max-height: 300px; overflow-y: auto;">
                                                    @foreach($oldRegionIds as $selectedId)
                                                        @php $selectedRegion = $regions->firstWhere('id', $selectedId); @endphp
                                                        @if($selectedRegion)
                                                            <div class="region-item border rounded p-2 mb-1 bg-success bg-opacity-10"
                                                                data-id="{{ $selectedRegion->id }}" style="cursor: grab;">
                                                                <i
                                                                    class="mdi mdi-drag-vertical me-1"></i>{{ $selectedRegion->name }}
                                                                <input type="hidden" name="time_travel_slider_regions[]"
                                                                    value="{{ $selectedRegion->id }}">
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">Bölgeleri sürükleyerek seçin ve sıralayın. Sağ taraftaki
                                            liste slider'da görünecek sırayı belirler.</small>
                                        @error('time_travel_slider_regions')<div class="invalid-feedback d-block">
                                        {{ $message }}</div>@enderror
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox"
                                            id="time_travel_hotspot_image_visible" name="time_travel_hotspot_image_visible"
                                            value="1" {{ old('time_travel_hotspot_image_visible', $settings['time_travel_hotspot_image_visible']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="time_travel_hotspot_image_visible">Zaman
                                            Yolculuğu Hotspot Image Görünüm</label>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h5 class="mb-3">İçerik Ayarları</h5>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="stories_active"
                                            name="stories_active" value="1" {{ old('stories_active', $settings['stories_active']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="stories_active">Hikayeler Aktif</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="featured_articles_active"
                                            name="featured_articles_active" value="1" {{ old('featured_articles_active', $settings['featured_articles_active']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="featured_articles_active">Öne Çıkan Yazılar
                                            Aktif</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="memory_izmir_active"
                                            name="memory_izmir_active" value="1" {{ old('memory_izmir_active', $settings['memory_izmir_active']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="memory_izmir_active">Hafıza İzmir Aktif</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="model_ar_experience_active"
                                            name="model_ar_experience_active" value="1" {{ old('model_ar_experience_active', $settings['model_ar_experience_active']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="model_ar_experience_active">Model AR
                                            Deneyimi</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <h5 class="mb-3">Yakınımdaki Ören Yerleri</h5>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox"
                                            id="nearby_archaeological_sites_active"
                                            name="nearby_archaeological_sites_active" value="1" {{ old('nearby_archaeological_sites_active', $settings['nearby_archaeological_sites_active']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="nearby_archaeological_sites_active">Yakınımdaki
                                            Ören Yerleri Aktif</label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="nearby_archaeological_sites_count">Yakınımdaki Ören
                                            Yerleri Sayısı</label>
                                        <input type="number"
                                            class="form-control @error('nearby_archaeological_sites_count') is-invalid @enderror"
                                            id="nearby_archaeological_sites_count" name="nearby_archaeological_sites_count"
                                            value="{{ old('nearby_archaeological_sites_count', $settings['nearby_archaeological_sites_count']) }}"
                                            min="1" max="50">
                                        @error('nearby_archaeological_sites_count')<div class="invalid-feedback">
                                        {{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h5 class="mb-3">Model Ayarları</h5>
                                    <div class="mb-3">
                                        <label class="form-label" for="model_background_color">Model Arka Plan Resmi
                                            (Renk)</label>
                                        <input type="color"
                                            class="form-control form-control-color @error('model_background_color') is-invalid @enderror"
                                            id="model_background_color" name="model_background_color"
                                            value="{{ old('model_background_color', $settings['model_background_color']) }}">
                                        @error('model_background_color')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Model arka plan rengini seçin</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="model_title_color">Model Başlığı Rengi</label>
                                        <input type="color"
                                            class="form-control form-control-color @error('model_title_color') is-invalid @enderror"
                                            id="model_title_color" name="model_title_color"
                                            value="{{ old('model_title_color', $settings['model_title_color']) }}">
                                        @error('model_title_color')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Model başlığı rengini seçin (Hex formatında)</small>
                                    </div>

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
                            <button type="submit" class="btn btn-info"><i class="mdi mdi-content-save me-1"></i>
                                Kaydet</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const availableRegions = document.getElementById('available-regions');
    const selectedRegions = document.getElementById('selected-regions');

    if (availableRegions && selectedRegions) {
        // Sortable for available regions
        new Sortable(availableRegions, {
            group: 'regions',
            animation: 150,
            ghostClass: 'bg-primary',
            onAdd: function(evt) {
                const item = evt.item;
                item.classList.remove('bg-success', 'bg-opacity-10');
                item.classList.add('bg-light');
                // Remove hidden input when moved to available
                const hiddenInput = item.querySelector('input[type="hidden"]');
                if (hiddenInput) hiddenInput.remove();
            }
        });

        // Sortable for selected regions
        new Sortable(selectedRegions, {
            group: 'regions',
            animation: 150,
            ghostClass: 'bg-primary',
            onAdd: function(evt) {
                const item = evt.item;
                item.classList.remove('bg-light');
                item.classList.add('bg-success', 'bg-opacity-10');
                // Add hidden input when moved to selected
                if (!item.querySelector('input[type="hidden"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'time_travel_slider_regions[]';
                    input.value = item.dataset.id;
                    item.appendChild(input);
                }
            },
            onSort: function(evt) {
                // Reorder hidden inputs to match visual order
                updateHiddenInputOrder();
            }
        });

        function updateHiddenInputOrder() {
            const items = selectedRegions.querySelectorAll('.region-item');
            items.forEach(function(item) {
                let input = item.querySelector('input[type="hidden"]');
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'time_travel_slider_regions[]';
                    input.value = item.dataset.id;
                    item.appendChild(input);
                }
            });
        }
    }
});
</script>
@endpush