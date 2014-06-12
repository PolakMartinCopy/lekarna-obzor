<script>
	$(document).ready(function(){
		data = <?php echo $users?>;
		$('input.UserRegionUserName').each(function() {
			var autoCompelteElement = this;
			var formElementName = $(this).attr('name');
			var formElementId = $(this).attr('id');
			var hiddenElementID  = 'UserRegionUserId';
			var hiddenElementName = 'data[UserRegion][user_id]';
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

<h1>Upravit oblast</h1>

<?php echo $form->create('UserRegion', array('url' => array('controller' => 'user_regions', 'action' => 'edit')))?>
<table class="left_heading">
	<tr>
		<th>Uživatel</th>
		<td><?php
			echo $form->input('UserRegion.user_name', array('label' => false, 'type' => 'text', 'class' => 'UserRegionUserName'));
			echo $form->error('UserRegion.user_id');
			if (!empty($this->data['UserRegion']['user_id'])) {
				echo $form->hidden('UserRegion.user_id_old', array('value' => $this->data['UserRegion']['user_id']));
				$this->data['UserRegion']['user_id_old'] = $this->data['UserRegion']['user_id'];
			}
			if (!empty($this->data['UserRegion']['user_id_old'])) {
				echo $form->hidden('UserRegion.user_id_old', array('value' => $this->data['UserRegion']['user_id_old']));
			}
		 ?></td>
	</tr>
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('UserRegion.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ</th>
		<td><?php echo $form->input('UserRegion.zip', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->hidden('UserRegion.id')?>
<?php echo $form->submit('Upravit')?>
<?php echo $form->end() ?>