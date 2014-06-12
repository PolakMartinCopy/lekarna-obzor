<h1>Upravit způsob dopravy</h1>
<?=$form->create('Shipping', array('url' => '/admin/shippings/edit/' . $id)) ?>
<?php echo $form->create('Shipping', array('url' => '/admin/shippings/edit/' . $id)) ?>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>Název</th>
		<td><?=$form->input('Shipping.name', array('label' => false, 'size' => 90)) ?></td>
	</tr>
	<tr>
		<th>Cena</th>
		<td><?=$form->input('Shipping.price', array('label' => false, 'size' => 90)) ?></td>
	</tr>
	<tr>
		<th>Doprava zdarma od</th>
		<td><?=$form->input('Shipping.free', array('label' => false, 'size' => 90)) ?></td>
	</tr>
	<tr>
		<th>Tracker pre</th>
		<td><?=$form->input('Shipping.tracker_prefix', array('label' => false, 'size' => 90)) ?></td>
	</tr>
	<tr>
		<th>Tracker post</th>
		<td><?=$form->input('Shipping.tracker_postfix', array('label' => false, 'size' => 90)) ?></td>
	</tr>
</table>
<?=$form->hidden('Shipping.id') ?>
<?=$form->submit('Uložit') ?>
<?=$form->end() ?>