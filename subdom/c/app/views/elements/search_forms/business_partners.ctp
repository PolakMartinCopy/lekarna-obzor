<button id="search_form_show">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['BusinessPartner']) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>

	<?php echo $form->create('BusinessPartner', array('url' => array('controller' => 'business_partners', 'action' => 'index')))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Společnost</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('BusinessPartner.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $form->input('BusinessPartner.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $form->input('BusinessPartner.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Poznámka</th>
			<td><?php echo $form->input('BusinessPartner.note', array('label' => false))?></td>
			<th>Bonita</th>
			<td colspan="6">
				<table>
					<tr>
						<th>&nbsp;</th>
						<th>A</th>
						<th>B</th>
						<th>C</th>
					</tr>
					<tr>
						<th>1</th>
						<td><input type="checkbox" name="data[BusinessPartner][bonity][1]" value="1"<?php echo (isset($this->data['BusinessPartner']['bonity'][1]) ) ? ' checked ' : ''?>/></td>
						<td><input type="checkbox" name="data[BusinessPartner][bonity][4]" value="4"<?php echo (isset($this->data['BusinessPartner']['bonity'][4]) ) ? ' checked ' : ''?>/></td>
						<td><input type="checkbox" name="data[BusinessPartner][bonity][7]" value="7"<?php echo (isset($this->data['BusinessPartner']['bonity'][7]) ) ? ' checked ' : ''?>/></td>
					</tr>
					<tr>
						<th>2</th>
						<td><input type="checkbox" name="data[BusinessPartner][bonity][2]" value="2"<?php echo (isset($this->data['BusinessPartner']['bonity'][2]) ) ? ' checked ' : ''?>/></td>
						<td><input type="checkbox" name="data[BusinessPartner][bonity][5]" value="5"<?php echo (isset($this->data['BusinessPartner']['bonity'][5]) ) ? ' checked ' : ''?>/></td>
						<td><input type="checkbox" name="data[BusinessPartner][bonity][8]" value="8"<?php echo (isset($this->data['BusinessPartner']['bonity'][8]) ) ? ' checked ' : ''?>/></td>
					</tr>
					<tr>
						<th>3</th>
						<td><input type="checkbox" name="data[BusinessPartner][bonity][3]" value="3"<?php echo (isset($this->data['BusinessPartner']['bonity'][3]) ) ? ' checked ' : ''?>/></td>
						<td><input type="checkbox" name="data[BusinessPartner][bonity][6]" value="6"<?php echo (isset($this->data['BusinessPartner']['bonity'][6]) ) ? ' checked ' : ''?>/></td>
						<td><input type="checkbox" name="data[BusinessPartner][bonity][9]" value="9"<?php echo (isset($this->data['BusinessPartner']['bonity'][9]) ) ? ' checked ' : ''?>/></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="6">Adresa sídla</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('Address.name', array('label' => false))?></td>
			<th>Jméno osoby</th>
			<td><?php echo $form->input('Address.person_first_name', array('label' => false))?></td>
			<th>Příjmení osoby</th>
			<td><?php echo $form->input('Address.person_last_name', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $form->input('Address.street', array('label' => false))?></td>
			<th>Číslo popisné</th>
			<td><?php echo $form->input('Address.number', array('label' => false))?></td>
			<th>Orientační číslo</th>
			<td><?php echo $form->input('Address.o_number', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Město</th>
			<td><?php echo $form->input('Address.city', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $form->input('Address.zip', array('label' => false))?></td>
			<th>Okres</th>
			<td><?php echo $form->input('Address.region', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php
					echo $html->link('reset filtru', array('controller' => 'business_partners', 'reset' => true))
				?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('BusinessPartner.search_form', array('value' => 1));
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
</script>