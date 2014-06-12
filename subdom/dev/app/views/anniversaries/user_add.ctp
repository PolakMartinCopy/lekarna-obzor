<script>
$(function() {
	var dates = $( "#AnniversaryDate" ).datepicker({
		defaultDate: "+1w",
		changeMonth: false,
		numberOfMonths: 1,
		onSelect: function( selectedDate ) {
			var option = this.id == "#AnniversaryDate" ? "minDate" : "maxDate",
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

<h1>Přidat výročí ke kontaktní osobě</h1>
<div class="actions">
	<ul>
		<li><?php echo $html->link('Detail kontaktní osoby', array('controller' => 'contact_people', 'action' => 'view', $contact_person_id))?>
	</ul>
</div>

<?php echo $form->create('Anniversary', array('url' => array('controller' => 'anniversaries', 'action' => 'add', 'contact_person_id' => $contact_person_id)))?>
<table class="left_heading">
	<tr>
		<th>Typ výročí</th>
		<td><?php echo $form->input('Anniversary.anniversary_type_id', array('label' => false, 'type' => 'select', 'options' => $anniversary_types, 'empty' => false))?></td>
	</tr>
	<tr>
		<th>Datum</th>
		<td><?php echo $form->input('Anniversary.date', array('label' => false, 'type' => 'text', 'value' => isset($this->data['Anniversary']['date']) ? $this->data['Anniversary']['date'] : date('d.m.Y')))?></td>
	</tr>
	<tr>
		<th>Akce</th>
		<td><?php echo $form->input('Anniversary.anniversary_action_id', array('label' => false, 'type' => 'select', 'options' => $anniversary_actions, 'empty' => false))?></td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $form->input('Anniversary.note', array('label' => false, 'size' => '75'))?></td>
	</tr>
</table>
<?php echo $form->hidden('Anniversary.contact_person_id', array('value' => $contact_person_id))?>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>