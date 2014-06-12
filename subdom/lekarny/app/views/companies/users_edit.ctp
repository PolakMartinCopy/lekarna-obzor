<h1>Úprava zákazníka</h1>
<?=$form->Create('Company', array('url' => '/users/companies/edit/')) ?>

<fieldset>
	<legend>odpovědná osoba</legend>
	<table class="left_headed register">
		<tr>
			<th>
				jméno
			</th>
			<td>
				<?=$form->input('person_first_name', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				příjmení
			</th>
			<td>
				<?=$form->input('person_last_name', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				telefon
			</th>
			<td>
				<?=$form->input('person_phone', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				email
			</th>
			<td>
				<?=$form->input('person_email', array('label' => false));?>
			</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>Údaje o společnosti</legend>
	<table class="left_headed register">
		<tr>
			<th>
				název společnosti
			</th>
			<td>
				<?=$form->input('name', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				IČO
			</th>
			<td>
				<?=$form->input('ico', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				DIČ
			</th>
			<td>
				<?=$form->input('dic', array('label' => false));?>
			</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>Fakturační adresa</legend>
	<table class="left_headed register">
		<tr>
			<th>
				název společnosti
			</th>
			<td>
				<?=$form->input('payment_name', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				ulice
			</th>
			<td>
				<?=$form->input('payment_street', array('label' => false));?>

			</td>
		</tr>
		<tr>
			<th>
				číslo popisné
			</th>
			<td>
				<?=$form->input('payment_street_number', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				psč
			</th>
			<td>
				<?=$form->input('payment_postal_code', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				město
			</th>
			<td>
				<?=$form->input('payment_city', array('label' => false));?>
			</td>
		</tr>
	</table>	
</fieldset>


<fieldset>
	<legend>Doručovací adresa</legend>
	<table class="left_headed register">
		<tr>
			<th>
				adresát
			</th>
			<td>
				<?=$form->input('delivery_name', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				ulice
			</th>
			<td>
				<?=$form->input('delivery_street', array('label' => false));?>

			</td>
		</tr>
		<tr>
			<th>
				číslo popisné
			</th>
			<td>
				<?=$form->input('delivery_street_number', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				psč
			</th>
			<td>
				<?=$form->input('delivery_postal_code', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				město
			</th>
			<td>
				<?=$form->input('delivery_city', array('label' => false));?>
			</td>
		</tr>
	</table>	
</fieldset>

<?=$form->hidden('Company.id') ?>
<?=$form->submit('odeslat') ?>
<?=$form->end() ?>