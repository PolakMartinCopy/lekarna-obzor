<button id="search_form_show_offers">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data) ){
		$hide = '';
	}
?>
<div id="search_form_offers"<?php echo $hide?>>
	<?php echo $form->create('Offer', array('url' => $_SERVER['REQUEST_URI'])); ?>
	<table class="left_heading">
		<tr>
			<th>Datum vytvoření</th>
			<td><?php echo $form->input('Offer.created', array('label' => false, 'type' => 'text'))?></td>
			<th>Obsah</th>
			<td><?php echo $form->input('Offer.content', array('label' => false, 'type' => 'text'))?></td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $html->link('reset filtru', $_SERVER['REQUEST_URI'])?></td>
		</tr>
	</table>
	<?php
		echo $form->hidden('Offer.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script>
	$("#search_form_show_offers").click(function () {
		if ($('#search_form_offers').css('display') == "none"){
			$("#search_form_offers").show("slow");
		} else {
			$("#search_form_offers").hide("slow");
		}
	});

	$(function() {
		var dates = $( "#OfferCreated" ).datepicker({
			defaultDate: "+1w",
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == "OfferCreated" ? "minDate" : "maxDate",
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