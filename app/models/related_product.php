<?
class RelatedProduct extends AppModel{
	var $name = 'RelatedProduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Product');
	
	function get_list($id){
		$related_products = $this->find('all', array(
			'conditions' => array(
				'product_id' => $id
			),
			'contain' => array()
		));
		
		$related_ids = array();
		foreach ( $related_products as $related_product ){
			$related_ids[] = $related_product['RelatedProduct']['related_product_id'];
		}
		
		return $related_ids;
	}
}
?>