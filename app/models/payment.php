<?
class Payment extends AppModel {
	var $name = 'Payment';
	var $hasMany = array('Order');

	function get_data($id){
		$this->recursive = -1;
		return $this->read(null, $id);
	}
}
?>