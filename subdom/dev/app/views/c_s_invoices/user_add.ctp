<script type="text/javascript" src="/js/c_s_invoice_add_edit.js"></script>

<h1>Vystavit fakturu</h1>
<?php
	$form_options = array();
	if (isset($business_partner)) {
?>
<ul>
	<li><?php echo $this->Html->link('Zpět na detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id']))?></li>
</ul>
<?php 
		$form_options = array('url' => array('business_partner_id' => $business_partner['BusinessPartner']['id']));
	}

	echo $this->Form->create('CSInvoice', $form_options);
	echo $this->element('c_s_invoices/add_edit_form');
	echo $this->Form->hidden('CSInvoice.date_of_issue');
	echo $this->Form->hidden('CSInvoice.year');
	echo $this->Form->hidden('CSInvoice.month');
	echo $this->Form->hidden('CSInvoice.user_id', array('value' => $user['User']['id']));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>