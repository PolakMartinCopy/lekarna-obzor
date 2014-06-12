<h1>Jednotky zboží</h1>
<ul>
	<li><?php echo $this->Html->link('Přidat jednotku', array('action' => 'add'))?></li>
</ul>

<?php if (empty($units)) { ?>
<p><em>V systému nejsou žádné jednotky zboží.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($units as $unit) { ?>
	<tr>	
		<td><?php echo $unit['Unit']['id']?></td>
		<td><?php echo $unit['Unit']['name']?></td>
		<td><?php
			echo $this->Html->link('Upravit', array('action' => 'edit', $unit['Unit']['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('action' => 'delete', $unit['Unit']['id']), array(), 'Opravdu chcete jednotku ' . $unit['Unit']['name'] . ' odstranit?');
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>

<ul>
	<li><?php echo $this->Html->link('Přidat jednotku', array('action' => 'add'))?></li>
</ul>