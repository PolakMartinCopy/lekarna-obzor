<div class="menu_header">
	Obchodní partner
</div>
<ul class="menu_links">
	<li><?php echo $html->link('Detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id']))?></li>
	<li><?php echo $html->link('Upravit obchodního partnera', array('controller' => 'business_partners', 'action' => 'edit', $business_partner['BusinessPartner']['id']))?></li>
	<li><?php echo $html->link('Smazat obchodního partnera', array('controller' => 'business_partners', 'action' => 'delete', $business_partner['BusinessPartner']['id']), null, 'Opravdu chcete tohoto obchodního partnera odstranit?')?>
	<li><?php echo $html->link('Upravit adresu sídla', array('controller' => 'addresses', 'action' => 'edit', $seat_address['Address']['id']))?></li>
<?php
	$target = array('controller' => 'addresses', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id'], 'address_type_id' => 3);
	if (!empty($invoice_address)) {
		$target = array('controller' => 'addresses', 'action' => 'edit', $invoice_address['Address']['id']);
	}
?>
	<li><?php echo $html->link('Upravit fakturační adresu', $target)?></li>
<?php if (!empty($invoice_address)) { ?>
	<li><?php echo $html->link('Smazat fakturační adresu', array('controller' => 'addresses', 'action' => 'delete', $invoice_address['Address']['id']))?></li>
<?php }
	$target = array('controller' => 'addresses', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id'], 'address_type_id' => 4);
	if (!empty($delivery_address)) {
		$target = array('controller' => 'addresses', 'action' => 'edit', $delivery_address['Address']['id']);
	}
?>
	<li><?php echo $html->link('Upravit doručovací adresu', $target)?></li>
<?php if (!empty($delivery_address)) { ?>
	<li><?php echo $html->link('Smazat doručovací adresu', array('controller' => 'addresses', 'action' => 'delete', $delivery_address['Address']['id']))?></li>
<?php } ?>
	<li><?php echo $html->link('Přidat adresu pobočky', array('controller' => 'addresses', 'action' => 'add', 'address_type_id' => 5, 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
	<li><?php echo $html->link('Přidat dokument', array('controller' => 'documents', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
	<li><?php echo $html->link('Přidat kontaktní osobu', array('controller' => 'contact_people', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
	<li><?php echo $html->link('Přidat obchodní jednání', array('controller' => 'business_sessions', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
	<li><?php echo $this->Html->link('Přidat dodací list', array('controller' => 'delivery_notes', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
	<li><?php echo $this->Html->link('Přidat prodej', array('controller' => 'sales', 'action' => 'add', 'business_partner_id' => $business_partner['BusinessPartner']['id']))?></li>
<?php if ($acl->check(array('model' => 'User', 'foreign_key' => $session->read('Auth.User.id')), 'controllers/BusinessPartners/user_edit_user')) { ?>
	<li><?php echo $html->link('Upravit uživatele', array('controller' => 'business_partners', 'action' => 'edit_user', $business_partner['BusinessPartner']['id']))?></li>
<?php } ?>
</ul>
