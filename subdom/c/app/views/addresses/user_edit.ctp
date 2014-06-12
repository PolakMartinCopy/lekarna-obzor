<h1>Editace adresy</h1>
<ul>
	<li><?php echo $html->link('Zpět na detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $business_partner_id))?>
</ul>

<?php echo $form->create('Address', array('url' => array('controller' => 'addresses', 'action' => 'edit')))?>
<table class="left_heading">
	<tr>
		<th>Název<sup>*</sup></th>
		<td><?php echo $form->input('Address.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Křestní jméno osoby</th>
		<td><?php echo $form->input('Address.person_first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení osoby</th>
		<td><?php echo $form->input('Address.person_last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Ulice<sup>*</sup></th>
		<td><?php echo $form->input('Address.street', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Číslo popisné<sup>*</sup></th>
		<td><?php echo $form->input('Address.number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Orientační číslo</th>
		<td><?php echo $form->input('Address.o_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město<sup>*</sup></th>
		<td><?php echo $form->input('Address.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ<sup>*</sup></th>
		<td><?php echo $form->input('Address.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Okres</th>
		<td><?php echo $form->input('Address.region', array('label' => false))?></td>
	</tr>
</table>
<?php
	echo $form->hidden('Address.id');
	echo $form->submit('Uložit');
	echo $form->end();
?>

<ul>
	<li><?php echo $html->link('Zpět na detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $business_partner_id))?>
</ul>