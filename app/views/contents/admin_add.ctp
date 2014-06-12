<h1>Nová obsahová stránka</h1>

<? echo $form->Create('Content'); ?>
	<fieldset>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>Cesta</th>
				<td><?=$form->text('Content.path', array('size' => '60'));?></td>
			</tr>
			<tr>
				<th>Titulek</th>
				<td><?=$form->text('Content.title', array('size' => '60'))?></td>
			</tr>
			<tr>
				<th>Popisek</th>
				<td><?=$form->textarea('Content.description', array('rows' => '3', 'cols' => '45'))?></td>
			</tr>
			<tr>
				<th>Textový obsah</th>
				<td><?=$form->textarea('Content.content', array('cols' => '60', 'rows' => '40'))?></td>
			</tr>
		</table>
	</fieldset>
<?
	echo $form->Submit('UPRAVIT');
	echo $form->end();
?>