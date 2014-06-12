<h1>Upravit obchodního partnera</h1>

<ul>
	<li><?php echo $html->link('Zpět na seznam obchodních partnerů', array('controller' => 'business_partners', 'action' => 'index'))?></li>
</ul>

<?php echo $form->create('BusinessPartner', array('url' => array('controller' => 'business_partners', 'action' => 'edit')))?>
<table class="left_heading">
	<tr>
		<th>Název<sup>*</sup></th>
		<td><?php echo $form->input('BusinessPartner.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>IČO<sup>*</sup></th>
		<td><?php echo $form->input('BusinessPartner.ico', array('label' => false))?></td>
	</tr>
	<tr>
		<th>DIČ</th>
		<td><?php echo $form->input('BusinessPartner.dic', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Email</th>
		<td><?php echo $form->input('BusinessPartner.email', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Telefon</th>
		<td><?php echo $form->input('BusinessPartner.phone', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Aktivní</th>
		<td><?php echo $form->input('BusinessPartner.active', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $form->input('BusinessPartner.note', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Bonita</th>
		<td>
			<table>
				<tr>
					<th>&nbsp;</th>
					<th>A</th>
					<th>B</th>
					<th>C</th>
				</tr>
				<tr>
					<th>1</th>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="1"<?php echo ($this->data['BusinessPartner']['bonity'] == 1) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="4"<?php echo ($this->data['BusinessPartner']['bonity'] == 4) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="7"<?php echo ($this->data['BusinessPartner']['bonity'] == 7) ? ' checked ' : ''?>/></td>
				</tr>
				<tr>
					<th>2</th>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="2"<?php echo ($this->data['BusinessPartner']['bonity'] == 2) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="5"<?php echo ($this->data['BusinessPartner']['bonity'] == 5) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="8"<?php echo ($this->data['BusinessPartner']['bonity'] == 8) ? ' checked ' : ''?>/></td>
				</tr>
				<tr>
					<th>3</th>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="3"<?php echo ($this->data['BusinessPartner']['bonity'] == 3) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="6"<?php echo ($this->data['BusinessPartner']['bonity'] == 6) ? ' checked ' : ''?>/></td>
					<td><input type="radio" name="data[BusinessPartner][bonity]" value="9"<?php echo ($this->data['BusinessPartner']['bonity'] == 9) ? ' checked ' : ''?>/></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>Provozní doba</th>
		<td><?php echo $form->input('BusinessPartner.opening_hours', array('label' => false))?></td>
	</tr>
	<tr>		
		<td colspan="2">Adresa sídla</td>
	</tr>
	<tr>
		<th>Název<sup>*</sup></th>
		<td><?php echo $form->input('Address.0.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Jméno osoby</th>
		<td><?php echo $form->input('Address.0.person_first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení osoby</th>
		<td><?php echo $form->input('Address.0.person_last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Ulice</th>
		<td><?php echo $form->input('Address.0.street', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Číslo popisné<sup>*</sup></th>
		<td><?php echo $form->input('Address.0.number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Orientační číslo</th>
		<td><?php echo $form->input('Address.0.o_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město<sup>*</sup></th>
		<td><?php echo $form->input('Address.0.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ</th>
		<td><?php echo $form->input('Address.0.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Okres</th>
		<td><?php echo $form->input('Address.0.region', array('label' => false))?></td>
	</tr>
</table>

<?php
	echo $form->hidden('Address.0.address_type_id', array('value' => 1));
	echo $form->hidden('BusinessPartner.id');
	echo $form->hidden('Address.0.id');
	echo $form->submit('Uložit');
	echo $form->end();
?>

<ul>
	<li><?php echo $html->link('Zpět na seznam obchodních partnerů', array('controller' => 'business_partners', 'action' => 'index'))?></li>
</ul>