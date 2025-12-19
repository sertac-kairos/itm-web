<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Destek Talebi - İzmir Time Machine</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root { --bg:#0f172a; --card:#111827; --muted:#94a3b8; --text:#e2e8f0; --primary:#2563eb; --primary-700:#1d4ed8; --success:#16a34a; --danger:#dc2626; }
        *{box-sizing:border-box}
        html,body{height:100%}
        body{margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif; background:linear-gradient(135deg,#0b1220,#111827); color:var(--text);}
        .container{max-width:960px; margin:0 auto; padding:32px 16px;}
        .logo{display:flex; align-items:center; gap:12px; text-decoration:none; color:var(--text); margin-bottom:16px}
        .logo img{height:32px; width:auto}
        .header{margin:24px 0 16px}
        .title{margin:0; font-size:28px}
        .subtitle{margin:6px 0 0; color:var(--muted)}
        .grid{display:grid; grid-template-columns:1fr; gap:16px}
        @media (min-width: 900px){ .grid{grid-template-columns: 2fr 1fr} }
        .card{background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,0.35)}
        .card-body{padding:20px}
        label{display:block; margin:0 0 6px; color:var(--muted); font-size:14px}
        input,textarea{width:100%; padding:12px 14px; border-radius:10px; border:1px solid rgba(255,255,255,0.12); background:rgba(255,255,255,0.03); color:var(--text); outline:none;}
        input:focus,textarea:focus{border-color:var(--primary)}
        .row{display:grid; grid-template-columns:1fr; gap:14px}
        @media (min-width:700px){ .row-2{grid-template-columns:1fr 1fr} }
        .btn{display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 16px; border-radius:10px; border:0; cursor:pointer; color:#fff; background:linear-gradient(135deg,var(--primary),var(--primary-700));}
        .btn:active{transform:translateY(1px)}
        .alert{padding:12px 14px; border-radius:10px; margin:8px 0 16px; font-size:14px}
        .alert-success{background:rgba(22,163,74,0.12); border:1px solid rgba(22,163,74,0.35); color:#bbf7d0}
        .invalid{border-color:var(--danger) !important}
        .invalid-feedback{color:#fecaca; font-size:12px; margin-top:6px}
        .muted{color:var(--muted)}
        .contact-blurb{font-size:14px; line-height:1.5}
        .footer{margin-top:28px; text-align:center; color:var(--muted); font-size:13px}
        a{text-decoration:none; color:#93c5fd}
    </style>
    <script>
        // Close button for alerts (no dependency)
        document.addEventListener('click', function(e){
            if(e.target.matches('[data-close]')){
                const p = e.target.closest('.alert'); if(p) p.remove();
            }
        });
    </script>
    <!-- Optional: Prefetch app logo for nicer look -->
    <link rel="preload" as="image" href="{{ asset('applogo.png') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#0f172a">
    <meta name="color-scheme" content="dark">
    <meta name="description" content="İzmir Time Machine destek talep formu">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    <meta name="referrer" content="no-referrer-when-downgrade">
    <meta name="x-csp" content="inline-css-only">
    <script>/* keep head minimal */</script>
    <!--
        Standalone, layout-free support page.
        Uses lightweight inline CSS and no sidebar or shared layout includes.
    -->
    <!--
        NOTE: Bu sayfa admin layout'u kullanmaz. Tamamen bağımsız HTML/CSS.
    -->
    <!--
        Security: Blade @csrf is used; no external JS deps; CSP-friendly minimal inline JS.
    -->
</head>
<body>
    <div class="container">
        <a href="{{ route('root') }}" class="logo" aria-label="Anasayfa">
            <img src="{{ asset('applogo.png') }}" alt="İzmir Time Machine">
        </a>

        <div class="header">
            <h1 class="title">Destek Talebi</h1>
            <p class="subtitle">Sorun, öneri veya geri bildiriminizi iletin. En kısa sürede dönüş yapacağız.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
                <button type="button" data-close style="float:right; background:none; border:0; color:#bbf7d0; cursor:pointer">✕</button>
            </div>
        @endif

        <div class="grid">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('support.submit') }}" novalidate>
                        @csrf
                        <div class="row row-2">
                            <div>
                                <label for="name">Ad Soyad</label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" class="@error('name') invalid @enderror" placeholder="Adınız Soyadınız">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label for="email">E-posta</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" class="@error('email') invalid @enderror" placeholder="ornek@mail.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row row-2">
                            <div>
                                <label for="phone">Telefon</label>
                                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="@error('phone') invalid @enderror" placeholder="05xx xxx xx xx">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div></div>
                        </div>
                        <div class="row">
                            <div>
                                <label for="message">Mesaj</label>
                                <textarea id="message" name="message" rows="7" class="@error('message') invalid @enderror" placeholder="Lütfen talebinizi yazınız...">{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div style="margin-top:10px">
                            <button type="submit" class="btn">Gönder</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h3 style="margin:0 0 10px">İletişim</h3>
                    <p class="contact-blurb muted">Destek ekibimiz en kısa sürede sizinle iletişime geçecektir. Lütfen mümkün olduğunca detay verin.</p>
                </div>
            </div>
        </div>

        <div class="footer">© {{ date('Y') }} İzmir Time Machine</div>
    </div>
</body>
</html>


