<h1>Moje oblasti</h1>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['UserRegionForm']) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>

	<?php echo $form->create('UserRegion', array('url' => array('controller' => 'user_regions', 'action' => 'index')))?>
	<table class="left_heading">
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('UserRegionForm.UserRegion.name', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $form->input('UserRegionForm.UserRegion.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Jméno uživatele</th>
			<td><?php echo $form->input('UserRegionForm.User.first_name', array('label' => false))?></td>
			<th>Příjmení uživatele</th>
			<td><?php echo $form->input('UserRegionForm.User.last_name', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="4">
				<?php echo $html->link('reset filtru', array('controller' => 'user_regions', 'action' => 'index', 'reset' => 'user_regions')) ?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('UserRegionForm.UserRegion.search_form', array('value' => 1));
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

<?php
echo $form->create('CSV', array('url' => array('controller' => 'user_regions', 'action' => 'xls_export')));
echo $form->hidden('data', array('value' => serialize($find)));
echo $form->hidden('fields', array('value' => serialize($export_fields)));
echo $form->submit('CSV');
echo $form->end();

if (empty($regions)) {
?>
<p><em>Nemáte přiděleny žádné oblasti.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>PSČ</th>
		<th>Uživatel</th>
<?php
		if ($acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/UserRegions/edit') ||
			$acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/UserRegions/delete')
		) { ?>
		<th>&nbsp;</th>
<?php 	} ?>
	</tr>
<?php
	$odd = '';
	foreach ($regions as $region) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $region['UserRegion']['id']?></td>
		<td><?php echo $region['UserRegion']['name']?></td>
		<td><?php echo $region['UserRegion']['zip']?></td>
		<td><?php echo $region['User']['last_name'] . ' ' . $region['User']['first_name']?></td>
		<td>
<?php 
			if ($acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/UserRegions/edit')) {
				echo $html->link('Upravit', array('controller' => 'user_regions', 'action' => 'edit', $region['UserRegion']['id']));
			}
			if ($acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/UserRegions/delete')) {
				echo ' ' .  $html->link('Smazat', array('controller' => 'user_regions', 'action' => 'delete', $region['UserRegion']['id']), null, 'Opravdu chcete oblast smazat?');
			}
?>			
		</td>
	</tr>
<?php } ?>
</table>
<?php } ?>