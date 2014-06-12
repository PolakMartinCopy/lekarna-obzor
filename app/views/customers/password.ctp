<div class="mainContentWrapper">
	<p>Do pole prosím vložte Vaši emailovou adresu, kterou používáte ve spojitosti s účtem na www.<?php echo CUST_ROOT?></p>
	<?=$form->create('Customer', array('action' => 'password')) ?>
		<table id="form">
			<tr>
				<th>
					Emailová adresa:
				</th>
				<td>
					<?=$form->input('Customer.email', array('label' => false)) ?>
				</td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td>
					<?=$form->submit('ODESLAT')?>
				</td>
			</tr>
		</table>
	<?=$form->end() ?>
</div>