<script type="text/javascript">
$(function() {
	$('#WalletTransactionRepName').autocomplete({
		delay: 500,
		minLength: 2,
		source: '/user/reps/autocomplete_list',
		select: function(event, ui) {
			$('#WalletTransactionRepName').val(ui.item.label);
			$('#WalletTransactionRepId').val(ui.item.value);
			return false;
		}
	});
});
</script>

<h1>Dobít peněženku</h1>
<?php echo $this->Form->create('WalletTransaction', array('url' => $this->passedArgs))?>
<table class="left_heading">
	<tr>
		<th>Rep</th>
		<td><?php
			if (isset($rep)) {
				echo $this->Form->input('WalletTransaction.rep_name', array('label' => false, 'value' => $rep['Rep']['name'], 'disabled' => true));
				echo $this->Form->hidden('WalletTransaction.rep_id', array('value' => $rep['Rep']['id']));
			} else {
				echo $this->Form->input('WalletTransaction.rep_name', array('label' => false));
				echo $this->Form->error('WalletTransaction.rep_id');
				echo $this->Form->hidden('WalletTransaction.rep_id');
			}
		?></td>
	</tr>
	<tr>
		<th>Částka</th>
		<td><?php echo $this->Form->input('WalletTransaction.amount', array('label' => false))?></td>
	</tr>
</table>
<?php 
	echo $this->Form->hidden('WalletTransaction.user_id', array('value' => $user['User']['id']));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end()
?>