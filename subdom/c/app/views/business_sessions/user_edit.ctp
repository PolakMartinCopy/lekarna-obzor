<script>
	$(document).ready(function(){
		data = <?php echo $business_partners?>;
		$('input.BusinessSessionBusinessPartnerName').each(function() {
			var autoCompelteElement = this;
			var formElementName = $(this).attr('name');
			var formElementId = $(this).attr('id');
			var hiddenElementID  = 'BusinessSessionBusinessPartnerId';
			var hiddenElementName = 'data[BusinessSession][business_partner_id]';
			/* create new hidden input with name of orig input */
			$(this).after("<input type=\"hidden\" name=\"" + hiddenElementName + "\" id=\"" + hiddenElementID + "\" />");
			$(this).autocomplete({
				source: data, 
				select: function(event, ui) {
					var selectedObj = ui.item;
					$(autoCompelteElement).val(selectedObj.label);
					$('#'+hiddenElementID).val(selectedObj.value);
					return false;
				}
			});
		});
	});

	$(function() {
		var dates = $( "#BusinessSessionDate" ).datepicker({
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "BusinessSessionDate" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
</script>

<h1>Upravit obchodní jednání <?php echo $business_session['BusinessSession']['id']?></h1>

<?php echo $form->create('BusinessSession', array('url' => array('controller' => 'business_sessions', 'action' => 'edit')))?>
<table class="left_heading">
	<tr>
		<th>Obchodní partner</th>
		<td>
		<?php
			echo $form->input('BusinessSession.business_partner_name', array('label' => false, 'type' => 'text', 'class' => 'BusinessSessionBusinessPartnerName'));
			echo $form->error('BusinessSession.business_partner_id');
		?>
		</td>
	</tr>
	<tr>
		<th>Datum uskutečnění</th>
		<td>
			<?php echo $form->input('BusinessSession.date', array('type' => 'text', 'label' => false, 'div' => false))?>
			<?php echo $form->input('BusinessSession.time', array('type' => 'time', 'timeFormat' => '24', 'label' => false))?>
		</td>
	</tr>
	<tr>
		<th>Typ jednání</th>
		<td><?php echo $form->input('BusinessSession.business_session_type_id', array('options' => $business_session_types, 'empty' => false, 'label' => false))?></td>
	</tr>
	<tr>
		<th>Popis</th>
		<td><?php echo $form->input('BusinessSession.description', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Pozvaní uživatelé</th>
		<td><?php echo $form->input('BusinessSessionsUser.user_id', array('options' => $users, 'multiple' => true, 'label' => false, 'empty' => false))?></td>
	</tr>
</table>

<?php
	echo $form->hidden('BusinessSession.id');
	echo $form->submit('Upravit');
	echo $form->end();
?>