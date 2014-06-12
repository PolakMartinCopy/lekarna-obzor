<?
class Ordernote extends AppModel{
	var $name = 'Ordernote';
	var $order = array(
		'Ordernote.created' => 'asc'
	);

	var $belongsTo = array('Administrator', 'Order', 'Status');
}
?>