<script>
	$(document).ready(function(){
		data = <?php echo $business_partners?>;
		$('input.ContactPersonBusinessPartnerName').each(function() {
			var autoCompelteElement = this;
			var formElementName = $(this).attr('name');
			var formElementId = $(this).attr('id');
			var hiddenElementID  = 'ContactPersonBusinessPartnerId';
			var hiddenElementName = 'data[ContactPerson][business_partner_id]';
			/* create new hidden input with name of orig input */
			$(this).after("<input type=\"hidden\" name=\"" + hiddenElementName + "\" id=\"" + hiddenElementID + "\" />");
			$(this).autocomplete({
				source: data, 
				select: function(event, ui) {
					var selectedObj = ui.item;
					$(autoCompelteElement).val(selectedObj.label);
					$('#'+hiddenElementID).val(selectedObj.value);
					return false;
				}
			});
		});
	});
</script>

<h1>Upravit kontaktní osobu</h1>
<ul>
<?php if (isset($business_partner_id)) { ?>
	<li><?php echo $html->link('Zpět na detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $business_partner_id))?></li>
<?php } else { ?>
	<li><?php echo $html->link('Zpět na seznam kontaktních osob', array('controller' => 'contact_people', 'action' => 'index'))?></li>
<?php } ?>
</ul>

<?php
if (isset($business_partner_id)) {
	echo $form->create('ContactPerson', array('url' => array('controller' => 'contact_people', 'action' => 'edit', 'business_partner_id' => $business_partner_id)));
} else {
	echo $form->create('ContactPerson', array('url' => array('controller' => 'contact_people', 'action' => 'edit')));
}
?>
<table class="left_heading">
	<tr>
		<th>Křestní jméno</th>
		<td><?php echo $form->input('ContactPerson.first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení<sup>*</sup></th>
		<td><?php echo $form->input('ContactPerson.last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Titul před</th>
		<td><?php echo $form->input('ContactPerson.prefix', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Titul za</th>
		<td><?php echo $form->input('ContactPerson.suffix', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Telefon<sup>*</sup></th>
		<td><?php echo $form->input('ContactPerson.phone', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Mobilní telefon</th>
		<td><?php echo $form->input('ContactPerson.cellular', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Email</th>
		<td><?php echo $form->input('ContactPerson.email', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Obchodní partner</th>
		<td><?php
			echo $form->input('ContactPerson.business_partner_name', array('label' => false, 'type' => 'text', 'class' => 'ContactPersonBusinessPartnerName'));
			echo $form->error('ContactPerson.business_partner_id');
			if (!empty($this->data['ContactPerson']['business_partner_id'])) {
				echo $form->hidden('ContactPerson.business_partner_id_old', array('value' => $this->data['ContactPerson']['business_partner_id']));
				$this->data['ContactPerson']['business_partner_id_old'] = $this->data['ContactPerson']['business_partner_id'];
			}
			if (!empty($this->data['ContactPerson']['business_partner_id_old'])) {
				echo $form->hidden('ContactPerson.business_partner_id_old', array('value' => $this->data['ContactPerson']['business_partner_id_old']));
			}
		 ?></td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $form->input('ContactPerson.note', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Koníčky</th>
		<td><?php echo $form->input('ContactPerson.hobby', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->hidden('ContactPerson.id')?>
<?php echo $form->submit('Upravit')?>
<?php echo $form->end()?>

<ul>
<?php if (isset($business_partner_id)) { ?>
	<li><?php echo $html->link('Zpět na detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $business_partner_id))?></li>
<?php } else { ?>
	<li><?php echo $html->link('Zpět na seznam kontaktních osob', array('controller' => 'contact_people', 'action' => 'index'))?></li>
<?php } ?>
</ul>