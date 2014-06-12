<h1>Seznam uživatelů</h1>
<button id="search_form_show">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['UserForm']) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>

	<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'index')))?>
	<table class="left_heading">
		<tr>
			<th>Křestní jméno</th>
			<td><?php echo $form->input('UserForm.User.first_name', array('label' => false))?></td>
			<th>Příjmení</th>
			<td><?php echo $form->input('UserForm.User.last_name', array('label' => false))?></td>
			<th>Login</th>
			<td><?php echo $form->input('UserForm.User.login', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Telefon</th>
			<td><?php echo $form->input('UserForm.User.phone', array('label' => false))?></td>
			<th>Email</th>
			<td><?php echo $form->input('UserForm.User.email', array('label' => false))?></td>
			<th>Typ</th>
			<td><?php echo $form->input('UserForm.User.user_type_id', array('options' => $user_types, 'empty' => true, 'label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php
					echo $html->link('reset filtru', array('controller' => 'users', 'action' => 'index', 'reset' => 'users'))
				?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('UserForm.User.search_form', array('value' => 1));
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
echo $form->create('CSV', array('url' => array('controller' => 'users', 'action' => 'xls_export')));
echo $form->hidden('data', array('value' => serialize($find)));
echo $form->hidden('fields', array('value' => serialize($export_fields)));
echo $form->submit('CSV');
echo $form->end();

if (empty($users)) {
?>
<p><em>V databázi nejsou žádní uživatelé.</em></p>
<?php } else {?>
<table class="top_heading">
	<tr>
		<th><?php echo $paginator->sort('ID', 'User.id')?></th>
		<th><?php echo $paginator->sort('Křestní jméno', 'User.first_name')?></th>
		<th><?php echo $paginator->sort('Příjmení', 'User.last_name')?></th>
		<th><?php echo $paginator->sort('Telefon', 'User.phone')?></th>
		<th><?php echo $paginator->sort('Email', 'User.email')?></th>
		<th><?php echo $paginator->sort('Login', 'User.login')?></th>
		<th><?php echo $paginator->sort('Typ', 'UserType.name')?></th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($users as $user) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $user['User']['id']?></td>
		<td><?php echo $user['User']['first_name']?></td>
		<td><?php echo $user['User']['last_name']?></td>
		<td><?php echo $user['User']['phone']?></td>
		<td><?php echo $html->link($user['User']['email'], 'mailto:' . $user['User']['email'])?></td>
		<td><?php echo $user['User']['login']?></td>
		<td><?php echo $user['UserType']['name']?></td>
		<td class="actions">
			<?php echo $html->link('Upravit', array('controller' => 'users', 'action' => 'edit', $user['User']['id']))?>
			<?php echo $html->link('Smazat', array('controller' => 'users', 'action' => 'delete', $user['User']['id']), null, 'Opravdu chcete uživatele ' . $user['User']['first_name'] . ' ' . $user['User']['last_name'] . ' smazat?')?>
			<?php //echo $html->link('Heslo', array('controller' => 'users', 'action' => 'generate_password', $user['User']['id']), null, 'Opravdu chcete uživateli ' . $user['User']['first_name'] . ' ' . $user['User']['last_name'] . ' změnit heslo?')?>
		</td>
	</tr>
<?php } ?>
</table>

<?php 
echo $paginator->numbers();
echo $paginator->prev('« Předchozí ', null, null, array('class' => 'disabled'));
echo $paginator->next(' Další »', null, null, array('class' => 'disabled'));

} ?>