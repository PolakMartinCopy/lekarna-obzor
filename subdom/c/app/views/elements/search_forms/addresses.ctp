<button id="search_form_show_addresses">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['AddressSearch']) ){
		$hide = '';
	}
?>
<div id="search_form_addresses"<?php echo $hide?>>
	<?php echo $form->create('Address', array('url' => $_SERVER['REQUEST_URI'])); ?>
	<table class="left_heading">
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('AddressSearch.Address.name', array('label' => false))?></td>
			<th>Jméno osoby</th>
			<td><?php echo $form->input('AddressSearch.Address.person_first_name', array('label' => false))?></td>
			<th>Příjmení osoby</th>
			<td><?php echo $form->input('AddressSearch.Address.person_last_name', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $form->input('AddressSearch.Address.street', array('label' => false))?></td>
			<th>Č. p.</th>
			<td><?php echo $form->input('AddressSearch.Address.number', array('label' => false))?></td>
			<th>O. č.</th>
			<td><?php echo $form->input('AddressSearch.Address.o_number', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Město</th>
			<td><?php echo $form->input('AddressSearch.Address.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $form->input('AddressSearch.Address.zip', array('label' => false))?></td>
			<th>Okres</th>
			<td><?php echo $form->input('AddressSearch.Address.region', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6"><?php echo $html->link('reset filtru', $_SERVER['REQUEST_URI'] . '/reset:address')?></td>
		</tr>
	</table>
	<?php
		echo $form->hidden('AddressSearch.Address.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script>
	$("#search_form_show_addresses").click(function () {
		if ($('#search_form_addresses').css('display') == "none"){
			$("#search_form_addresses").show("slow");
		} else {
			$("#search_form_addresses").hide("slow");
		}
	});
</script>