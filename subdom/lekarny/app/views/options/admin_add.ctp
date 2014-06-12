<h1>Vložit nový název atributu</h1>
<?php echo $form->create('Option');?>
<table class="left_headed">
	<tr>
		<th>Název atributu</th>
		<td><?=$form->text('name')?></td>
	</tr>
</table>
<?php echo $form->end('Vložit');?>