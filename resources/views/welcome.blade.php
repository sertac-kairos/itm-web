<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İzmir Time Machine</title>
    <meta content="İzmir Time Machine" name="author" />
    <meta name="description" content="İzmir Time Machine" />
    <meta property="og:site_name" content="İzmir Time Machine" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://izmirtimemachine.com" />
    <meta property="og:title" content="İzmir Time Machine" />

    <!-- App favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="https://izmirtimemachine.com/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://izmirtimemachine.com/favicon-16x16.png">
    <link rel="shortcut icon" href="https://izmirtimemachine.com/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="https://izmirtimemachine.com/apple-touch-icon.png">
    <meta name="theme-color" content="#000000" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts and Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Labrada:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Labrada', serif;
            min-height: 100vh;
            background-image: url('/image-bg.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .main-container {
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        /* Header */
        .header {
            background: rgba(26, 35, 126, 0.7);
            height: 80px;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 10;
        }
        
        .header-content {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            width: 100%;
            padding: 0 2rem;
        }
        
        .brand-container {
            flex: 0 0 auto;
        }
        
        .brand-logo {
            height: 30px;
            width: auto;
        }
        
        
        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 4rem 2rem;
            position: relative;
            z-index: 5;
            margin-top: -100px;
            transition: all 0.5s ease;
        }
        
        .main-content.login-mode {
            align-items: flex-start;
            text-align: left;
            padding-left: 4rem;
        }
        
        .main-title {
            color: #000;
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 1rem;
            line-height: 1.2;
            max-width: 800px;
        }
        
        
        .cta-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            transition: all 0.5s ease;
        }
        
        .main-content.login-mode .cta-buttons {
            justify-content: flex-start;
        }
        
        .cta-button {
            background: #1a237e;
            color: white;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            background: #0d1a5c;
            color: white;
            text-decoration: none;
        }
        
        .cta-button.secondary {
            background: #6c757d;
        }
        
        .cta-button.secondary:hover {
            background: #5a6268;
        }
        
        .cta-button.disabled {
            background: #ccc;
            color: #666;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .cta-button.disabled:hover {
            background: #ccc;
            color: #666;
        }
        
        /* Main Image */
        .main-image {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 60vh;
            object-fit: cover;
            z-index: 1;
        }
        
        /* Footer */
        .footer-privacy {
            position: absolute;
            bottom: 1rem;
            right: 1rem;
            color: rgba(0, 0, 0, 0.6);
            font-size: 0.9rem;
            z-index: 10;
        }
        
        .footer-privacy a {
            color: rgba(0, 0, 0, 0.6);
            text-decoration: none;
        }
        
        .footer-privacy a:hover {
            color: #1a237e;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
        }
        
        .form-label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .form-input {
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #1a237e;
        }
        
        .login-submit {
            background: #1a237e;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }
        
        .login-submit:hover {
            background: #0d1a5c;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: none;
        }
        
        .error-message.show {
            display: block;
        }
        
        .form-input.error {
            border-color: #dc3545;
        }
        
        .login-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        /* Login Panel */
        .login-panel {
            position: fixed;
            top: 0;
            right: -100%;
            width: 50%;
            height: 100vh;
            background: white;
            z-index: 1000;
            transition: right 0.5s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .login-panel.show {
            right: 0;
        }
        
        .login-panel-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-panel-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #1a237e;
            margin-bottom: 0.5rem;
        }
        
        .login-panel-subtitle {
            color: #666;
            font-size: 1rem;
        }
        
        .login-panel-form {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }
        
        .login-panel-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }
        
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        
        .overlay.show {
            display: block;
        }
        
        @media (max-width: 768px) {
            .login-panel {
                width: 100%;
                right: -100%;
            }
            
            .main-content.login-mode {
                padding-left: 2rem;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                padding: 0 1rem;
            }
            
            .nav-buttons {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.5rem;
            }
            
            .nav-button {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
            
            .main-title {
                font-size: 2rem;
            }
            
            .main-content {
                padding: 2rem 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .nav-buttons {
                flex-direction: column;
                width: 100%;
            }
            
            .nav-button {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="brand-container">
                    <img src="/applogo.png" alt="İzmir Time Machine" class="brand-logo">
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <h1 class="main-title">İzmir'in 8500 Yıllık Tarihinde Üç Boyutlu Zaman Yolculuğu</h1>
            <div class="cta-buttons">
                <a href="#" class="cta-button" onclick="showLoginPanel()">
                    Beta Login
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="#" class="cta-button secondary disabled" onclick="return false;">
                    Canlı Login
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Main Image -->
        <img src="/image-models.webp" alt="İzmir Historical Buildings" class="main-image">

        <!-- Footer Privacy -->
        <div class="footer-privacy">
            <a href="#">Gizlilik - Şartlar</a>
        </div>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="overlay" onclick="hideLoginPanel()"></div>

    <!-- Login Panel -->
    <div id="loginPanel" class="login-panel">
        <button class="login-panel-close" onclick="hideLoginPanel()">&times;</button>
        <div class="login-panel-header">
            <h2 class="login-panel-title">Beta Giriş</h2>
            <p class="login-panel-subtitle">Yönetim Paneline Giriş</p>
        </div>
        <form id="loginForm" class="login-panel-form">
            @csrf
            <div class="form-group">
                <label class="form-label" for="panel_email">Email</label>
                <input type="email" id="panel_email" name="email" class="form-input" value="{{ old('email') }}" required autofocus placeholder="mail@domain.com">
                <div id="emailError" class="error-message"></div>
            </div>
            <div class="form-group">
                <label class="form-label" for="panel_password">Şifre</label>
                <input type="password" id="panel_password" name="password" class="form-input" required placeholder="••••••••">
                <div id="passwordError" class="error-message"></div>
            </div>
            <div id="generalError" class="error-message"></div>
            <button type="submit" class="login-submit" id="loginSubmit">Giriş Yap</button>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showLoginPanel() {
            document.getElementById('loginPanel').classList.add('show');
            document.getElementById('overlay').classList.add('show');
            document.getElementById('mainContent').classList.add('login-mode');
            clearErrors();
        }
        
        function hideLoginPanel() {
            document.getElementById('loginPanel').classList.remove('show');
            document.getElementById('overlay').classList.remove('show');
            document.getElementById('mainContent').classList.remove('login-mode');
            clearErrors();
        }
        
        function clearErrors() {
            document.getElementById('emailError').classList.remove('show');
            document.getElementById('passwordError').classList.remove('show');
            document.getElementById('generalError').classList.remove('show');
            document.getElementById('panel_email').classList.remove('error');
            document.getElementById('panel_password').classList.remove('error');
        }
        
        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
        
        function showFieldError(fieldId, message) {
            showError(fieldId + 'Error', message);
            document.getElementById(fieldId).classList.add('error');
        }
        
        // Close panel with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideLoginPanel();
            }
        });
        
        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            console.log('Form submitted, preventing default');
            e.preventDefault();
            e.stopPropagation();
            
            clearErrors();
            
            const email = document.getElementById('panel_email').value;
            const password = document.getElementById('panel_password').value;
            const submitBtn = document.getElementById('loginSubmit');
            
            console.log('Email:', email, 'Password length:', password.length);
            
            // Basic validation
            if (!email) {
                showFieldError('panel_email', 'E-posta adresi gerekli');
                return;
            }
            
            if (!password) {
                showFieldError('panel_password', 'Şifre gerekli');
                return;
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Giriş yapılıyor...';
            
            // Send AJAX request using the same route as the working login form
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            console.log('Sending AJAX request to:', '{{ route("login.attempt") }}');
            
            fetch('{{ route("login.attempt") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response redirected:', response.redirected);
                
                if (response.redirected) {
                    // Login successful, redirect
                    console.log('Login successful, redirecting');
                    hideLoginPanel();
                    window.location.href = response.url;
                    return;
                }
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data && data.success) {
                    // Login successful
                    console.log('Login successful via data.success');
                    hideLoginPanel();
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else if (data && data.errors) {
                    // Show validation errors
                    console.log('Validation errors:', data.errors);
                    if (data.errors.email) {
                        showFieldError('panel_email', data.errors.email[0]);
                    }
                    if (data.errors.password) {
                        showFieldError('panel_password', data.errors.password[0]);
                    }
                } else if (data && data.message) {
                    console.log('Error message:', data.message);
                    showError('generalError', data.message);
                } else {
                    console.log('Generic error message');
                    showError('generalError', 'Giriş yapılamadı. Lütfen bilgilerinizi kontrol edin.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showError('generalError', 'Bir hata oluştu. Lütfen tekrar deneyin.');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Giriş Yap';
            });
            
            return false;
        });
    </script>
</body>
</html>