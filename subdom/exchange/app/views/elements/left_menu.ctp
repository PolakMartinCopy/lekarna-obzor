<?php if (isset($administrator)) { ?>
<div class="menu_header">
	Menu
</div>
<ul class="menu_links">
	<li><?php echo $this->Html->link('Kurzy', array('controller' => 'rates', 'action' => 'index'))?></li>
	<li><?php echo $this->Html->link('Nastavení', array('controller' => 'settings', 'action' => 'edit'))?></li>
	<li><?php echo $this->Html->link('Odhlásit', array('controller' => 'administrators', 'action' => 'logout'))?></li>
</ul>
<?php } ?>