<button id="search_form_show_contact_people">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['ContactPersonSearch']) ){
		$hide = '';
	}
?>
<div id="search_form_contact_people"<?php echo $hide?>>
	<?php echo $form->create('ContactPerson', array('url' => $_SERVER['REQUEST_URI'])); ?>
	<table class="left_heading">
		<tr>
			<th>Titul</th>
			<td><?php echo $form->input('ContactPersonSearch.ContactPerson.prefix', array('label' => false))?></td>
			<th>Jméno</th>
			<td><?php echo $form->input('ContactPersonSearch.ContactPerson.first_name', array('label' => false))?></td>
			<th>Příjmení</th>
			<td><?php echo $form->input('ContactPersonSearch.ContactPerson.last_name', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Telefon</th>
			<td><?php echo $form->input('ContactPersonSearch.ContactPerson.phone', array('label' => false))?></td>
			<th>Mobil</th>
			<td><?php echo $form->input('ContactPersonSearch.ContactPerson.cellular', array('label' => false))?></td>
			<th>Email</th>
			<td><?php echo $form->input('ContactPersonSearch.ContactPerson.email', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Obchodní partner</th>
			<td><?php echo $form->input('ContactPersonSearch.BusinessPartner.name', array('label' => false))?></td>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', $_SERVER['REQUEST_URI'] . '/reset:contact_people') ?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('ContactPersonSearch.ContactPerson.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script>
	$("#search_form_show_contact_people").click(function () {
		if ($('#search_form_contact_people').css('display') == "none"){
			$("#search_form_contact_people").show("slow");
		} else {
			$("#search_form_contact_people").hide("slow");
		}
	});
</script>