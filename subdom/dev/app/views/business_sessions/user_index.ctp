<h1>Obchodní jednání</h1>

<button id="search_form_show_business_session">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['BusinessSessionSearch2']) ){
		$hide = '';
	}
?>
	<div id="search_form_business_session"<?php echo $hide?>>
		
		<?php echo $form->create('BusinessSession', array('url' => $_SERVER['REQUEST_URI'])); ?>
		<table class="left_heading">
			<tr>
				<th>Obchodní partner</th>
				<td><?php echo $form->input('BusinessSessionSearch2.BusinessSession.business_partner_name', array('label' => false, 'type' => 'text'))?></td>
				<th>Datum jednání od</th>
				<td><?php echo $form->input('BusinessSessionSearch2.BusinessSession.date_from', array('label' => false, 'type' => 'text'))?></td>
				<th>Datum jednání do</th>
				<td><?php echo $form->input('BusinessSessionSearch2.BusinessSession.date_to', array('label' => false, 'type' => 'text'))?></td>
			</tr>
			<tr>
				<th>Popis</th>
				<td><?php echo $form->input('BusinessSessionSearch2.BusinessSession.description', array('label' => false, 'type' => 'text'))?></td>
				<th>Datum vložení od</th>
				<td><?php echo $form->input('BusinessSessionSearch2.BusinessSession.created_from', array('label' => false, 'type' => 'text'))?></td>
				<th>Datum vložení do</th>
				<td><?php echo $form->input('BusinessSessionSearch2.BusinessSession.created_to', array('label' => false, 'type' => 'text'))?></td>
			</tr>
			<tr colspan>
				<th>Typ jednání</th>
				<td><?php echo $form->input('BusinessSessionSearch2.BusinessSession.business_session_type_id', array('options' => $business_session_types, 'empty' => true, 'label' => false))?></td>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="6">
					<?php
						echo $html->link('reset filtru', array('controller' => 'business_sessions', 'action' => 'index', 'reset' => 'business_sessions'));
					?>
				</td>
			</tr>
		</table>
		<?php
			echo $form->hidden('BusinessSessionSearch2.BusinessSession.search_form', array('value' => 1));
			echo $form->submit('Vyhledávat');
			echo $form->end();
		?>
	</div>
	
	<script>
		$("#search_form_show_business_session").click(function () {
			if ($('#search_form_business_session').css('display') == "none"){
				$("#search_form_business_session").show("slow");
			} else {
				$("#search_form_business_session").hide("slow");
			}
		});
		$(function() {
			var dates = $( "#BusinessSessionSearch2BusinessSessionDateFrom, #BusinessSessionSearch2BusinessSessionDateTo").datepicker({
				defaultDate: "+1w",
				changeMonth: false,
				numberOfMonths: 1,
				onSelect: function( selectedDate ) {
					var option = this.id == "BusinessSessionSearch2BusinessSessionDateFrom" ? "minDate" : "maxDate",
						instance = $( this ).data( "datepicker" ),
						date = $.datepicker.parseDate(
							instance.settings.dateFormat ||
							$.datepicker._defaults.dateFormat,
							selectedDate, instance.settings );
					dates.not( this ).datepicker( "option", option, date );
				}
			});

			var dates2 = $( "#BusinessSessionSearch2BusinessSessionCreatedFrom, #BusinessSessionSearch2BusinessSessionCreatedTo" ).datepicker({
				defaultDate: "+1w",
				changeMonth: false,
				numberOfMonths: 1,
				onSelect: function( selectedDate ) {
					var option = this.id == "BusinessSessionSearch2BusinessSessionCreatedFrom" ? "minDate" : "maxDate",
						instance = $( this ).data( "datepicker" ),
						date = $.datepicker.parseDate(
							instance.settings.dateFormat ||
							$.datepicker._defaults.dateFormat,
							selectedDate, instance.settings );
					dates2.not( this ).datepicker( "option", option, date );
				}
			}); 
		});
		$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
	</script>
<?php	
	echo $form->create('CSV', array('url' => array('controller' => 'business_sessions', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
	
	if (empty($business_sessions)) {
		$message = 'V databázi nejsou žádná obchodní jednání';
?>
<p><em><?php echo $message?>.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $paginator->sort('ID', 'BusinessSession.id')?></th>
		<th><?php echo $paginator->sort('Datum jednání', 'BusinessSession.date')?></th>
		<th><?php echo $paginator->sort('Obchodní partner', 'BusinessPartner.name')?></th>
		<th><?php echo $paginator->sort('Typ jednání', 'BusinessSessionType.name')?></th>
		<th><?php echo $paginator->sort('Stav jednání', 'BusinessSessionState.name')?></th>
		<th><?php echo $paginator->sort('Datum vložení', 'BusinessSession.created')?></th>
		<th><?php echo $paginator->sort('Založil', 'User.last_name')?></th>
		<th><?php echo $paginator->sort('Náklady', 'celkem')?></th>
		<th>Nabídka</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($business_sessions as $business_session) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $business_session['BusinessSession']['id']?></td>
		<td><?php echo $business_session['BusinessSession']['date']?></td>
		<td><?php echo $html->link($business_session['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $business_session['BusinessPartner']['id']))?></td>
		<td><?php echo $business_session['BusinessSessionType']['name']?></td>
		<td><?php echo $business_session['BusinessSessionState']['name']?></td>
		<td><?php echo $business_session['BusinessSession']['created']?></td>
		<td><?php echo $business_session['User']['last_name']?></td>
		<td><?php echo floatval($business_session[0]['celkem'])?></td>
		<td><?php echo (empty($business_session['Offer']) ? 'ne' : 'ano')?></td>
		<td class="actions">
			<?php echo $html->link('Detail', array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id']))?>
			<?php echo $html->link('Upravit', array('controller' => 'business_sessions', 'action' => 'edit', $business_session['BusinessSession']['id']))?>
			<?php echo $html->link('Uzavřít', array('controller' => 'business_sessions', 'action' => 'close', $business_session['BusinessSession']['id']), null, 'Opravdu chcete obchnodní jednání ' . $business_session['BusinessSession']['id'] . ' označit jako uzavřené?')?>
			<?php echo $html->link('Storno', array('controller' => 'business_sessions', 'action' => 'storno', $business_session['BusinessSession']['id']), null, 'Opravdu chcete obchodní jednání ' . $business_session['BusinessSession']['id'] . ' stornovat?')?>
			<?php echo $this->Html->link('Smazat', array('controller' => 'business_sessions', 'action' => 'delete', $business_session['BusinessSession']['id']), null, 'Opravdu chcete obchodní jednání ' . $business_session['BusinessSession']['id'] . ' smazat?')?>
		</td>
	</tr>
<?php } ?>
</table>

<?php
echo $paginator->numbers();
echo $paginator->prev('« Předchozí ', null, null, array('class' => 'disabled'));
echo $paginator->next(' Další »', null, null, array('class' => 'disabled'));
?>

<?php } ?>