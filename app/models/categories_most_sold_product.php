<?php 
class CategoriesMostSoldProduct extends AppModel {
	var $name = 'CategoriesMostSoldProduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Category', 'Product');
	
	// vyprazdni tabulku
	function truncate() {
		// prepnu na admin db config
		$this->useDbConfig = 'admin';
		$query = 'TRUNCATE TABLE ' . $this->useTable;
		$result = $this->query($query);
		$this->useDbConfig = 'default';
		return $result;
	}
}
?>