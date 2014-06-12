<h1>Přidat typ adresy</h1>
<ul>
	<li><?php echo $html->link('Zpět na seznam typů adresy', array('controller' => 'address_types', 'action' => 'index'))?></li>
</ul>

<?php echo $form->create('AddressType', array('url' => array('controller' => 'address_types', 'action' => 'add')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('AddressType.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Jen jednou</th>
		<td><?php echo $form->input('AddressType.just_one', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>

<ul>
	<li><?php echo $html->link('Zpět na seznam typů adresy', array('controller' => 'address_types', 'action' => 'index'))?></li>
</ul>