<h1>Obsahové stránky</h1>

<ul>
	<li><?php echo $this->Html->link('Přidat obsahovou stránku', array('controller' => 'contents', 'action' => 'add'))?></li>
</ul>

<?php if (empty($contents)) { ?>
<p><em>V systému nejsou žádné obsahové stránky.</em></p>
<?php } else { ?>
<table class="topHeading">
	<tr>
		<th>ID</th>
		<th>Titulek</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($contents as $content) { ?>
	<tr>
		<td><?php echo $content['Content']['id']?></td>
		<td><?php echo $this->Html->link($content['Content']['title'], '/' . $content['Content']['path'])?></td>
		<td><?php
			echo $this->Html->link('Upravit', array('controller' => 'contents', 'action' => 'edit', $content['Content']['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('controller' => 'contents', 'action' => 'delete', $content['Content']['id']), null, 'Opravdu chcete obsahovou stránku "' . $content['Content']['title'] . '" odstranit?');
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>