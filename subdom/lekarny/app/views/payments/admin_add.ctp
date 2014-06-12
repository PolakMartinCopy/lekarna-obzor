<h1>Vložení nového způsobu platby</h1>
<?=$form->create('Payment') ?>
<?php echo $form->create('Payment') ?>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>Název</th>
		<td><?=$form->input('Payment.name', array('label' => false, 'size' => 90)) ?></td>
	</tr>
</table>
<?=$form->submit('Uložit') ?>
<?=$form->end() ?>