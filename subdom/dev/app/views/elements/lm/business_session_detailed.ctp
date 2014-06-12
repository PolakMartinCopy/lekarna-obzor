<div class="menu_header">
	Obchodní jednání
</div>
<ul class="menu_links">
	<li><?php echo $html->link('Detail obchodního jednání', array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id']))?></li>
	<li><?php echo $html->link('Upravit obchodní jednání', array('controller' => 'business_sessions', 'action' => 'edit', $business_session['BusinessSession']['id']))?></li>
	<li><?php echo $html->link('Uzařít obchodní jednání', array('controller' => 'business_sessions', 'action' => 'close', $business_session['BusinessSession']['id']))?></li>
	<li><?php echo $html->link('Stornovat obchodní jednání', array('controller' => 'business_sessions', 'action' => 'storno', $business_session['BusinessSession']['id']))?></li>
	<li><?php echo $html->link('Přizvané kontaktní osoby', array('controller' => 'business_sessions', 'action' => 'invite', $business_session['BusinessSession']['id']))?></li>
	<li><?php echo $html->link('Přidat náklady', array('controller' => 'costs', 'action' => 'add', 'business_session_id' => $business_session['BusinessSession']['id']))?></li>
	<li><?php echo $html->link('Přidat nabídku', array('controller' => 'offers', 'action' => 'add', 'business_session_id' => $business_session['BusinessSession']['id']))?></li>
</ul>