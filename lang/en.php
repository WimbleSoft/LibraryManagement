<?php
$lang = array(
"anasayfa"		=> "Anasayfa",
"pvplinkekle"	        => "Pvp Link Ekle",
"dilseciniz"	        => "Dil Se�iniz",
"trdil"			=> "T�rk�e",
"ingdil"		=> "�ngilizce",
"baslik"		=> "Ba�l�k",
"durum"			=> "Durum",
"servertipi"	        => "Server Tipi",
"git"			=> "Git",
"yorumbirak"	        => "Bir Yorum B�rak�n...",
"adsoyad"		=> "Ad�n�z ve Soyad�n�z...",
"email"			=> "Eposta Adresiniz...",
"yorum"			=> "Yorumunuzu Yaz�n...",
"gonder"		=> "G�nder",
"onceki"		=> "�nceki",
"sonraki"		=> "Sonraki",
"acik"			=> "A��k",
"kapali"		=> "Kapal�",
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
	echo ' taraf�ndan ';
	$pullPersonnel2=$connection->query("select * from personnel where personnelId=".$pulledLoan['loanerId']."")->fetchAll(PDO::FETCH_ASSOC);
	foreach ($pullPersonnel2 as $pulledPersonnel2)
	{
	echo $pulledPersonnel2['nameSurname'];
	}
	echo ' adl� personele emanet edilmi�tir.';
	if ($pulledLoan['returnDate']!="0000-00-00") 
	{
	//echo $pulledLoan['returnDate'].' tarihinde, '.$pulledLoan['returnAccepterId'].' taraf�ndan iade al�nm��t�r.';
	}
	else echo 'Bu anahtar hen�z iade al�nmam��t�r.';
	
	
	return $returnString;
}
?>