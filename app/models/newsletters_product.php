<?
class NewslettersProduct extends AppModel{
	var $name = 'NewslettersProduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Product', 'Newsletter');
}
?>