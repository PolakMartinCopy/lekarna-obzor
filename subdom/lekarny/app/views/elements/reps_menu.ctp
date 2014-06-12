<ul id="nav">
	<li><?=$html->link('Hlavní stránka', array('rep' => true, 'controller' => 'reps', 'action' => 'index')) ?></li>
	<li><?=$html->link('Upravit osobní údaje', array('rep' => true, 'controller' => 'reps', 'action' => 'edit')) ?></li>
	<li><?=$html->link('Seznam mých oblastí', array('rep' => true, 'controller' => 'rep_areas', 'action' => 'index')) ?></li>
	<li><?=$html->link('Seznam mých zákazníků', array('rep' => true, 'controller' => 'companies', 'action' => 'index')) ?></li>
	<li><?=$html->link('Objednávky mých zákazníků', array('rep' => true, 'controller' => 'orders', 'action' => 'index')) ?></li>
	<li><?=$html->link('Odhlásit', array('rep' => true, 'controller' => 'reps', 'action' => 'logout')) ?></li>
</ul>
