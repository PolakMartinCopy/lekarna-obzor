<div class="menu_header">
	Uživatelé
</div>
<ul class="menu_links">
<?php if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/Users/user_index')) { ?>
	<li><?php echo $html->link('Uživatelé', array('controller' => 'users', 'action' => 'index'))?></li>
<?php }
	if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/Users/user_add')) {
?>
	<li><?php echo $html->link('Přidat uživatele', array('controller' => 'users', 'action' => 'add'))?></li>
<?php } ?>
	<li><?php echo $html->link('Upravit údaje o mně', array('controller' => 'users', 'action' => 'edit', $logged_in_user['User']['id']))?></li>
</ul>