<div class="menu_header">
	Úkol
</div>
<ul class="menu_links">
	<li><?php echo $html->link('Detail úkolu', array('controller' => 'impositions', 'action' => 'view', $imposition['Imposition']['id']))?></li>
	<li><?php echo $html->link('Upravit úkol', array('controller' => 'impositions', 'action' => 'edit', $imposition['Imposition']['id']))?></li>
	<li><?php echo $html->link('Přidat dokument', array('controller' => 'documents', 'action' => 'add', 'imposition_id' => $imposition['Imposition']['id']))?></li>
</ul>