@extends('layouts.vertical', ['title' => 'Onboarding Slayt Detayı', 'topbarTitle' => 'Onboarding'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Slayt Detayı</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.onboarding-slides.index') }}">Onboarding</a></li>
                        <li class="breadcrumb-item active">Detay</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Arkaplan Görseli</h4></div>
                <div class="card-body text-center">
                    @if($onboardingSlide->image)
                        <img src="{{ asset('storage/' . $onboardingSlide->image) }}" class="img-fluid rounded" style="max-height: 520px;">
                    @else
                        <div class="bg-light rounded" style="width:100%;height:520px;"></div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Çeviriler</h4></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Dil</th>
                                    <th>Başlık</th>
                                    <th>Açıklama</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($locales as $locale)
                                    @php $tr = $onboardingSlide->translate($locale, false); @endphp
                                    <tr>
                                        <td><span class="badge bg-info">{{ strtoupper($locale) }}</span></td>
                                        <td>{{ $tr?->title ?: '-' }}</td>
                                        <td>{{ $tr?->description ? \Illuminate\Support\Str::limit($tr->description, 120) : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Genel Bilgiler</h4></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr><th class="ps-0">Sıra:</th><td class="text-muted">{{ $onboardingSlide->sort_order }}</td></tr>
                                <tr><th class="ps-0">Durum:</th><td class="text-muted">@if($onboardingSlide->is_active)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-danger">Pasif</span>@endif</td></tr>
                                <tr><th class="ps-0">Oluşturulma:</th><td class="text-muted">{{ $onboardingSlide->created_at->format('d.m.Y H:i') }}</td></tr>
                                <tr><th class="ps-0">Güncellenme:</th><td class="text-muted">{{ $onboardingSlide->updated_at->format('d.m.Y H:i') }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('admin.onboarding-slides.edit', $onboardingSlide) }}" class="btn btn-warning"><i class="mdi mdi-pencil me-1"></i> Düzenle</a>
                        <a href="{{ route('admin.onboarding-slides.index') }}" class="btn btn-light"><i class="mdi mdi-arrow-left me-1"></i> Listeye Dön</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



