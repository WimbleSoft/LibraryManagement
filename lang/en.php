<?php
$lang = array(
"anasayfa"		=> "Anasayfa",
"pvplinkekle"	        => "Pvp Link Ekle",
"dilseciniz"	        => "Dil Seçiniz",
"trdil"			=> "Türkçe",
"ingdil"		=> "Ýngilizce",
"baslik"		=> "Baþlýk",
"durum"			=> "Durum",
"servertipi"	        => "Server Tipi",
"git"			=> "Git",
"yorumbirak"	        => "Bir Yorum Býrakýn...",
"adsoyad"		=> "Adýnýz ve Soyadýnýz...",
"email"			=> "Eposta Adresiniz...",
"yorum"			=> "Yorumunuzu Yazýn...",
"gonder"		=> "Gönder",
"onceki"		=> "Önceki",
"sonraki"		=> "Sonraki",
"acik"			=> "Açýk",
"kapali"		=> "Kapalý",
);

function returnKeyStatus()
{
	$returnString="";
	echo 'Bu '.$pulledLoan['type'].', '.$pulledLoan['loanDate'].' tarihinde ';
	
	$pullPersonnel=$connection->query("select * from personnel where personnelId=".$pulledLoan['loanerId']."")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($pullPersonnel as $pulledPersonnel)
	{
	echo $pulledPersonnel['nameSurname'];
	}
	echo ' tarafýndan ';
	$pullPersonnel2=$connection->query("select * from personnel where personnelId=".$pulledLoan['loanerId']."")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($pullPersonnel2 as $pulledPersonnel2)
	{
	echo $pulledPersonnel2['nameSurname'];
	}
	echo ' adlý personele emanet edilmiþtir.';
	if ($pulledLoan['returnDate']!="0000-00-00") 
	{
	//echo $pulledLoan['returnDate'].' tarihinde, '.$pulledLoan['returnAccepterId'].' tarafýndan iade alýnmýþtýr.';
	}
	else echo 'Bu anahtar henüz iade alýnmamýþtýr.';
	
	
	return $returnString;
}
?>