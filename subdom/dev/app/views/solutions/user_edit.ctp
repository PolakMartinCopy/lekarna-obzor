<script>
$(function() {
	var dates = $( "#SolutionAccomplishmentDate" ).datepicker({
		defaultDate: "+1w",
		changeMonth: false,
		numberOfMonths: 1,
		onSelect: function( selectedDate ) {
			var option = this.id == "SolutionAccomplishmentDate" ? "minDate" : "maxDate",
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

<h1>Editace požadavku na řešení</h1>

<?php echo $form->create('Solution', array('url' => array('controller' => 'solutions', 'action' => 'edit', $solution['Solution']['id'])))?>
<table class="left_heading">
	<tr>
		<th>Termín</th>
		<td><?php echo $form->input('Solution.accomplishment_date', array('label' => false, 'type' => 'text'))?></td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $form->input('Solution.note', array('label' => false, 'cols' => '100%', 'rows' => 15))?></td>
	</tr>
	<tr>
		<th>Stav</th>
		<td><?php echo $form->input('Solution.solution_state_id', array('label' => false, 'options' => $solution_states, 'empty' => false))?></td>
	</tr>
</table>
<?php
echo $form->hidden('Solution.back_link', array('value' => base64_encode(serialize($back_link))));
echo $form->hidden('Solution.id');
echo $form->submit('Uložit');
echo $form->end();
?>