<button id="search_form_show_documents">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data) ){
		$hide = '';
	}
?>
<div id="search_form_documents"<?php echo $hide?>>
	<?php echo $form->create('Document', array('url' => $_SERVER['REQUEST_URI'])); ?>
	<table class="left_heading">
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('Document.title', array('label' => false))?></td>
			<th>Vloženo</th>
			<td><?php echo $form->input('Document.created', array('label' => false, 'type' => 'text'))?></td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $html->link('reset filtru', $_SERVER['REQUEST_URI'])?></td>
		</tr>
	</table>
	<?php
		echo $form->hidden('Document.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script>
	$("#search_form_show_documents").click(function () {
		if ($('#search_form_documents').css('display') == "none"){
			$("#search_form_documents").show("slow");
		} else {
			$("#search_form_documents").hide("slow");
		}
	});

	$(function() {
		var dates = $( "#DocumentCreated" ).datepicker({
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "DocumentCreated" ? "minDate" : "maxDate",
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