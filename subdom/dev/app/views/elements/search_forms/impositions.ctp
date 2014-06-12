<button id="search_form_show">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['ImpositionForm']) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>
	<?php echo $form->create('Imposition', array('url' => array('controller' => 'impositions', 'action' => 'index', $date))); ?>
	<table class="left_heading">
		<tr>
			<th>Obchodní partner</th>
			<td><?php echo $form->input('ImpositionForm.BusinessPartner.name', array('label' => false))?></td>
			<th>Zadavatel</th>
			<td><?php echo $form->input('ImpositionForm.Imposition.user_id', array('label' => false, 'type' => 'select', 'empty' => true, 'options' => $users))?></td>
			<th>Řešitel</th>
			<td><?php echo $form->input('ImpositionForm.ImpositionsUser.user_id', array('label' => false, 'type' => 'select', 'empty' => true, 'options' => $impositions_users))?></td>
		</tr>
		<tr>
			<th>Popis</th>
			<td><?php echo $form->input('ImpositionForm.Imposition.description', array('label' => false, 'type' => 'text'))?></td>
			<th>Od</th>
			<td><?php echo $form->input('ImpositionForm.Solution.accomplishment_date_from', array('label' => false, 'type' => 'text'))?></td>
			<th>Do</th>
			<td><?php echo $form->input('ImpositionForm.Solution.accomplishment_date_to', array('label' => false, 'type' => 'text'))?></td>
		</tr>
		<tr>
			<th>Stav</th>
			<td><?php echo $form->input('ImpositionForm.Solution.solution_state_id', array('label' => false, 'type' => 'select', 'options' => $solution_states, 'selected' => (isset($this->data['ImpositionForm']['Solution']['solution_state_id']) ? $this->data['ImpositionForm']['Solution']['solution_state_id'] : 2)))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php
					echo $html->link('reset filtru', array('controller' => 'impositions', 'action' => 'index', $date, 'reset' => 'impositions'))
				?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('ImpositionForm.Imposition.search_form', array('value' => 1));
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
	$(function() {
		var dates = $( "#ImpositionFormSolutionAccomplishmentDateFrom, #ImpositionFormSolutionAccomplishmentDateTo" ).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "ImpositionFormSolutionAccomplishmentDateFrom" ? "minDate" : "maxDate",
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