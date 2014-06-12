<h1>Upravit způsob platby</h1>
<?php echo $form->create('Payment', array('url' => '/admin/payments/edit/' . $id)) ?>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>Název</th>
		<td><?=$form->input('Payment.name', array('label' => false, 'size' => 90)) ?></td>
	</tr>
</table>
<?=$form->hidden('Payment.id') ?>
<?=$form->submit('Uložit') ?>
<?=$form->end() ?>