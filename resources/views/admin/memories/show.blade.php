@extends('layouts.vertical', ['title' => 'Hafıza Detayı - İzmir Time Machine', 'topbarTitle' => 'Hafıza Detayı'])

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.memories.index') }}">Hafıza İzmir</a></li>
                        <li class="breadcrumb-item active">Hafıza Detayı</li>
                    </ol>
                </div>
                <h4 class="page-title">Hafıza Detayı: {{ $memory->translate(app()->getLocale())->title ?? 'Başlıksız' }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- General Information -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Genel Bilgiler</h4>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="fw-medium">ID:</td>
                            <td><span class="badge badge-soft-secondary">#{{ $memory->id }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Link:</td>
                            <td>
                                @if($memory->hasLink())
                                    <a href="{{ $memory->formatted_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="mdi mdi-link me-1"></i>
                                        Linki Aç
                                    </a>
                                @else
                                    <span class="text-muted">Link yok</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Sıralama:</td>
                            <td>{{ $memory->sort_order }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Durum:</td>
                            <td>
                                @if($memory->is_active)
                                    <span class="badge badge-success-lighten">
                                        <i class="mdi mdi-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge badge-danger-lighten">
                                        <i class="mdi mdi-close-circle me-1"></i>Pasif
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Oluşturulma:</td>
                            <td>{{ $memory->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Güncellenme:</td>
                            <td>{{ $memory->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Image -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Resim</h4>
                </div>
                <div class="card-body text-center">
                    @if($memory->image)
                        <img src="{{ $memory->image_url }}" alt="Resim" 
                             class="img-fluid rounded" style="max-height: 200px;">
                    @else
                        <div class="text-muted py-4">
                            <i class="mdi mdi-image" style="font-size: 3rem;"></i>
                            <p class="mb-0 mt-2">Resim yok</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.memories.edit', $memory) }}" class="btn btn-warning">
                            <i class="mdi mdi-pencil me-1"></i>
                            Düzenle
                        </a>
                        <a href="{{ route('admin.memories.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Geri Dön
                        </a>
                        <button type="button" class="btn btn-danger" 
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="mdi mdi-delete me-1"></i>
                            Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Multilingual Content -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Çok Dilli İçerik</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-bordered">
                        @foreach($memory->translations as $translation)
                            <li class="nav-item">
                                <a href="#{{ $translation->locale }}" data-bs-toggle="tab" 
                                   aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                   class="nav-link {{ $loop->first ? 'active' : '' }}">
                                    <span class="d-none d-sm-block">{{ strtoupper($translation->locale) }}</span>
                                    <span class="d-block d-sm-none">{{ strtoupper($translation->locale) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content">
                        @foreach($memory->translations as $translation)
                            <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $translation->locale }}">
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Başlık</label>
                                            <div class="form-control-plaintext">
                                                {{ $translation->title ?? 'Başlık yok' }}
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-medium">İçerik</label>
                                            <div class="form-control-plaintext" style="min-height: 200px; white-space: pre-wrap;">
                                                {{ $translation->content ?? 'İçerik yok' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="mdi mdi-alert-circle-outline text-warning me-2"></i>
                        Silme Onayı
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="mdi mdi-trash-can-outline text-danger" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 mb-2">Hafızayı Silmek İstediğinizden Emin misiniz?</h5>
                        <p class="text-muted mb-3">
                            <strong>"{{ $memory->translate(app()->getLocale())->title ?? 'Hafıza' }}"</strong> kalıcı olarak silinecek.<br>
                            Bu işlem geri alınamaz.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>İptal
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="mdi mdi-delete me-1"></i>Evet, Sil
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // CSRF token'ı JavaScript değişkenine al
    const csrfToken = '{{ csrf_token() }}';
    const memoryId = '{{ $memory->id }}';

    // Silme onayı
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/memories/${memoryId}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        });
    });
</script>
@endpush
