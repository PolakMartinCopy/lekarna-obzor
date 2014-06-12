<?php
	if ( !isset($active_tab) ){
		$active_tab = '';
	}
?>

<ul id="top_nav">
	<li><?php echo $html->link('Obchodní partneři', array('controller' => 'business_partners', 'action' => 'index'), array('class' => ($active_tab == 'business_partners' ? 'active' : '')))?></li>
	<li><?php echo $html->link('Obchodní jednání', array('controller' => 'business_sessions', 'action' => 'index'), array('class' => ($active_tab == 'business_sessions' ? 'active' : '')))?></li>
	<li><?php echo $html->link('Výročí', array('controller' => 'anniversaries', 'action' => 'index'), array('class' => ($active_tab == 'anniversaries' ? 'active' : '')))?></li>
	<li><?php echo $html->link('Kontaktní osoby', array('controller' => 'contact_people', 'action' => 'index'), array('class' => ($active_tab == 'contact_people' ? 'active' : '')))?></li>
	<li><?php echo $html->link('Úkoly', array('controller' => 'impositions', 'action' => 'index'), array('class' => ($active_tab == 'impositions' ? 'active' : '')))?></li>
	<li><?php echo $this->Html->link('Dod. listy', array('controller' => 'delivery_notes', 'action' => 'index'), array('class' => ($active_tab == 'delivery_notes' ? 'active' : '')))?></li>
	<li><?php echo $this->Html->link('Prodeje', array('controller' => 'sales', 'action' => 'index'), array('class' => ($active_tab == 'sales' ? 'active' : '')))?></li>
	<li><?php echo $this->Html->link('Sklady', array('controller' => 'store_items', 'action' => 'index'), array('class' => ($active_tab == 'store_items' ? 'active' : '')))?></li>
	<li><?php echo $this->Html->link('Pohyby', array('controller' => 'transactions', 'action' => 'index'), array('class' => ($active_tab == 'transactions' ? 'active' : '')))?></li>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/users/index')) { ?>
		<li><?php echo $html->link('Uživatelé', array('controller' => 'users', 'action' => 'index'), array('class' => ($active_tab == 'users' ? 'active' : '')))?></li>
<?php } else { ?>
		<li><?php echo $html->link('Uživatelé', array('controller' => 'users', 'action' => 'edit', $logged_in_user['User']['id']), array('class' => ($active_tab == 'users' ? 'active' : '')))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/products/index')) { ?>
	<li><?php echo $this->Html->link('Zboží', array('controller' => 'products', 'action' => 'index'), array('class' => ($active_tab == 'products' ? 'active' : '')))?></li>
<?php } ?>
	<li><?php echo $html->link('Oblasti', array('controller' => 'user_regions', 'action' => 'index'), array('class' => ($active_tab == 'user_regions' ? 'active' : '')))?></li>	
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/users/user_setting')) { ?>
		<li><?php echo $html->link('Nastavení', array('controller' => 'anniversary_types', 'action' => 'index'), array('class' => ($active_tab == 'settings' ? 'active' : '')))?></li>
<?php } ?>
	<li><?php echo $html->link('Odhlásit', array('controller' => 'users', 'action' => 'logout'))?></li>
</ul><div class="clearer"></div>