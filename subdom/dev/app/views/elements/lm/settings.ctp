<div class="menu_header">
	Nastavení
</div>
<ul class="menu_links">
	<li><?php echo $html->link('Typy výročí', array('controller' => 'anniversary_types', 'action' => 'index'))?></li>
	<li><?php echo $html->link('Stavy úkolů', array('controller' => 'imposition_states', 'action' => 'index'))?></li>
	<li><?php echo $html->link('Stavy řešení', array('controller' => 'solution_states', 'action' => 'index'))?></li>
	<li><?php echo $html->link('Typy obchodních jednání', array('controller' => 'business_session_types', 'action' => 'index'))?></li>
	<li><?php echo $html->link('Stavy obchodních jednání', array('controller' => 'business_session_states', 'action' => 'index'))?></li>
	<li><?php echo $html->link('Typy adres', array('controller' => 'address_types', 'action' => 'index'))?></li>
	<li><?php echo $html->link('Emailové šablony', array('controller' => 'mail_templates', 'action' => 'index'))?></li>
	<li><?php echo $html->link('Periody úkolů', array('controller' => 'imposition_periods', 'action' => 'index'))?></li>
	<li><?php echo $this->Html->link('Jednotky zboží', array('controller' => 'units', 'action' => 'index'))?></li>
	<li><?php echo $this->Html->link('Typy transakcí', array('controller' => 'transaction_types', 'action' => 'index'))?></li>
</ul>