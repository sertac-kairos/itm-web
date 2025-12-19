<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gizlilik Politikası - İzmir Time Machine</title>
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
        .card{background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,0.35); margin-bottom:20px}
        .card-body{padding:20px}
        .card-title{margin:0 0 16px; font-size:18px; color:var(--text)}
        .content{line-height:1.6; color:var(--text)}
        .content p{margin:0 0 16px}
        .content h3{margin:20px 0 12px; color:var(--text)}
        .content h4{margin:16px 0 8px; color:var(--muted)}
        .content ul{margin:0 0 16px; padding-left:20px}
        .content li{margin:4px 0}
        .footer{margin-top:28px; text-align:center; color:var(--muted); font-size:13px}
        a{text-decoration:none; color:#93c5fd}
        a:hover{text-decoration:underline}
        .last-updated{color:var(--muted); font-size:12px; margin-bottom:20px}
        .language-selector{display:flex; gap:8px; margin-bottom:16px; justify-content:flex-end}
        .lang-btn{display:inline-flex; align-items:center; justify-content:center; padding:6px 12px; border-radius:6px; background:rgba(255,255,255,0.08); color:var(--muted); text-decoration:none; font-size:12px; font-weight:500; transition:all 0.2s ease}
        .lang-btn:hover{background:rgba(255,255,255,0.12); color:var(--text); text-decoration:none}
        .lang-btn.active{background:var(--primary); color:#fff}
    </style>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#0f172a">
    <meta name="color-scheme" content="dark">
    <meta name="description" content="İzmir Time Machine Gizlilik Politikası">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    <meta name="referrer" content="no-referrer-when-downgrade">
</head>
<body>
    <div class="container">
        <a href="{{ route('root') }}" class="logo" aria-label="Anasayfa">
            <img src="{{ asset('applogo.png') }}" alt="İzmir Time Machine">
        </a>

        <div class="header">
            <div class="language-selector">
                <a href="?lang=tr" class="lang-btn {{ $locale === 'tr' ? 'active' : '' }}">TR</a>
                <a href="?lang=en" class="lang-btn {{ $locale === 'en' ? 'active' : '' }}">EN</a>
            </div>
            <h1 class="title">{{ $locale === 'en' ? 'Privacy Policy' : 'Gizlilik Politikası' }}</h1>
            <p class="subtitle">{{ $locale === 'en' ? 'The protection of your personal data is important to us.' : 'Kişisel verilerinizin korunması bizim için önemlidir.' }}</p>
            <div class="last-updated">{{ $locale === 'en' ? 'Last updated:' : 'Son güncelleme:' }} {{ date('d.m.Y') }}</div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="content">
                    {!! $privacyPolicyContent !!}
                </div>
            </div>
        </div>

        <div class="footer">© {{ date('Y') }} İzmir Time Machine</div>
    </div>
</body>
</html>
