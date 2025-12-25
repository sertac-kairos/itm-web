@extends('layouts.vertical', ['title' => 'Destek Talepleri - İzmir Time Machine', 'topbarTitle' => 'Destek Talepleri'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Destek Talepleri</li>
                    </ol>
                </div>
                <h4 class="page-title">Destek Talepleri</h4>
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

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Arama</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="İsim, e-posta, telefon veya mesaj...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Durum</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tümü</option>
                                @foreach(['open' => 'Açık', 'in_progress' => 'Devam Ediyor', 'resolved' => 'Çözüldü', 'closed' => 'Kapalı'] as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="mdi mdi-magnify me-1"></i> Ara
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <a href="{{ route('admin.support-requests.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            ID
                                            @if(request('sort') === 'id')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.support-requests.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            İsim
                                            @if(request('sort') === 'name')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.support-requests.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'email', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            İletişim
                                            @if(request('sort') === 'email')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Cihaz</th>
                                    <th>Mesaj</th>
                                    <th>
                                        <a href="{{ route('admin.support-requests.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'status', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Durum
                                            @if(request('sort') === 'status')
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('admin.support-requests.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                            Tarih
                                            @if(request('sort') === 'created_at' || !request('sort'))
                                                <i class="mdi mdi-chevron-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                            @else
                                                <i class="mdi mdi-chevron-up opacity-25"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $req)
                                    <tr>
                                        <td><span class="badge badge-soft-secondary">#{{ $req->id }}</span></td>
                                        <td>{{ $req->name ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $req->email ?? '-' }}</span>
                                                <small class="text-muted">{{ $req->phone ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $req->device_id ?? '-' }}</td>
                                        <td>{{ Str::limit($req->message, 80) }}</td>
                                        <td>
                                            @php $statusMap = ['open' => 'Açık', 'in_progress' => 'Devam', 'resolved' => 'Çözüldü', 'closed' => 'Kapalı']; @endphp
                                            <span class="badge text-white {{ $req->status === 'open' ? 'bg-danger' : ($req->status === 'in_progress' ? 'bg-warning' : 'bg-success') }}">{{ $statusMap[$req->status] ?? $req->status }}</span>
                                        </td>
                                        <td>{{ $req->created_at?->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('admin.support-requests.show', $req) }}" class="btn btn-soft-primary btn-sm" title="Görüntüle">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-lifebuoy" style="font-size: 3rem;"></i>
                                                <h5 class="mt-2">Henüz destek talebi yok</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($requests->hasPages())
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="text-muted mb-0">
                                            {{ $requests->total() }} kayıttan {{ $requests->firstItem() }}-{{ $requests->lastItem() }} arası gösteriliyor
                                        </p>
                                    </div>
                                    <div>
                                        {{ $requests->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


