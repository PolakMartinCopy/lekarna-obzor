Křestní jméno,Příjmení,Zobrazené jméno,Přezdívka,První e-mail,Druhý e-mail,Zobrazené jméno,Telefon do zaměstnání,Telefon domů,Faxové číslo,Číslo na pager,Číslo na mobil,Adresa domů,Adresa domů 2,Město,Kraj,PSČ,Země,Adresa do zaměstnání,Adresa do zaměstnání 2,Město,Kraj,PSČ,Země,Pozice,Oddělení,Společnost,Webová stránka 1,webová stránka 2,Rok narození,Měsíc narození,Den narození,Vlastní 1,Vlastní 2,Vlastní 3,Vlastní 4,Poznámky,
<?php 
	foreach ($customers as $customer) {
		echo $customer['Customer']['first_name'] . ',' . $customer['Customer']['last_name'] . ',,,' . $customer['Customer']['email'] . ',,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,' . "\n";
	}
?>