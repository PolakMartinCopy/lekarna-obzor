<h1>Úprava kontaktních informací objednávky č. <?php echo $id ?></h1>
<?php echo $form->create('Order', array('action' => 'admin_edit_customer_info')) ?>
<ul class="actions">
	<li><?php echo $html->link('zpět k objednávce bez změn', array('controller' => 'orders', 'action' => 'view', $id))?></li>
</ul>
<h2>Firma</h2>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>
			Název:
		</th>
		<td>
			<?php echo $form->input('Order.company_name', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
	<tr>
		<th>
			IČO:
		</th>
		<td>
			<?php echo $form->input('Order.company_ico', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
	<tr>
		<th>
			DIČ:
		</th>
		<td>
			<?php echo $form->input('Order.company_dic', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
</table>

<h2>Kontaktní údaje</h2>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>
			Jméno:
		</th>
		<td>
			<?=$form->input('Order.person_first_name', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
	<tr>
		<th>
			Příjmení:
		</th>
		<td>
			<?=$form->input('Order.person_last_name', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
	<tr>
		<th>
			Telefon:
		</th>
		<td>
			<?=$form->input('Order.person_phone', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
	<tr>
		<th>
			Email:
		</th>
		<td>
			<?=$form->input('Order.person_email', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
</table>

<h2>Fakturační adresa</h2>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>
			Adresát:
		</th>
		<td>
			<?=$form->input('Order.payment_name', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
	<tr>
		<th>
			Ulice:
		</th>
		<td>
			<?=$form->input('Order.payment_street', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
	<tr>
		<th>
			Město:
		</th>
		<td>
			<?=$form->input('Order.payment_city', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
	<tr>
		<th>
			PSČ:
		</th>
		<td>
			<?=$form->input('Order.payment_postal_code', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
</table>

<h2>Doručovací adresa</h2>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>
			Adresát:
		</th>
		<td>
			<?=$form->input('Order.delivery_name', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
	<tr>
		<th>
			Ulice:
		</th>
		<td>
			<?=$form->input('Order.delivery_street', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
	<tr>
		<th>
			Město:
		</th>
		<td>
			<?=$form->input('Order.delivery_city', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
	<tr>
		<th>
			PSČ:
		</th>
		<td>
			<?=$form->input('Order.delivery_postal_code', array('label' => false, 'size' => 60))?>
		</td>
	</tr>
</table>
<?=$form->end('Změnit údaje') ?>