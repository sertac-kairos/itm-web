@extends('layouts.vertical', ['title' => '3D Model Detayları - İzmir Time Machine', 'topbarTitle' => '3D Model Görüntüleme'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">3D Model Detayları</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.models-3d.index') }}">3D Modeller</a></li>
                        <li class="breadcrumb-item active">{{ $model3d->name ?: 'İsimsiz Model' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Sketchfab Model Embed -->
            @if($model3d->sketchfab_model_id)
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">3D Model (Sketchfab)</h4>
                    </div>
                    <div class="card-body">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://sketchfab.com/models/{{ $model3d->sketchfab_model_id }}/embed?autostart=0&ui_controls=1&ui_infos=1&ui_inspector=1&ui_stop=1&ui_watermark=1" 
                                    title="{{ $model3d->name }}" 
                                    frameborder="0" 
                                    allow="autoplay; fullscreen; vr" 
                                    mozallowfullscreen="true" 
                                    webkitallowfullscreen="true">
                            </iframe>
                        </div>
                        <div class="mt-2 text-center">
                            <a href="https://sketchfab.com/3d-models/{{ $model3d->sketchfab_model_id }}" 
                               target="_blank" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="mdi mdi-open-in-new me-1"></i> Sketchfab'da Aç
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Model Önizleme -->
            @if($model3d->sketchfab_thumbnail_url)
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Model Önizleme</h4>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ $model3d->sketchfab_thumbnail_url }}" 
                             alt="{{ $model3d->name }}" 
                             class="img-fluid rounded"
                             style="max-height: 400px;">
                    </div>
                </div>
            @endif

            <!-- Çoklu Dil İçerikleri -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Model Bilgileri</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                        @foreach($locales as $locale)
                            @php
                                $translation = $model3d->translate($locale);
                                $hasContent = $translation && ($translation->name || $translation->description);
                            @endphp
                            @if($hasContent)
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
                            @endif
                        @endforeach
                    </ul>

                    <div class="tab-content p-3 text-muted">
                        @foreach($locales as $locale)
                            @php
                                $translation = $model3d->translate($locale);
                                $hasContent = $translation && ($translation->name || $translation->description);
                            @endphp
                            @if($hasContent)
                                <div class="tab-pane @if($loop->first) active @endif" id="{{ $locale }}" role="tabpanel">
                                    @if($translation->name)
                                        <div class="mb-3">
                                            <h5 class="text-primary">{{ $translation->name }}</h5>
                                        </div>
                                    @endif
                                    
                                    @if($translation->description)
                                        <div class="mb-3">
                                            <h6 class="text-muted mb-2">Açıklama:</h6>
                                            <p class="mb-0">{{ $translation->description }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sketchfab Bilgileri -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Sketchfab Model Bilgileri</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                    <i class="mdi mdi-cube-outline"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Model ID: {{ $model3d->sketchfab_model_id }}</h6>
                            <p class="text-muted mb-0">
                                3D model Sketchfab platformunda barındırılıyor
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="https://sketchfab.com/3d-models/{{ $model3d->sketchfab_model_id }}" 
                               target="_blank" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="mdi mdi-open-in-new me-1"></i> Sketchfab'da Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Genel Bilgiler -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Genel Bilgiler</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="ps-0" scope="row">Ören Yeri:</th>
                                    <td class="text-muted">
                                        @if($model3d->archaeologicalSite)
                                            <a href="{{ route('admin.archaeological-sites.show', $model3d->archaeologicalSite) }}" 
                                               class="text-decoration-none">
                                                {{ $model3d->archaeologicalSite->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Sıra:</th>
                                    <td class="text-muted">{{ $model3d->sort_order }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Durum:</th>
                                    <td class="text-muted">
                                        @if($model3d->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Pasif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Oluşturulma:</th>
                                    <td class="text-muted">{{ $model3d->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Güncellenme:</th>
                                    <td class="text-muted">{{ $model3d->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- İşlemler -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">İşlemler</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.models-3d.edit', $model3d) }}" class="btn btn-warning">
                            <i class="mdi mdi-pencil me-1"></i> Düzenle
                        </a>
                        <form action="{{ route('admin.models-3d.generate-qr', $model3d) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="mdi mdi-qrcode me-1"></i> QR Oluştur
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.models-3d.destroy', $model3d) }}" 
                              method="POST" 
                              onsubmit="return confirm('Bu 3D modeli silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="mdi mdi-delete me-1"></i> Sil
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.models-3d.index') }}" class="btn btn-light">
                            <i class="mdi mdi-arrow-left me-1"></i> Listeye Dön
                        </a>

                        @if($model3d->archaeologicalSite)
                            <a href="{{ route('admin.archaeological-sites.show', $model3d->archaeologicalSite) }}" 
                               class="btn btn-info">
                                <i class="mdi mdi-eye me-1"></i> Ören Yerini Gör
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sketchfab Model Detayları -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Model Detayları</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Sketchfab Model ID:</h6>
                        <p class="mb-1 font-monospace">{{ $model3d->sketchfab_model_id }}</p>
                        <small class="text-muted">32 karakter uzunluğunda unique ID</small>
                    </div>

                    @if($model3d->sketchfab_thumbnail_url)
                        <div class="mb-3">
                            <h6 class="text-muted mb-1">Thumbnail URL:</h6>
                            <p class="mb-1">
                                <a href="{{ $model3d->sketchfab_thumbnail_url }}" 
                                   target="_blank" 
                                   class="text-decoration-none">
                                    {{ Str::limit($model3d->sketchfab_thumbnail_url, 50) }}
                                </a>
                            </p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Embed URL:</h6>
                        <p class="mb-1">
                            <small class="font-monospace text-muted">
                                https://sketchfab.com/models/{{ $model3d->sketchfab_model_id }}/embed
                            </small>
                        </p>
                    </div>
                </div>
            </div>

            <!-- QR Bilgileri -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">QR Kod</h4>
                    <form action="{{ route('admin.models-3d.generate-qr', $model3d) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="mdi mdi-qrcode me-1"></i> QR Oluştur / Yenile
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    @if($model3d->qr_image_path)
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $model3d->qr_image_path) }}" alt="QR Code" class="img-fluid" style="max-width: 240px;">
                            <div class="mt-2">
                                <code class="small">UUID: {{ $model3d->qr_uuid }}</code>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">QR kod henüz oluşturulmamış.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
