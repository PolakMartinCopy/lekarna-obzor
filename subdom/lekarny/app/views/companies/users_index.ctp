<h1>Objednávkový systém Pharmacorp CZ, spol. s r.o.</h1>
<h2>Kontaktní osoba</h2>
<table class="left_headed">
	<tr>
		<th>
			jméno a příjmení
		</th>
		<td>
			<?=$company['Company']['person_first_name'] . ' ' . $company['Company']['person_last_name']?>
		</td>
	</tr>
	<tr>
		<th>
			telefon
		</th>
		<td>
			<?=$company['Company']['person_phone'] ?>
		</td>
	</tr>
	<tr>
		<th>
			email
		</th>
		<td>
			<?=$company['Company']['person_email'] ?>
		</td>
	</tr>
</table>
<h2>Údaje o společnosti</h2>
<table class="left_headed">
	<tr>
		<th>
			název společnosti
		</th>
		<td>
			<?=$company['Company']['name']?>
		</td>
	</tr>
	<tr>
		<th>
			IČO
		</th>
		<td>
			<?=$company['Company']['ico']?>
		</td>
	</tr>
		<tr>
		<th>
			DIČ
		</th>
		<td>
			<?=$company['Company']['dic']?>
		</td>
	</tr>
</table>
<h2>Fakturační adresa</h2>
<table class="left_headed">
	<tr>
		<td>
			<?=$company['Company']['payment_name']?>
		</td>
	</tr>
	<tr>
		<td>
			<?=$company['Company']['payment_street'] . ' ' . $company['Company']['payment_street_number']?>
		</td>
	</tr>
	<tr>
		<td>
			<?=$company['Company']['payment_postal_code'] . ' ' . $company['Company']['payment_city']?>
		</td>
	</tr>
</table>
<h2>Doručovací adresa</h2>
<table class="left_headed">
	<tr>
		<td>
			<?=$company['Company']['delivery_name']?>
		</td>
	</tr>
	<tr>
		<td>
			<?=$company['Company']['delivery_street'] . ' ' . $company['Company']['delivery_street_number']?>
		</td>
	</tr>
	<tr>
		<td>
			<?=$company['Company']['delivery_postal_code'] . ' ' . $company['Company']['delivery_city']?>
		</td>
	</tr>
</table>