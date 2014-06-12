<h1><?=$this->data['ProductDocument']['name'] ?> - editace</h1>
<?
	echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'documents_edit', 'id' => $id), 'type' => 'file'));
	echo 'zvolte typ: ' . $form->select('ProductDocument.type', array('doc' => 'Dokument Word', 'pdf' => 'Dokument PDF', 'xls' => 'Dokument Excel'), $this->data['ProductDocument']['type'], array(), false);
	echo $form->hidden('ProductDocument.product_id', array('value' => $this->data['ProductDocument']['product_id']));
	echo $form->submit('upravit');
	echo $form->end();
 ?>