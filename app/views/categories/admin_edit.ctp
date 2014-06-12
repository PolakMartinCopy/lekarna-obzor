<h2>Úprava kategorie - <?php echo $form->value('Category.name')?>(ID:<?php echo $form->value('Category.id')?>)</h2>
<div class="category">
<?php echo $form->create('Category');?>
	<fieldset>
 		<legend>Kategorie</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>Název kategorie</th>
				<td>
					<?=$form->text('name', array('size' => 60))?>
				</td>
			</tr>
			<tr>
				<th>Nadpis</th>
				<td><?php echo $form->input('Category.heading', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>Breadcrumb</th>
				<td><?php echo $form->input('Category.breadcrumb', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>Popis</th>
				<td><?php echo $form->input('Category.content', array('label' => false, 'style' => 'width:600px;height:350px;'))?></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					----------- níže uvedené nevyplňujte -----------
				</td>
			</tr>
			<tr>
				<th>Titulek</th>
				<td><?=$form->text('title', array('size' => 60))?></td>
			</tr>
			<tr>
				<th>Popisek</th>
				<td><?=$form->textarea('description', array('cols' => '50'))?></td>
			</tr>
			<tr>
				<th>
					URL
				</th>
				<td>
					<?=$form->text('url', array('size' => 60))?>
				</td>
			</tr>
		</table>
	<?php
		echo $form->hidden('id');
		echo $form->hidden('lft');
		echo $form->hidden('rght');
	?>
	</fieldset>
<?php echo $form->end('Upravit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Zpět na detaily kategorie', true), array('action'=>'view', $form->value('Category.id')));?></li>
	</ul>
</div>