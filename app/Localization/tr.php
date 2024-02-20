<?php

/**
 *  KalipsoX - Localization File
 *  Turkish(tr)
 **/

return [
    'lang' => [
        'code' => 'tr',
        'iso_code' => 'tr_TR',
        'dir' => 'ltr',
        'timezone' => 'Europe/Istanbul',
        'currency' => 'try',
        'plural_suffix' => '',
    ],
    'langs' => [
        'tr' => 'Türkçe',
        'en' => 'İngilizce',
    ],
    'error' => [
        'bad_request' => 'Hatalı İstek!',
        'bad_request_sub_text' => 'Bu sayfa isteğinizi işleyemedi.',
        'unauthorized' => 'Yetkisiz!',
        'unauthorized_sub_text' => 'Bu sayfaya erişim izniniz yok.',
        'forbidden' => 'Yasaklandı!',
        'forbidden_sub_text' => 'Bu sayfaya erişim izniniz yok.',
        'not_found' => 'Bulunamadı!',
        'not_found_sub_text' => 'Aradığınız sayfa bulunamadı.',
        'method_not_allowed' => 'Methoda izin verilmez!',
        'method_not_allowed_sub_text' => 'Bu sayfa istekte bulunduğunuz method için izne sahip değil.',
        'too_many_requests' => 'Çok fazla istek!',
        'too_many_requests_sub_text' => 'Bu sayfaya çok fazla istekte bulundunuz. <br> Lütfen bekleyin:',
        'internal_server_error' => 'Sunucu Hatası!',
        'internal_server_error_sub_text' => 'Bu sayfa isteğinizi işleyemedi.',
        'service_unavailable' => 'Servis Kullanılamaz!',
        'service_unavailable_sub_text' => 'Bu sayfa şu anda kullanılamıyor.',
    ],
    'auth' => [
        'login' => 'Giriş Yap',
        'login_desc' => 'Hesabınıza giriş yapın',
        'register' => 'Kayıt Ol',
        'register_desc' => 'Yeni bir hesap oluşturun',
        'recovery' => 'Hesabı Kurtar',
        'recovery_desc' => 'Eposta adresinizi girdikten sonra size gönderilen bağlantıya tıklayarak hesabınızı kurtarabilirsiniz.',
        'recovery_password_desc' => 'Aşağıdan yeni şifrenizi girerek hesabınızı kurtarabilirsiniz.',
        'email_or_username' => 'E-posta veya Kullanıcı Adı',
        'password' => 'Şifre',
        'show_password' => 'Şifreyi Göster',
        'remember_me' => 'Beni Hatırla',
        'recovery_account' => 'Hesabı Kurtar',
        'dont_have_account_yet' => 'Henüz bir hesabınız yok mu?',
        'you_have_already_account' => 'Zaten bir hesabınız var mı?',
        'username' => 'Kullanıcı Adı',
        'email' => 'E-posta',
        'username_already_exists' => 'Bu kullanıcı adı zaten mevcut.',
        'email_already_exists' => 'Bu e-posta adresi zaten mevcut.',
        'register_success' => 'Kayıt işlemi başarılı. Giriş sayfasına yönlendiriliyorsunuz...',
        'registration_system_disabled' => 'Kullanıcı kayıt sistemi şu anda devre dışı bırakılmıştır. Lütfen daha sonra tekrar deneyin.',
        'verify_account' => 'Hesabı Doğrula',
        'verify_account_desc' => 'Hesabınızı doğrulamak için e-posta adresinize gönderilen bağlantıya tıklayın.',
        'your_account_deleted' => 'Üzgünüz, hesabınız silinmiştir.',
        'password_incorrect' => 'Girdiğiniz şifre yanlış.',
        'account_not_found' => 'Hesap bulunamadı, lütfen bilgilerinizi kontrol edin.',
        'login_success' => 'Giriş başarılı, yönlendiriliyorsunuz...',
        'account_verified' => 'Hesabınız doğrulandı. Hesabınız şimdi aktif.',
        'a_problem_has_occurred' => 'Bir sorun oluştu!',
        'account_already_verified' => 'Hesabınız zaten doğrulanmış.',
        'token_not_found' => 'Geçersiz veya süresi dolmuş bir bağlantı.',
        'recovery_email_sent' => 'E-posta adresinize kurtarma bağlantısı gönderildi.',
        'new_password' => 'Yeni Şifre',
        'new_password_confirmation' => 'Yeni Şifre (Tekrar)',
        'password_changed' => 'Şifreniz başarıyla değiştirildi. Giriş sayfasına yönlendiriliyorsunuz...',
    ],
    'form' => [
        'fill_all_fields' => 'Lütfen tüm alanları doldurunuz.',
        'required_validation' => 'Bu alan zorunludur.',
        'min_validation' => 'Bu alan en az :min karakter olmalıdır.',
        'max_validation' => 'Bu alan en fazla :max karakter olmalıdır.',
        'email_validation' => 'Lütfen geçerli bir e-posta adresi giriniz.',
        'url_validation' => 'Lütfen geçerli bir URL adresi giriniz.',
        'ip_validation' => 'Lütfen geçerli bir IP adresi giriniz.',
        'numeric_validation' => 'Lütfen sadece sayısal değerler giriniz.',
        'alpha_validation' => 'Lütfen sadece harf değerleri giriniz.',
        'alphanumeric_validation' => 'Lütfen sadece harf ve sayısal değerler giriniz.',
        'regex_validation' => 'Lütfen geçerli bir değer giriniz.',
        'match_validation' => 'Bu alanın değeri :match değeri ile eşleşmelidir.',
        'in_validation' => 'Girdiğiniz değer :in içerisinde olmalıdır.',
        'not_in_validation' => 'Girdiğiniz değer :not_in içerisinde olmamalıdır.',
    ],
    'notification' => [
        'welcome_title' => 'Aramıza Hoş Geldin!',
        'welcome_body' => 'Şimdi hesabını kullanmaya başlayabilirsin.',
        'welcome_email_title' => 'Hesabın Başarıyla Oluşturuldu!',
        'welcome_email_body' => 'Merhaba @:user, <br> Hesabın başarıyla oluşturuldu. Aşağıdaki bağlantı ile hesabını doğrulayabilirsin. <br> <a href=":link_url">:link_text</a>',
        'account_verified_title' => 'Hesabın Doğrulandı!',
        'account_verified_body' => 'Hesabın başarıyla doğrulandı. Şimdi hesabını kullanmaya başlayabilirsin.',
        'account_verified_email_title' => 'Hesabın Doğrulandı!',
        'account_verified_email_body' => 'Merhaba @:user, <br> Hesabın başarıyla doğrulandı. Şimdi hesabını kullanmaya başlayabilirsin. <br> <a href=":link_url">:link_text</a>',
        'recovery_request_title' => 'Hesap Kurtarma İsteği!',
        'recovery_request_body' => 'Hesabınızı kurtarmak için bir şifre sıfırlama isteği gönderildi!',
        'recovery_request_email_title' => 'Hesap Kurtarma',
        'recovery_request_email_body' => 'Merhaba @:user, <br> Hesabını kurtarmak için bir şifre sıfırlama isteği gönderildi. Aşağıdaki bağlantı ile şifreni sıfırlayabilirsin. <br> <a href=":link_url">:link_text</a>',
        'recover_success_title' => 'Hesap Kurtarma Başarılı!',
        'recover_success_body' => 'Hesabını başarıyla kurtardın. Şimdi hesabını kullanmaya başlayabilirsin.',
        'recover_success_email_title' => 'Hesap Kurtarma Başarılı!',
        'recover_success_email_body' => 'Merhaba @:user, <br> Hesabını başarıyla kurtardın. Aşağıdaki bağlantı ile hesabını kullanmaya başlayabilirsin. <br> <a href=":link_url">:link_text</a>',
    ],
    'base' => [
        'toggle_theme' => 'Renk Modunu Değiştir',
        'copyright' => 'Telif Hakkı',
        'home' => 'Anasayfa',
        'back_to_home' => 'Ana Sayfaya Dön',
        'sorry' => 'Üzgünüz...',
        'reset' => 'Sıfırla',
    ],
];
