<h1>Seznam repů</h1>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['RepForm']) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>

	<?php echo $form->create('Rep', array('url' => array('controller' => 'reps', 'action' => 'index')))?>
	<table class="left_heading">
		<tr>
			<th>Křestní jméno</th>
			<td><?php echo $form->input('RepForm.Rep.first_name', array('label' => false))?></td>
			<th>Příjmení</th>
			<td><?php echo $form->input('RepForm.Rep.last_name', array('label' => false))?></td>
			<th>Login</th>
			<td><?php echo $form->input('RepForm.Rep.login', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Telefon</th>
			<td><?php echo $form->input('RepForm.Rep.phone', array('label' => false))?></td>
			<th>Email</th>
			<td><?php echo $form->input('RepForm.Rep.email', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">
				<?php
					echo $html->link('reset filtru', array('controller' => 'reps', 'action' => 'index', 'reset' => 'reps'))
				?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('RepForm.Rep.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$("#search_form_show").click(function () {
		if ($('#search_form').css('display') == "none"){
			$("#search_form").show("slow");
		} else {
			$("#search_form").hide("slow");
		}
	});
</script>

<?php 
echo $form->create('CSV', array('url' => array('controller' => 'reps', 'action' => 'xls_export')));
echo $form->hidden('data', array('value' => serialize($find)));
echo $form->hidden('fields', array('value' => serialize($export_fields)));
echo $form->submit('CSV');
echo $form->end();

if (empty($reps)) {
?>
<p><em>V databázi nejsou žádní uživatelé.</em></p>
<?php } else {?>
<table class="top_heading">
	<tr>
		<th><?php echo $paginator->sort('ID', 'Rep.id')?></th>
		<th><?php echo $paginator->sort('Křestní jméno', 'Rep.first_name')?></th>
		<th><?php echo $paginator->sort('Příjmení', 'Rep.last_name')?></th>
		<th><?php echo $paginator->sort('Telefon', 'Rep.phone')?></th>
		<th><?php echo $paginator->sort('Email', 'Rep.email')?></th>
		<th><?php echo $paginator->sort('Login', 'Rep.login')?></th>
		<th><?php echo $paginator->sort('Peněženka', 'Rep.wallet')?>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($reps as $rep) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $rep['Rep']['id']?></td>
		<td><?php echo $rep['Rep']['first_name']?></td>
		<td><?php echo $this->Html->link($rep['Rep']['last_name'], array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id']))?></td>
		<td><?php echo $rep['Rep']['phone']?></td>
		<td><?php echo $html->link($rep['Rep']['email'], 'mailto:' . $rep['Rep']['email'])?></td>
		<td><?php echo $rep['Rep']['login']?></td>
		<td><?php echo $rep['Rep']['wallet']?></td>
		<td class="actions"><?php
			echo $this->Html->link('Detail', array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'])) . ' ';
			echo $html->link('Upravit', array('controller' => 'reps', 'action' => 'edit', $rep['Rep']['id'])) . ' ';
			echo $html->link('Smazat', array('controller' => 'reps', 'action' => 'delete', $rep['Rep']['id']), null, 'Opravdu chcete uživatele ' . $rep['Rep']['first_name'] . ' ' . $rep['Rep']['last_name'] . ' smazat?') . ' ';
			echo $this->Html->link('Nabít', array('controller' => 'wallet_transaction', 'action' => 'add', 'rep_id' => $rep['Rep']['id'])) . ' ';
		?></td>
	</tr>
<?php } ?>
</table>

<?php 
echo $paginator->numbers();
echo $paginator->prev('« Předchozí ', null, null, array('class' => 'disabled'));
echo $paginator->next(' Další »', null, null, array('class' => 'disabled'));

} ?>