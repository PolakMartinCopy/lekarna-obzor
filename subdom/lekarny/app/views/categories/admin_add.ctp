<h1>Vytvoření nové kategorie</h1>
<div class="category">
<?=$form->create('Category');?>
	<table class="left_headed" cellpadding="5" cellspacing="3">
		<tr>
			<th>
				Název kategorie
			</th>
			<td>
				<?=$form->input('name', array('label' => false))?>
			</td>
		</tr>
	</table>
<?=$form->hidden('parent_id', array('value' => $parent_id))?>
<?=$form->end('vložit');?>
</div>