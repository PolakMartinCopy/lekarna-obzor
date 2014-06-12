<h1>Registrace nového zákazníka</h1>
<?=$form->Create('Company') ?>

<fieldset>
	<legend>Odpovědná osoba</legend>
	<table class="left_headed register" cellpadding="5" cellspacing="3">
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
	<table class="left_headed register" cellpadding="5" cellspacing="3">
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
	<table class="left_headed register" cellpadding="5" cellspacing="3">
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
	<table class="left_headed register" cellpadding="5" cellspacing="3">
		<tr>
			<td colspan="2">
				<?=$form->checkbox('delivery_same_as_payment', array('onclick' => 'return copy_payment_as_delivery();'))?>
				zaškrtněte, je-li doručovací adresa shodná s fakturační
			</td>
		</tr>
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

<fieldset>
	<legend>Přihlašovací údaje</legend>
	<table class="left_headed register" cellpadding="5" cellspacing="3">
		<tr>
			<td colspan="2" style="font-size:10px;">
				<strong>Nemusíte vyplňovat, login a heslo bude vygenerováno automaticky.</strong><br />
				Zvolíte-li si přihlašovací údaje sami, login i heslo musí mít nejméně 10 znaků.
			</td>
		</tr>
		<tr>
			<th>
				login
			</th>
			<td>
				<?=$form->input('login', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				heslo
			</th>
			<td>
				<?=$form->input('password', array('label' => false));?>
			</td>
		</tr>
		<tr>
			<th>
				zopakujte heslo
			</th>
			<td>
				<?=$form->input('password_repeat', array('label' => false, 'type' => 'password'));?>
			</td>
		</tr>
	</table>
</fieldset>

<?=$form->submit('odeslat') ?>
<?=$form->end() ?>