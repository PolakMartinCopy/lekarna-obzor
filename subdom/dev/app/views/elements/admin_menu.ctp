<?php
	if ( !isset($active_tab) ){
		$active_tab = '';
	}
?>

<ul id="top_nav">
	<li><?php echo $html->link('Obch. partneři', array('controller' => 'business_partners', 'action' => 'index'), array('class' => ($active_tab == 'business_partners' ? 'active' : '')))?></li>
	<li><?php echo $html->link('Obch. jednání', array('controller' => 'business_sessions', 'action' => 'index'), array('class' => ($active_tab == 'business_sessions' ? 'active' : '')))?></li>
	<li><?php echo $html->link('Výročí', array('controller' => 'anniversaries', 'action' => 'index'), array('class' => ($active_tab == 'anniversaries' ? 'active' : '')))?></li>
	<li><?php echo $html->link('Kont. osoby', array('controller' => 'contact_people', 'action' => 'index'), array('class' => ($active_tab == 'contact_people' ? 'active' : '')))?></li>
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
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/reps/index')) { ?>
	<li><?php echo $this->Html->link('Repové', array('controller' => 'reps', 'action' => 'index'), array('class' => ($active_tab == 'reps' ? 'active' : '')))?>
		<ul>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/WalletTransactions/index')) { ?>
			<li><?php echo $this->Html->link('Peněženky', array('controller' => 'wallet_transactions', 'action' => 'index'))?></li>
<?php } ?>
		</ul>
	</li>
<?php } ?>
	<li><?php
		$url = '#';
		if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/ProductVariants/index')) {
			$url = array('controller' => 'product_variants', 'action' => 'index');
		}		
		echo $this->Html->link('Centrální sklad', $url, array('class' => ($active_tab == 'central_storing' ? 'active' : '')))
		?>
		<ul>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSStorings/index')) { ?>
			<li><?php echo $this->Html->link('CS Naskladnění', array('controller' => 'c_s_storings', 'action' => 'index'), array('class' => $active_tab == 'c_s_storings' ? 'active' : ''))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSInvoices/index')) { ?>
			<li><?php echo $this->Html->link('CS Faktury', array('controller' => 'c_s_invoices', 'action' => 'index'), array('class' => $active_tab == 'c_s_invoices' ? 'active' : ''))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSCreditNotes/index')) { ?>
			<li><?php echo $this->Html->link('CS Dobropisy', array('controller' => 'c_s_credit_notes', 'action' => 'index'), array('class' => $active_tab == 'c_s_credit_notes' ? 'active' : ''))?></li>
<?php } ?>
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/CSTransactions/index')) { ?>
			<li><?php echo $this->Html->link('CS Pohyby', array('controller' => 'c_s_transactions', 'action' => 'index'), array('class' => $active_tab == 'c_s_transactions' ? 'active' : ''))?></li>
<?php } ?>			
		</ul>
	</li>
	<li><?php echo $html->link('Oblasti', array('controller' => 'user_regions', 'action' => 'index'), array('class' => ($active_tab == 'user_regions' ? 'active' : '')))?></li>	
<?php if (isset($acl) && $acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/users/user_setting')) { ?>
		<li><?php echo $html->link('Nastavení', array('controller' => 'anniversary_types', 'action' => 'index'), array('class' => ($active_tab == 'settings' ? 'active' : '')))?></li>
<?php } ?>
	<li><?php echo $html->link('Odhlásit', array('controller' => 'users', 'action' => 'logout'))?></li>
</ul><div class="clearer"></div>