<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSCreditNote']) ){
		$hide = '';
	}
?>
<div id="search_form_c_s_credit_notes"<?php echo $hide?>>
	<?php echo $form->create('CSCreditNote', array('url' => $url))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Odběratel</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $form->input('BusinessPartner.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $form->input('BusinessPartner.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $form->input('BusinessPartner.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('Address.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('Address.city', array('label' => false))?></td>
			<th>Okres</th>
			<td><?php echo $this->Form->input('Address.region', array('label' => false))?></td>
		</tr>
		<tr>
			<td>Dobropis</td>
		</tr>
		<tr>
			<th>Číslo dokladu</th>
			<td><?php echo $this->Form->input('CSCreditNote.code', array('label' => false))?></td>
			<th>Jazyk</th>
			<td><?php echo $this->Form->input('CSCreditNote.language_id', array('label' => false, 'empty' => true))?></td>
			<th>Měna</th>
			<td><?php echo $this->Form->input('CSCreditNote.currency_id', array('label' => false, 'empty' => true))?></td>
		</tr>
		</tr>
			<th>Obchodník</th>
			<td><?php echo $this->Form->input('CSCreditNote.user_id', array('label' => false, 'empty' => true))?></td>
			<th>Datum vystavení od</th>
			<td><?php echo $this->Form->input('CSCreditNote.date_of_issue_from', array('label' => false))?></td>
			<th>Datum vystavení do</th>
			<td><?php echo $this->Form->input('CSCreditNote.date_of_issue_to', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Datum splatnosti od</th>
			<td><?php echo $this->Form->input('CSCreditNote.due_date_from', array('label' => false))?></td>
			<th>Datum splatnosti do</th>
			<td><?php echo $this->Form->input('CSCreditNote.due_date_to', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td>Zboží</td>
		</tr>
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('Product.vzp_code', array('label' => false))?></td>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('Product.referential_number', array('label' => false))?></td>
		</tr>
		<tr>
			<th>LOT</th>
			<td><?php echo $this->Form->input('ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('ProductVariant.exp', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6"><?php
				$reset_url = $url + array('reset' => 'c_s_credit_notes');
				echo $html->link('reset filtru', $reset_url);
			?></td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('CSCreditNote.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$(function() {
		var model = 'CSCreditNote';
		var dateOfIssueFromId = model + 'DateOfIssueFrom';
		var dateOfIssueToId = model + 'DateOfIssueTo';
		var datesOfIssue = $('#' + dateOfIssueFromId + ',#' + dateOfIssueToId).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == dateOfIssueFromId ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				datesOfIssue.not( this ).datepicker( "option", option, date );
			}
		});
		
		var dueDateFromId = model + 'DueDateFrom';
		var dueDateToId = model + 'DueDateTo';
		var dueDates = $('#' + dueDateFromId + ',#' + dueDateToId).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == dueDateFromId ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dueDates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
</script>