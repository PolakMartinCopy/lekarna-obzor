<h1>Obchodní partneři</h1>

<?php
	echo $this->element('search_forms/business_partners');

	echo $form->create('CSV', array('url' => array('controller' => 'business_partners', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<!-- <ul>
	<li><?php echo $html->link('Přidat obchodního partnera', array('controller' => 'business_partners', 'action' => 'add'))?></li>
</ul> -->

<?php if (empty($business_partners)) {
	$message = 'V databázi nejsou žádní obchodní partneři';
	if (isset($reset)) {
		$message .= ' odpovídající zadaným parametrům';
	}
?>
<p><em><?php echo $message?>.</em></p>
<?php } else {
	if (isset($this->data['BusinessPartner'])) {
		$paginator->options(array('url' => $this->data['BusinessPartner']));
	}

	echo $this->element('indexes/business_partners', array('business_partners' => $business_partners));

	echo $paginator->numbers();
	echo $paginator->prev('« Předchozí ', null, null, array('class' => 'disabled'));
	echo $paginator->next(' Další »', null, null, array('class' => 'disabled'));
?>

<?php } // end if?>

<!-- <ul>
	<li><?php echo $html->link('Přidat obchodního partnera', array('controller' => 'business_partners', 'action' => 'add'))?></li>
</ul> -->