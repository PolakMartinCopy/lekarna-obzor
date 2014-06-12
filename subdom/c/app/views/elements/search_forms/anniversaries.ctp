<button id="search_form_show">vyhledávací formulář</button>

<?php
	$hide = ' style="display:none"';
	if ( isset($this->data) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>

	<?php echo $form->create('Anniversary', array('url' => array('controller' => 'anniversaries', 'action' => 'index')))?>
	<table class="left_heading">
		<tr>
			<th>Typ výročí</th>
			<td><?php echo $form->input('Anniversary.anniversary_type_id', array('label' => false, 'type' => 'select', 'options' => $anniversary_types, 'empty' => true))?></td>
			<th>Datum od</th>
			<td><?php echo $form->input('Anniversary.date_from', array('type' => 'text', 'label' => false))?></td>
			<th>Datum do</th>
			<td><?php echo $form->input('Anniversary.date_to', array('type' => 'text', 'label' => false))?></td>
			<th>Akce</th>
			<td><?php echo $form->input('Anniversary.anniversary_action_id', array('label' => false, 'type' => 'select', 'options' => $anniversary_actions, 'empty' => true))?></td>
		</tr>
		<tr>
			<td colspan="8">
				<?php
					echo $html->link('reset filtru', array('controller' => 'anniversaries', 'action' => 'index', 'reset' => 'anniversary'))
				?>
			</td>
		</tr>
	</table>
<?php
	echo $form->hidden('Anniversary.search_form', array('value' => 1));
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
		var dates = $( "#AnniversaryDateFrom, #AnniversaryDateTo" ).datepicker({
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "AnniversaryDateFrom" ? "minDate" : "maxDate",
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