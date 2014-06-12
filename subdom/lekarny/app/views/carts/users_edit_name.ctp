<h1>Přejmenování rozpracované objednávky</h1>
<?=$form->Create('Cart', array('url' => '/users/carts/edit_name/' . $this->data['Cart']['id'])) ?>
<table class="left_headed register">
	<tr>
		<th>
			název rozpracované objednávky
		</th>
		<td>
			<?=$form->input('Cart.name', array('label' => false, 'size' => 50)) ?>
		</td>
	</tr>
	<tr>
		<th>
			&nbsp;
		</th>
		<td>
			<?=$form->submit('přejmenovat') ?>
		</td>
	</tr>
</table>
<? ?>
<?=$form->end() ?>