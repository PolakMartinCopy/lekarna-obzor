<div class="menu_header">
	Úkoly
</div>
<ul class="menu_links">
	<li><?php echo $html->link('Úkoly', array('controller' => 'impositions', 'action' => 'index'))?></li>
	<li><?php echo $html->link('Notifikovat', array('controller' => 'impositions', 'action' => 'notify', 'back_link' => base64_encode($this->params['url']['url'])))?></li>
	<li><?php echo $html->link('Přidat úkol', array('controller' => 'impositions', 'action' => 'add'))?></li>
</ul>