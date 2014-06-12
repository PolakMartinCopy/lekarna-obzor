<script>
	$(document).ready(function(){
		data = <?php echo $users?>;
		$('input.BusinessPartnerUserName').each(function() {
			var autoCompelteElement = this;
			var formElementName = $(this).attr('name');
			var formElementId = $(this).attr('id');
			var hiddenElementID  = 'BusinessPartnerUserId';
			var hiddenElementName = 'data[BusinessPartner][user_id]';
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

<h1>Upravit uživatele</h1>
<?php echo $form->create('BusinessPartner', array('url' => array('controller' => 'business_partners', 'action' => 'edit_user', $business_partner['BusinessPartner']['id'])))?>
<table class="left_heading">
	<tr>
		<th>Uživatel</th>
		<td><?php echo $form->input('BusinessPartner.user_name', array('label' => false, 'type' => 'text', 'class' => 'BusinessPartnerUserName'))?></td>
	</tr>
</table>
<?php
echo $form->hidden('BusinessPartner.id', array('value' => $business_partner['BusinessPartner']['id']));
echo $form->submit('Upravit');
echo $form->end();
?>