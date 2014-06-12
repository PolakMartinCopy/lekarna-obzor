<div class="menu_header">
	Peněženky
</div>
<ul class="menu_links">
	<li><?php echo $html->link('Peněženky', array('controller' => 'wallet_transactions', 'action' => 'index'))?></li>
<?php if ($acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/WalletTransactions/add')) { ?>
	<li><?php echo $html->link('Přidat transakci', array('controller' => 'wallet_transactions', 'action' => 'add'))?></li>
<?php } ?>
</ul>