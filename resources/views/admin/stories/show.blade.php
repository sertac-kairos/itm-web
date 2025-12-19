@extends('layouts.vertical', ['title' => 'Hikaye Detayı - İzmir Time Machine', 'topbarTitle' => 'Hikaye Detayı'])

@section('content')
<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.stories.index') }}">Hikayeler</a></li>
                    <li class="breadcrumb-item active">Hikaye Detayı</li>
                </ol>
            </div>
            <h4 class="page-title">Hikaye Detayı</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Genel Bilgiler -->
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Genel Bilgiler</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">ID</th>
                                <td>{{ $story->id }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Durum</th>
                                <td>
                                    @if($story->is_active)
                                        <span class="badge badge-success-lighten">Aktif</span>
                                    @else
                                        <span class="badge badge-danger-lighten">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Sıralama</th>
                                <td><span class="badge badge-secondary-lighten">{{ $story->sort_order }}</span></td>
                            </tr>
                            <tr>
                                <th scope="row">Oluşturulma</th>
                                <td>{{ $story->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Güncellenme</th>
                                <td>{{ $story->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Küçük Resim -->
        @if($story->thumbnail)
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Küçük Resim</h4>
            </div>
            <div class="card-body text-center">
                <img src="{{ $story->thumbnail_url }}" alt="Küçük Resim" 
                     class="img-fluid rounded" style="max-width: 100%;">
            </div>
        </div>
        @endif

        <!-- İşlem Butonları -->
        <div class="card">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.stories.edit', $story) }}" class="btn btn-primary">
                        <i class="mdi mdi-pencil me-1"></i>
                        Düzenle
                    </a>
                    <a href="{{ route('admin.stories.index') }}" class="btn btn-light">
                        <i class="mdi mdi-arrow-left me-1"></i>
                        Listeye Dön
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="mdi mdi-delete me-1"></i>
                        Sil
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Çok Dilli İçerik -->
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">Çok Dilli İçerik</h4>
                <p class="text-muted mb-0">Farklı dillerdeki içerik bilgileri</p>
            </div>
            <div class="card-body">
                <!-- Dil Sekmeleri -->
                <ul class="nav nav-tabs nav-bordered mb-3" role="tablist">
                    @foreach($story->translations as $index => $translation)
                        <li class="nav-item" role="presentation">
                            <a href="#{{ $translation->locale }}-content" data-bs-toggle="tab" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                               class="nav-link {{ $index === 0 ? 'active' : '' }}" role="tab">
                                <i class="mdi mdi-translate me-1"></i>
                                {{ strtoupper($translation->locale) }}
                                @if($translation->locale === 'tr') <span class="text-muted">(Varsayılan)</span> @endif
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Dil İçerikleri -->
                <div class="tab-content">
                    @foreach($story->translations as $index => $translation)
                        <div class="tab-pane {{ $index === 0 ? 'show active' : '' }}" id="{{ $translation->locale }}-content" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Başlık</label>
                                        <p class="form-control-plaintext">{{ $translation->title }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Açıklama</label>
                                        <p class="form-control-plaintext">{{ $translation->description ?: 'Açıklama yok' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if($translation->image)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Resim</label>
                                            <div class="text-center">
                                                <img src="{{ asset('storage/' . $translation->image) }}" 
                                                     alt="Resim - {{ $translation->locale }}" 
                                                     class="img-fluid rounded" style="max-width: 100%;">
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-5">
                                            <i class="mdi mdi-image" style="font-size: 48px;"></i>
                                            <p class="mt-2">Resim yok</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function confirmDelete() {
        if (confirm('Bu hikayeyi silmek istediğinizden emin misiniz?\n\n"{{ $story->translate(app()->getLocale())->title ?? 'Hikaye' }}"')) {
            // Create form and submit
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.stories.destroy", $story) }}';
            
            // CSRF token
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            // Method override
            var methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection
