<?
	if ( $session->check('Company') ){
		header('Location: http://lekarny.lekarna-obzor.cz/users/companies/index');
		header('Connection: close');
		die();
	}
?>
	<h1>Objednávkový systém Pharmacorp CZ s.r.o.</h1>
	<p>Vítejte v sekci určené obchodním partnerům naší společnosti. V případě že si chcete objednat zboží, můžete využít náš nový objednávkový systém. Tento systém je pro Vás výhodný z následujících důvodů:</p>
	<ul>
		<li>umožní Vám evidovat stav Vašich objednávek</li>
		<li>umožňuje vytvořit objednávku duplikací nebo úpravou předchozí objednávky</li>
		<li>neztrácíte čas telefonním kontaktem</li>
		<li>eliminuje chyby při přijímání telefonické objednávky a zaslání jiného zboží</li>
		<li>udržuje aktuální relevantní adresy a kontakty na Vaši společnost</li>
	</ul>
	<h2>Přihlášení do systému</h2>
	<p>Jste-li již registrovaným obchodním partnerem, <a href="http://lekarny.lekarna-obzor.cz/users/companies/login">přihlašte se</a>.
	<h2>Registrace nového obchodního partnera</h2>
	<p>Ještě jste se nestali registrovaným obchodním partnerem? <a href="http://lekarny.lekarna-obzor.cz/users/companies/register">Zaregistrujte se</a> a začněte využívat výše uvedených výhod našeho systému.</p>