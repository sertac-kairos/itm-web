@extends('layouts.vertical', ['title' => 'Destek Talebi Detayı - İzmir Time Machine', 'topbarTitle' => 'Destek Talebi'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.support-requests.index') }}">Destek Talepleri</a></li>
                        <li class="breadcrumb-item active">#{{ $supportRequest->id }}</li>
                    </ol>
                </div>
                <h4 class="page-title">Destek Talebi #{{ $supportRequest->id }}</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Mesaj</h4>
                    <p class="mt-3">{{ $supportRequest->message }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Bilgiler</h4>
                    <div class="mt-3">
                        <p class="mb-1"><strong>İsim:</strong> {{ $supportRequest->name ?? '-' }}</p>
                        <p class="mb-1"><strong>E-posta:</strong> {{ $supportRequest->email ?? '-' }}</p>
                        <p class="mb-1"><strong>Telefon:</strong> {{ $supportRequest->phone ?? '-' }}</p>
                        <p class="mb-1"><strong>Cihaz ID:</strong> {{ $supportRequest->device_id ?? '-' }}</p>
                        <p class="mb-1"><strong>Oluşturulma:</strong> {{ $supportRequest->created_at?->format('d.m.Y H:i') }}</p>
                        <p class="mb-1"><strong>Durum:</strong>
                            <span class="badge text-white {{ $supportRequest->status === 'open' ? 'bg-danger' : ($supportRequest->status === 'in_progress' ? 'bg-warning' : 'bg-success') }}">{{ $supportRequest->status }}</span>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.support-requests.update', $supportRequest) }}" class="mt-3">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="status" class="form-label">Durumu Güncelle</label>
                            <select name="status" id="status" class="form-select">
                                @foreach(['open' => 'Açık', 'in_progress' => 'Devam Ediyor', 'resolved' => 'Çözüldü', 'closed' => 'Kapalı'] as $key => $label)
                                    <option value="{{ $key }}" {{ $supportRequest->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Kaydet</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


