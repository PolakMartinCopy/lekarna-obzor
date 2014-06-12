<button id="search_form_show">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>

	<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'index')))?>
	<table class="left_heading">
		<tr>
			<th>Křestní jméno</th>
			<td><?php echo $form->input('User.first_name', array('label' => false))?></td>
			<th>Příjmení</th>
			<td><?php echo $form->input('User.last_name', array('label' => false))?></td>
			<th>Login</th>
			<td><?php echo $form->input('User.login', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Telefon</th>
			<td><?php echo $form->input('User.phone', array('label' => false))?></td>
			<th>Email</th>
			<td><?php echo $form->input('User.email', array('label' => false))?></td>
			<th>Typ</th>
			<td><?php echo $form->input('User.user_type_id', array('options' => $user_types, 'empty' => true, 'label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php
					echo $html->link('reset filtru', array('controller' => 'users'))
				?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('User.search_form', array('value' => 1));
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