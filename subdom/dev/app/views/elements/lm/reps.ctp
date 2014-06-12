<div class="menu_header">
	Repové
</div>
<ul class="menu_links">
<?php if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/Reps/user_index')) { ?>
	<li><?php echo $html->link('Repové', array('controller' => 'reps', 'action' => 'index'))?></li>
<?php }
	if ($acl->check(array('model' => 'User', 'foreign_key' => $logged_in_user['User']['id']), 'controllers/Reps/user_add')) {
?>
	<li><?php echo $html->link('Přidat repa', array('controller' => 'reps', 'action' => 'user_add'))?></li>
<?php } ?>
</ul>