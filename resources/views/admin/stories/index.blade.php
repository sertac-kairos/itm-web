@extends('layouts.vertical', ['title' => 'Hikayeler Yönetimi - İzmir Time Machine', 'topbarTitle' => 'Hikayeler'])

@section('content')
<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active">Hikayeler</li>
                </ol>
            </div>
            <h4 class="page-title">Hikayeler Yönetimi</h4>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle me-1"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Actions Bar -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h4 class="mb-0">
                            <i class="mdi mdi-book-open-page-variant text-primary me-2"></i>
                            Tüm Hikayeler
                            <span class="badge badge bg-primary text-white ms-2">{{ $stories->total() }} Hikaye</span>
                        </h4>
                        <p class="text-muted mb-0">İzmir Time Machine uygulaması için tanımlı hikayeleri yönetin</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <button type="button" class="btn btn-warning me-2" onclick="testJavaScript()">
                            <i class="mdi mdi-test-tube me-1"></i>
                            JS Test
                        </button>
                        <a href="{{ route('admin.stories.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle me-1"></i>
                            Yeni Hikaye Ekle
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stories List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Search and Filter -->
                <form method="GET" action="{{ route('admin.stories.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Arama</label>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Başlık ara...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Durum</label>
                            <select class="form-select" name="status">
                                <option value="">Tümü</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Pasif</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="mdi mdi-filter-variant"></i> Filtrele
                            </button>
                            <a href="{{ route('admin.stories.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-refresh"></i> Temizle
                            </a>
                        </div>
                    </div>
                </form>

                @if($stories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="customCheck1">
                                            <label class="form-check-label" for="customCheck1">&nbsp;</label>
                                        </div>
                                    </th>
                                    <th>Hikaye</th>
                                    <th>Sıra</th>
                                    <th>Durum</th>
                                    <th>Oluşturulma</th>
                                    <th style="width: 125px;">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stories as $story)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="customCheck{{ $story->id }}">
                                                <label class="form-check-label" for="customCheck{{ $story->id }}">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $previewUrl = $story->translated_image_url ?? $story->thumbnail_url;
                                                @endphp
                                                @if($previewUrl)
                                                    <img src="{{ $previewUrl }}" alt="Önizleme" class="rounded me-3" height="48">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                        <i class="mdi mdi-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h5 class="mb-1">
                                                        <a href="{{ route('admin.stories.show', $story) }}" class="text-dark">
                                                            {{ $story->translate(app()->getLocale())->title ?? 'Başlıksız' }}
                                                        </a>
                                                    </h5>
                                                    <p class="mb-0 text-muted">
                                                        @if($story->translate(app()->getLocale())->description)
                                                            {{ Str::limit($story->translate(app()->getLocale())->description, 60) }}
                                                        @else
                                                            Açıklama yok
                                                        @endif
                                                    </p>
                                                    <div class="mt-1">
                                                        @foreach($story->translations as $translation)
                                                            <span class="badge badge-soft-primary me-1">{{ strtoupper($translation->locale) }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge bg-secondary text-white">{{ $story->sort_order }}</span>
                                        </td>
                                        <td>
                                            @if($story->is_active)
                                                <span class="badge bg-success text-white">Aktif</span>
                                            @else
                                                <span class="badge bg-danger text-white">Pasif</span>
                                            @endif
                                        </td>
                                        <td>{{ $story->created_at->diffForHumans() }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.stories.show', $story) }}" class="btn btn-soft-primary btn-sm" title="Görüntüle">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.stories.edit', $story) }}" class="btn btn-soft-success btn-sm" title="Düzenle">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-soft-danger btn-sm delete-story-btn" title="Sil" 
                                                        data-story-id="{{ $story->id }}" 
                                                        data-story-title="{{ $story->translate(app()->getLocale())->title ?? 'Hikaye' }}"
                                                        onclick="deleteStory('{{ $story->id }}', '{{ $story->translate(app()->getLocale())->title ?? 'Hikaye' }}')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info">
                                {{ $stories->firstItem() }}-{{ $stories->lastItem() }} arası, toplam {{ $stories->total() }} kayıt
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers">
                                {{ $stories->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-book-open-page-variant text-muted" style="font-size: 48px;"></i>
                        <h5 class="text-muted mt-2">Henüz hikaye eklenmemiş</h5>
                        <p class="text-muted">İlk hikayanizi eklemek için butona tıklayın.</p>
                        <a href="{{ route('admin.stories.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle me-1"></i> Hikaye Ekle
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Hikaye Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bu hikayeyi silmek istediğinizden emin misiniz?</p>
                <p class="text-muted"><strong id="storyTitleToDelete"></strong></p>
                <p class="text-danger"><small>Bu işlem geri alınamaz!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Evet, Sil</button>
            </div>
        </div>
    </div>
</div>

<!-- Inline Script for Delete Functionality -->
<script>
// Global variables
let currentDeleteId = null;
let currentDeleteTitle = null;

// Global functions - must be defined immediately
window.deleteStory = function(id, title) {
    console.log('deleteStory called with ID:', id, 'Title:', title);
    
    // Set current delete info
    currentDeleteId = id;
    currentDeleteTitle = title;
    
    // Update modal content
    document.getElementById('storyTitleToDelete').textContent = title;
    
    // Show modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
};

window.testJavaScript = function() {
    console.log('JavaScript is working!');
    alert('JavaScript çalışıyor! Console\'u kontrol edin.');
};

// Confirm delete function
function confirmDelete() {
    if (currentDeleteId) {
        console.log('User confirmed deletion for ID:', currentDeleteId);
        
        // Create form and submit
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.stories.destroy", ":id") }}'.replace(':id', currentDeleteId);
        form.style.display = 'none';
        
        console.log('Form action:', form.action);
        
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
        console.log('Submitting form...');
        form.submit();
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Confirm delete button
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        confirmDelete();
    });
    
    // Clear data when modal is hidden
    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function() {
        currentDeleteId = null;
        currentDeleteTitle = null;
    });
});

console.log('Inline script loaded');
console.log('deleteStory function defined:', typeof window.deleteStory);
</script>

@endsection


