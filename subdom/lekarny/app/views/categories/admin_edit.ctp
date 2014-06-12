<h1>Editace kategorie</h1>
<div class="category">
<?=$form->create('Category');?>
	<table class="left_headed" cellpadding="5" cellspacing="3">
		<tr>
			<th>
				Název kategorie
			</th>
			<td>
				<?=$form->input('name', array('label' => false, 'size' => 70))?>
			</td>
		</tr>
	</table>
<?=$form->hidden('parent_id')?>
<?=$form->end('upravit');?>
</div>