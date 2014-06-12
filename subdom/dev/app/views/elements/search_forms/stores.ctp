<button id="search_form_show">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['StoreItem']) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>

	<?php echo $form->create('StoreItem', array('url' => array('controller' => 'store_items', 'action' => 'index')))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Odběratel</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $form->input('BusinessPartner.name', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('Address.city', array('label' => false))?></td>
			<th>Okres</th>
			<td><?php echo $this->Form->input('Address.region', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">Zboží</td>
		</tr>
		<tr>
			<th>Název</th>
			<td><?php echo $this->Form->input('Product.name', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('Product.vzp_code', array('label' => false))?></td>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('Product.group_code', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', array('controller' => 'store_items', 'reset' => true)) ?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('StoreItem.search_form', array('value' => 1));
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