<button id="search_form_show_costs">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data) ){
		$hide = '';
	}
?>
<div id="search_form_costs"<?php echo $hide?>>
	<?php echo $form->create('Cost', array('url' => $_SERVER['REQUEST_URI'])); ?>
	<table class="left_heading">
		<tr>
			<th>Datum</th>
			<td><?php echo $form->input('Cost.date', array('label' => false, 'type' => 'text'))?></td>
			<th>Popis</th>
			<td><?php echo $form->input('Cost.description', array('label' => false, 'type' => 'text'))?></td>
		</tr>
		<tr>
			<th>Částka od</th>
			<td><?php echo $form->input('Cost.amount_from', array('label' => false))?></td>
			<th>Částka do</th>
			<td><?php echo $form->input('Cost.amount_to', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $html->link('reset filtru', $_SERVER['REQUEST_URI'])?></td>
		</tr>
	</table>
	<?php
		echo $form->hidden('Cost.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script>
	$("#search_form_show_costs").click(function () {
		if ($('#search_form_costs').css('display') == "none"){
			$("#search_form_costs").show("slow");
		} else {
			$("#search_form_costs").hide("slow");
		}
	});

	$(function() {
		var dates = $( "#CostDate" ).datepicker({
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "CostDate" ? "minDate" : "maxDate",
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