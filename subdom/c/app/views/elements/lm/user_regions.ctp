<div class="menu_header">
	Oblasti
</div>
<ul class="menu_links">
	<li><?php echo $html->link('Oblasti', array('controller' => 'user_regions', 'action' => 'index'))?></li>
<?php if ($acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/UserRegions/add')) { ?>
	<li><?php echo $html->link('Přidat oblast', array('controller' => 'user_regions', 'action' => 'add'))?></li>
<?php } ?>
</ul>