<?php
	if ( !isset($active_tab) ){
		$active_tab = '';
	}
?>

<ul id="top_nav">
	<li><?php echo $html->link('Hledání zákazníka', array('controller' => 'customers', 'action' => 'index'), array('class' => ($active_tab == 'customers' ? 'active' : '')))?></li>
	<li><?php echo $html->link('Vložení nového zákazníka', array('controller' => 'customers', 'action' => 'add'), array('class' => ($active_tab == 'customers_add' ? 'active' : '')))?></li>
	<li><?php echo $html->link('Prodeje', array('controller' => 'sales', 'action' => 'index'), array('class' => ($active_tab == 'sales' ? 'active' : '')))?></li>
	<li><?php echo $html->link('Vyplacené bonusy', array('controller' => 'pay_outs', 'action' => 'index'), array('class' => ($active_tab == 'pay_outs' ? 'active' : '')))?></li>
<?php
	if ($user['User']['is_admin']) {
?>
		<li><?php echo $html->link('Uživatelé', array('controller' => 'users', 'action' => 'index'), array('class' => ($active_tab == 'users' ? 'active' : '')))?></li>
<?php
	}
?>
	<li><?php echo $html->link('Odhlásit', array('controller' => 'users', 'action' => 'logout', 'user' => false))?></li>
</ul><div class="clearer"></div>