<button id="search_form_show_business_session">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['BusinessSessionSearch']) ){
		$hide = '';
	}
?>
<div id="search_form_business_session"<?php echo $hide?>>
	
	<?php echo $form->create('BusinessSession', array('url' => $_SERVER['REQUEST_URI'])); ?>
	<table class="left_heading">
		<tr>
			<th>Obchodní partner</th>
			<td><?php echo $form->input('BusinessSessionSearch.BusinessSession.business_partner_name', array('label' => false, 'type' => 'text'))?></td>
			<th>Datum od</th>
			<td><?php echo $form->input('BusinessSessionSearch.BusinessSession.date_from', array('label' => false, 'type' => 'text'))?></td>
			<th>Datum do</th>
			<td><?php echo $form->input('BusinessSessionSearch.BusinessSession.date_to', array('label' => false, 'type' => 'text'))?></td>
		</tr>
		<tr>
			<th>Typ jednání</th>
			<td><?php echo $form->input('BusinessSessionSearch.BusinessSession.business_session_type_id', array('options' => $business_session_types, 'empty' => true, 'label' => false))?></td>
			<th>Popis</th>
			<td><?php echo $form->input('BusinessSessionSearch.BusinessSession.description', array('label' => false, 'type' => 'text'))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">
				<?php
					echo $html->link('reset filtru', $_SERVER['REQUEST_URI'] . '/reset:business_session')
				?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('BusinessSessionSearch.BusinessSession.search_form', array('value' => 1));
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
		var dates = $( "#BusinessSessionSearchBusinessSessionDateFrom, #BusinessSessionSearchBusinessSessionDateTo" ).datepicker({
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "BusinessSessionSearchBusinessSessionDateFrom" ? "minDate" : "maxDate",
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