<?php
/**
 * Turkish interface strings.
 */

return [
    // Application
    'App_Name'            => 'Kütüphane Kayıtları',
    'App_Name_Upper'      => 'KÜTÜPHANE KAYITLARI',
    'App_Short_Name'      => 'KK',
    'Page_Title'          => 'Kütüphane Kayıtları Yönetici Paneli',
    'Version'             => 'Sürüm',
    'All_Rights_Reserved' => 'Tüm hakları saklıdır.',
    'Toggle_Navigation'   => 'Menüyü aç/kapat',

    // Sign in
    'Login_Title'                 => 'Kütüphane Kayıtlarına Giriş',
    'Login_Prompt'                => 'Yönetici paneline erişmek için giriş yapın',
    'Login'                       => 'Giriş Yap',
    'Sign_Out'                    => 'Çıkış Yap',
    'Enter_Username_And_Password' => 'Lütfen kullanıcı adınızı ve şifrenizi girin.',
    'Invalid_Credentials'         => 'Kullanıcı adı veya şifre hatalı.',
    'No_Panel_Access'             => 'Hesabınızın yönetici paneline erişim yetkisi yok.',
    'Too_Many_Login_Attempts'     => 'Çok fazla hatalı deneme yapıldı. Lütfen 15 dakika sonra tekrar deneyin.',

    // Navigation and dashboard
    'Homepage'        => 'Anasayfa',
    'Control_Panel'   => 'Kontrol Paneli',
    'Books'           => 'Kitaplar',
    'Computers'       => 'Bilgisayarlar',
    'Keys'            => 'Anahtarlar',
    'Personnel'       => 'Personel',
    'Loans'           => 'Emanetler',
    'Settings'        => 'Ayarlar',
    'Total_Books'     => 'Toplam Kitap',
    'Total_Keys'      => 'Toplam Anahtar',
    'Total_Computers' => 'Toplam Bilgisayar',
    'Total_Personnel' => 'Toplam Personel',
    'Open_Loans'      => 'Şu anda emanette olan eşyalar',
    'More_Info'       => 'Detaylar',

    // Common
    'Add'        => 'Ekle',
    'Edit'       => 'Düzenle',
    'Delete'     => 'Sil',
    'Save'       => 'Kaydet',
    'Cancel'     => 'İptal',
    'Close'      => 'Kapat',
    'Status'     => 'Durum',
    'Type'       => 'Tür',
    'Item'       => 'Eşya',
    'Operations' => 'İşlemler',
    'Unknown'    => 'Bilinmiyor',

    // Messages
    'Process_Successful'         => 'İşlem başarılı.',
    'Process_Failed'             => 'İşlem başarısız.',
    'Record_Deleted'             => 'Kayıt silindi.',
    'Fill_All_Fields'            => 'Lütfen tüm zorunlu alanları doldurun.',
    'Invalid_Request'            => 'Geçersiz veya süresi dolmuş istek. Lütfen geri dönüp sayfayı yenileyin ve tekrar deneyin.',
    'Confirm_Delete'             => 'Silmek istediğinize emin misiniz? Bu işlem geri alınamaz!',
    'Item_On_Loan_Cannot_Delete' => 'Bu eşya emanette. Silmeden önce lütfen iade alın.',

    // Item status
    'In_Inventory'      => 'Envanterde',
    'Lent'              => 'Emanette',
    'Lent_Description'  => '%2$s tarihinde %3$s tarafından %1$s adlı personele emanet edildi.',
    'Deleted_Personnel' => '(silinmiş personel)',
    'Deleted_Item'      => '(silinmiş eşya)',

    // Books
    'Book'              => 'Kitap',
    'Book_Name'         => 'Kitap Adı',
    'Author'            => 'Yazar',
    'Add_New_Book'      => 'Yeni Kitap Ekle',
    'Add_Book'          => 'Kitabı Ekle',
    'Edit_Book'         => 'Kitabı Düzenle',
    'Enter_Book_Name'   => 'Kitap adını girin',
    'Enter_Book_Author' => 'Yazarı girin',

    // Computers
    'Computer'            => 'Bilgisayar',
    'Manufacturer'        => 'Üretici',
    'Model'               => 'Model',
    'Serial_Number'       => 'Seri Numarası',
    'Add_New_Computer'    => 'Yeni Bilgisayar Ekle',
    'Add_Computer'        => 'Bilgisayarı Ekle',
    'Edit_Computer'       => 'Bilgisayarı Düzenle',
    'Enter_Manufacturer'  => 'Üreticiyi girin',
    'Enter_Model'         => 'Modeli girin',
    'Enter_Serial_Number' => 'Seri numarasını girin',

    // Keys
    'Key'                   => 'Anahtar',
    'Key_Number'            => 'Anahtar Numarası',
    'Description'           => 'Açıklama',
    'Add_New_Key'           => 'Yeni Anahtar Ekle',
    'Add_Key'               => 'Anahtarı Ekle',
    'Edit_Key'              => 'Anahtarı Düzenle',
    'Enter_Key_Number'      => 'Anahtar numarasını girin',
    'Enter_Key_Description' => 'Anahtarın neyi açtığını girin',

    // Loans
    'New_Loan'             => 'Yeni Emanet',
    'Select_Book'          => 'Bir kitap seçin',
    'Select_Computer'      => 'Bir bilgisayar seçin',
    'Select_Key'           => 'Bir anahtar seçin',
    'Select_Personnel'     => 'Personel seçin',
    'Borrower'             => 'Emanet Alan',
    'Lender'               => 'Emanet Veren',
    'Loan_Date'            => 'Emanet Tarihi',
    'Return_Date'          => 'İade Tarihi',
    'Received_By'          => 'İade Alan',
    'Not_Returned_Yet'     => 'Henüz iade edilmedi',
    'Mark_Returned'        => 'İade alındı olarak işaretle',
    'Confirm_Return'       => 'Bu eşya iade alındı olarak işaretlensin mi? Bugünün tarihi ve adınız kaydedilecek.',
    'Confirm_Delete_Loan'  => 'Bu emanet kaydı sistemden silinecek. Devam edilsin mi?',
    'Item_Already_Lent'    => 'Bu eşya zaten emanette.',
    'Already_Returned'     => 'Bu eşya zaten iade alınmış.',
    'Return_Before_Delete' => 'İade alınmamış bir emanet silinemez. Lütfen önce iade alın.',

    // Personnel
    'Full_Name'                    => 'Ad Soyad',
    'Username'                     => 'Kullanıcı Adı',
    'Email'                        => 'e-Posta',
    'Phone'                        => 'Telefon',
    'Password'                     => 'Şifre',
    'Admin'                        => 'Yönetici',
    'Is_Admin'                     => 'Yönetici (bu panele giriş yapabilir)',
    'Add_New_Personnel'            => 'Yeni Personel Ekle',
    'Edit_Personnel'               => 'Personeli Düzenle',
    'Enter_Full_Name'              => 'Ad soyad girin',
    'Enter_Phone'                  => 'Telefon numarası girin',
    'Enter_Email'                  => 'e-Posta adresi girin',
    'Enter_Username'               => 'Kullanıcı adı girin',
    'Enter_Password'               => 'Şifre girin',
    'Leave_Blank_To_Keep_Password' => 'Şifreyi değiştirmemek için boş bırakın',
    'Invalid_Email'                => 'Lütfen geçerli bir e-posta adresi girin.',
    'Password_Too_Short'           => 'Şifre en az 8 karakter olmalıdır.',
    'Username_Taken'               => 'Bu kullanıcı adı zaten kullanılıyor.',
    'Cannot_Delete_Self'           => 'Kendi hesabınızı silemezsiniz.',
    'Personnel_Has_Open_Loans'     => 'Bu personelde hâlâ emanet eşya var. Lütfen önce iade alın.',

    // Settings
    'Account_Settings'          => 'Hesap Ayarları',
    'New_Password'              => 'Yeni Şifre',
    'Current_Password'          => 'Mevcut Şifre',
    'Current_Password_Required' => 'Değişiklikleri kaydetmek için gerekli',
    'Current_Password_Wrong'    => 'Mevcut şifreniz hatalı.',
];
