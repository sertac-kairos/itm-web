@extends('layouts.vertical', ['title' => '3D Model Düzenle - İzmir Time Machine', 'topbarTitle' => '3D Model Düzenleme'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">3D Model Düzenle</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.models-3d.index') }}">3D Modeller</a></li>
                        <li class="breadcrumb-item active">Düzenle</li>
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

    <form action="{{ route('admin.models-3d.update', $model3d) }}" method="POST" enctype="multipart/form-data">
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
                                                       value="{{ old($locale.'.name', $model3d->translate($locale)->name ?? '') }}"
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
                                                          placeholder="Model açıklamasını girin">{{ old($locale.'.description', $model3d->translate($locale)->description ?? '') }}</textarea>
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
                        <h4 class="card-title mb-0">Ören Yeri</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="archaeological_site_id">Ören Yeri <span class="text-danger">*</span></label>
                            <select class="form-select @error('archaeological_site_id') is-invalid @enderror" 
                                    id="archaeological_site_id" 
                                    name="archaeological_site_id" required>
                                <option value="">Ören Yeri Seçin</option>
                                @foreach($archaeologicalSites as $site)
                                    <option value="{{ $site->id }}" 
                                            {{ old('archaeological_site_id', $model3d->archaeological_site_id) == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
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
                                   value="{{ old('sketchfab_model_id', $model3d->sketchfab_model_id) }}"
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
                                   value="{{ old('sketchfab_thumbnail_url', $model3d->sketchfab_thumbnail_url) }}"
                                   placeholder="https://media.sketchfab.com/models/...">
                            <div class="form-text">
                                Model önizleme resmi URL'si (boş bırakılabilir)
                            </div>
                            @error('sketchfab_thumbnail_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($model3d->sketchfab_thumbnail_url)
                            <div class="mb-3">
                                <label class="form-label">Mevcut Thumbnail</label>
                                <div>
                                    <img src="{{ $model3d->sketchfab_thumbnail_url }}" 
                                         alt="Thumbnail" 
                                         class="img-thumbnail"
                                         style="max-width: 200px;">
                                </div>
                            </div>
                        @endif
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
                                   value="{{ old('sort_order', $model3d->sort_order) }}" 
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
                                       {{ old('is_active', $model3d->is_active) ? 'checked' : '' }}>
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
                                <i class="mdi mdi-check me-1"></i> Güncelle
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
        });
    });
</script>
@endsection
