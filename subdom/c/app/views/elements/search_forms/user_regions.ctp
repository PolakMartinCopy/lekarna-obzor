<button id="search_form_show">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>

	<?php echo $form->create('UserRegion', array('url' => array('controller' => 'user_regions', 'action' => 'index')))?>
	<table class="left_heading">
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('UserRegion.name', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $form->input('UserRegion.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Jméno uživatele</th>
			<td><?php echo $form->input('User.first_name', array('label' => false))?></td>
			<th>Příjmení uživatele</th>
			<td><?php echo $form->input('User.last_name', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="4">
				<?php
					echo $html->link('reset filtru', array('controller' => 'user_regions'))
				?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('UserRegion.search_form', array('value' => 1));
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