<h1>Úprava zákazníka</h1>
<?=$form->Create('Company', array('action' => 'edit')) ?>

<h2>odpovědná osoba</h2>
<table class="left_headed" cellpadding="5" cellspacing="3">
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

<h2>Údaje o společnosti</h2>
<table class="left_headed" cellpadding="5" cellspacing="3">
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

<h2>Doručovací adresa</h2>
<table class="left_headed" cellpadding="5" cellspacing="3">
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

<h2>Fakturčaní adresa</h2>
<table class="left_headed" cellpadding="5" cellspacing="3">
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
	<tr>
		<th>
			aktivní
		</th>
		<td>
			<?=$form->radio('active', array('0' => 'neschválit', '1' => 'schválit'), array('label' => false, 'legend' => false, 'separator' => '<br/>')) ?>
			<?=$form->hidden('notified') ?>
		</td>
	</tr>
</table>

<h2>Přístupy</h2>
<table class="left_headed" cellpadding="5" cellspacing="3">
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
			<?=$form->input('password', array('label' => false, 'type' => 'text'));?>
		</td>
	</tr>
</table>

<?php echo $form->hidden('Company.id') ?>
<?php echo $form->submit('odeslat') ?>
<?php echo $form->end() ?>