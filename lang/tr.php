<?php

$lang = array(
"LIBRARY_RECORDS" =>"KÜTÜPHANE KAYITLARI",
"Library_Records" =>"Kütüphane Kayıtları",
"KK" => "KK",
"Toggle_Nav" => "Değiştir",
"Login_to_reach_panel" => "Yönetici paneline erişmek için giriş yapın",
"Login" => "Giriş Yap",
"lang" => "Dil",
"Homepage" => "Anasayfa",
"Control_Panel" => "Kontrol Paneli",
"Total_Books" => "Toplam Kitaplar",
"Total_Keys" => "Toplam Anahtarlar",
"Total_Computers" => "Toplam Bilgisayarlar",
"Total_Personnel" => "Toplam Personel",
"Books" => "Kitaplar",
"Computers" => "Bilgisayarlar",
"Keys" => "Anahtarlar",
"Personnel" => "Personel",
"Loans" => "Emanetler",
"Login_Title" => "Kitap Kayıtlarına Giriş Yap",
"Add_New_Personnel" => "Yeni Personel Oluştur",
"Name_Surname" => "Ad Soyad",
"Username" => "Kullanıcı Adı",
"eMail" => "e-Posta",
"Phone" => "Telefon",
"Operations" => "İşlemler",
"Are_you_sure_to_delete" => "Silmek istediğinize emin misiniz? Bu işlem geri alınamaz!",
"Enter_Name_Surname" => "Ad Soyad Girin",
"Enter_Phone" => "Telefon Numarası Girin",
"Enter_eMail" => "e-Posta Adresi Girin",
"Is_Admin" => "Bu kişi yetkili mi?",
"Add_the_Personnel" => "Personeli Ekle",
"Add_a_Key" => "Yeni Anahtar Oluştur",
"Number" => "Numara",
"Content" => "İçerik",
"Status" => "Durum",
"Enter_Key_Number" => "Anahtar Numarasını Girin",
"Enter_Key_Content" => "Anahtar İçeriğini Girin",
"Key_Number" => "Anahtar Numarası",
"Key_Content" => "Anahtar İçeriği",
"Key" => "Anahtar",
"Add_the_Key" => "Anahtarı Ekle",
"Add_New_Computer" => "Yeni Bilgisayar Oluştur",
"Manufacturer" => "Üretici",
"Model" => "Model",
"Serial_Number" => "Seri Numarası",
"Edit_Computer" => "Bilgisayarı Düzenle",
"Enter_Serial_Number" => "Seri Numarası Girin",
"Enter_Model" => "Model Girin",
"In_Inventory" => "Envanterde",
"Lent" => "Ödünç Verildi",
"Enter_Manufacturer" => "Üretici Girin",
"Add_the_Computer" => "Bilgisayarı Ekle",
"Add_New_Book" => "Yeni Kitap Oluştur",
"Book_Name" => "Kitap Adı",
"Writer" => "Yazar",
"Book_Writer" => "Kitap Yazarı",
"Book" => "Kitap",
"Enter_Book_Name" => "Kitap Adı Girin",
"Enter_Book_Writer" => "Kitap yazarı Girin",
"Add_the_Book" => "Kitabı Ekle",
"Edit" => "Düzenle",
"Cancel" => "İptal",
"Save" => "Kaydet",
"Process_Successful" => "İşlem Başarılı",
"Process_Failed" => "İşlem Başarısız",
"Lent_Information" => "Ödünç Bilgisi",
"New_Loan_Entry" => "Yeni Ödünç Girdisi Oluştur",
"Please_Select_a_Book_From_List" => "Lütfen Listeden Bir Kitap Seçin",
"Please_Select_a_Computer_From_List" => "Lütfen Listeden Bir Bilgisayar Seçin",
"Please_Select_a_Key_From_List" => "Lütfen Listeden Bir Anahtar Seçin",
"Computer" => "Bilgisayar",
"Loaner" => "Ödünç Alan",
"Lender" => "Ödünç Veren",
"Loan_Date" => "Ödünç Tarihi",
"Return_Date" => "İade Tarihi",
"Return_Accepter" => "İade Alan",
"Please_check_once_more" => "Lütfen bir kez daha kontrol edin",
"Select_Personnel" => "Personel Seç",
"Type" => "Tipi",
"Name" => "Adı",
"Not_Returned_Yet" => "Henüz İade Edilmedi",
"Date_and_Repossesser_will_be_assigned_automatically" => "İade Tarihi ve İade Alan otmatik olarak işlenecektir",
"This_loan_information_for_this_product_will_be_deleted_from_the_system" => "Bu ürün için bu ödünç bilgileri sistemden silinecektir",
"Settings" => "Ayarlar",
"Personnel_Settings_Screen" => "Personel Ayarları Ekranı",
"Enter_Username" => "Kullanıcı Adı Girin",
"Enter_Password" => "Şifre Girin",
"Enter_Phone" => "Telefon Girin",
"PageTitle" => "Kütüphane Kayıtları Yönetici Paneli",
"Record_Deleted" => "Kayıt silindi",
"Record_couldnt_Deleted" => "Kayıt silinirken bir hata oluştu",
"" => "",
"" => "",

);
function getTypeName ($type)
{
	$returnString="";
	switch($type)
	{
		case 0:
		{
			$returnString = $lang["Book"];
			break;
		}
		case 1:
		{
			$returnString = $lang["Computer"];
			break;
		}
		case 2:
		{
			$returnString = $lang["Key"];
			break;
		}
		default:
		{
			$returnString = "UNDEFINED";
		}
	}
	return $returnString;
}
function returnKeyStatus()
{
	$returnString="";
	$returnString += 'Bu '.getTypeName($pulledLoan['type']).', '.$pulledLoan['loanDate'].' tarihinde ';
	
	$pullPersonnel=$connection->query("select * from personnel where personnelId=".$pulledLoan['loanerId']."")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($pullPersonnel as $pulledPersonnel)
	{
		$returnString += $pulledPersonnel['nameSurname'];
	}
	$returnString += ' tarafından ';

	$pullPersonnel2=$connection->query("select * from personnel where personnelId=".$pulledLoan['loanerId']."")->fetchAll(PDO::FETCH_ASSOC);

	foreach ($pullPersonnel2 as $pulledPersonnel2)
	{
		$returnString += $pulledPersonnel2['nameSurname'];
	}
		$returnString += ' adlı personele emanet edilmiştir.';

	if ($pulledLoan['returnDate']!="0000-00-00") 
	{
		$returnString += $pulledLoan['returnDate'].' tarihinde, ';
		$pullPersonnel3=$connection->query("select * from personnel where personnelId=".$pulledLoan['returnAccepterId']."")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($pullPersonnel3 as $pulledPersonnel3)
		{
			$returnString += $pulledPersonnel3['nameSurname'];
		}
		$returnString += ' tarafından iade alınmıştır.';
	}
	else 
	{
		$returnString += 'Bu anahtar henüz iade alınmamıştır.';
	}
	
	
	
	return $returnString;
}
?>