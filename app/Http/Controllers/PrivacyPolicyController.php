<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    public function show(Request $request)
    {
        // Get locale from request parameter or default to Turkish
        $locale = $request->get('lang', 'tr');
        
        // Validate locale
        if (!in_array($locale, ['tr', 'en'])) {
            $locale = 'tr';
        }
        
        // Set application locale
        app()->setLocale($locale);
        
        // Get privacy policy content from settings
        $privacyPolicyContent = AppSetting::get("privacy_policy.$locale");
        
        // If no content found for current locale, try Turkish as fallback
        if (empty($privacyPolicyContent)) {
            $privacyPolicyContent = AppSetting::get("privacy_policy.tr");
        }
        
        // If still no content, show default message
        if (empty($privacyPolicyContent)) {
            $privacyPolicyContent = $this->getDefaultPrivacyPolicyContent($locale);
        }
        
        return view('pages.privacy-policy', compact('privacyPolicyContent', 'locale'));
    }
    
    private function getDefaultPrivacyPolicyContent($locale = 'tr')
    {
        if ($locale === 'en') {
            return '<h3>Privacy Policy</h3>
            <p>As the İzmir Time Machine application, the protection of your personal data is of great importance to us. This privacy policy explains what information we collect when you use our application and how we use this information.</p>
            
            <h3>Collected Information</h3>
            <p>When using our application, we may collect the following information:</p>
            <ul>
                <li>Your contact information (email, phone)</li>
                <li>Device information (device ID, operating system)</li>
                <li>Usage statistics</li>
                <li>Your support requests</li>
            </ul>
            
            <h3>Use of Information</h3>
            <p>We use the information we collect for the following purposes:</p>
            <ul>
                <li>To develop and improve the application</li>
                <li>To provide you with support</li>
                <li>To fulfill our legal obligations</li>
            </ul>
            
            <h3>Information Security</h3>
            <p>We take appropriate security measures to protect your personal information. Your information is stored on secure servers and protected against unauthorized access.</p>
            
            <h3>Cookies</h3>
            <p>Our application may use cookies to improve user experience. These cookies help us remember your preferences and provide you with better service.</p>
            
            <h3>Sharing with Third Parties</h3>
            <p>We do not share your personal information with third parties unless required by law. We may only share limited information with our service providers within the framework of privacy agreements.</p>
            
            <h3>Your Rights</h3>
            <p>Regarding your personal data, you have the following rights:</p>
            <ul>
                <li>Right to access your data</li>
                <li>Right to correct your data</li>
                <li>Right to delete your data</li>
                <li>Right to object to data processing</li>
            </ul>
            
            <h3>Contact</h3>
            <p>If you have any questions about our privacy policy, please contact us:</p>
            <p>Email: <a href="mailto:info@izmirimemachine.com">info@izmirimemachine.com</a></p>
            
            <h3>Changes</h3>
            <p>This privacy policy may be updated from time to time. We will notify you in case of important changes.</p>';
        }
        
        // Turkish content (default)
        return '<h3>Gizlilik Politikası</h3>
        <p>İzmir Time Machine uygulaması olarak, kişisel verilerinizin korunması bizim için büyük önem taşımaktadır. Bu gizlilik politikası, uygulamamızı kullanırken topladığımız bilgileri ve bu bilgileri nasıl kullandığımızı açıklar.</p>
        
        <h3>Toplanan Bilgiler</h3>
        <p>Uygulamamızı kullanırken aşağıdaki bilgileri toplayabiliriz:</p>
        <ul>
            <li>İletişim bilgileriniz (e-posta, telefon)</li>
            <li>Cihaz bilgileri (cihaz ID, işletim sistemi)</li>
            <li>Kullanım istatistikleri</li>
            <li>Destek talepleriniz</li>
        </ul>
        
        <h3>Bilgilerin Kullanımı</h3>
        <p>Topladığımız bilgileri şu amaçlarla kullanırız:</p>
        <ul>
            <li>Uygulamayı geliştirmek ve iyileştirmek</li>
            <li>Size destek sağlamak</li>
            <li>Yasal yükümlülüklerimizi yerine getirmek</li>
        </ul>
        
        <h3>Bilgi Güvenliği</h3>
        <p>Kişisel bilgilerinizi korumak için uygun güvenlik önlemlerini alırız. Bilgileriniz güvenli sunucularda saklanır ve yetkisiz erişime karşı korunur.</p>
        
        <h3>Çerezler (Cookies)</h3>
        <p>Uygulamamız, kullanıcı deneyimini iyileştirmek için çerezler kullanabilir. Bu çerezler, tercihlerinizi hatırlamamıza ve size daha iyi hizmet sunmamıza yardımcı olur.</p>
        
        <h3>Üçüncü Taraflarla Paylaşım</h3>
        <p>Kişisel bilgilerinizi, yasal zorunluluk olmadıkça üçüncü taraflarla paylaşmayız. Sadece hizmet sağlayıcılarımızla, gizlilik anlaşmaları çerçevesinde sınırlı bilgi paylaşımı yapabiliriz.</p>
        
        <h3>Haklarınız</h3>
        <p>Kişisel verilerinizle ilgili olarak aşağıdaki haklara sahipsiniz:</p>
        <ul>
            <li>Verilerinize erişim hakkı</li>
            <li>Verilerinizi düzeltme hakkı</li>
            <li>Verilerinizi silme hakkı</li>
            <li>Veri işlemeye itiraz etme hakkı</li>
        </ul>
        
        <h3>İletişim</h3>
        <p>Gizlilik politikamız hakkında sorularınız varsa, lütfen bizimle iletişime geçin:</p>
        <p>E-posta: <a href="mailto:info@izmirimemachine.com">info@izmirimemachine.com</a></p>
        
        <h3>Değişiklikler</h3>
        <p>Bu gizlilik politikası zaman zaman güncellenebilir. Önemli değişiklikler durumunda size bildirim yapacağız.</p>';
    }
}
