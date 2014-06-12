<div class="menu_header">
	Detail repa
</div>
<ul class="menu_links">
<?php if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/Reps/user_view')) { ?>
	<li><?php echo $html->link('Detail repa', array('controller' => 'reps', 'action' => 'view', $rep['Rep']['id'], 'tab' => 1))?></li>
<?php }
	if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/WalletTransactions/user_add')) {
?>
	<li><?php echo $html->link('Dobít peněženku', array('controller' => 'wallet_transactions', 'action' => 'user_add', 'rep_id' => $rep['Rep']['id']))?></li>
<?php } ?>
</ul>