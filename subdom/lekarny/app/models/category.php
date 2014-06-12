<?php
class Category extends AppModel {

	var $name = 'Category';

	var $actsAs = array('Containable', 'Tree');
	
	var $validate = array(
		'parent_id' => array(
			'rule' => 'numeric',
			'message' => 'Prázdné parent_id!'
		),
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'Název nemůže zůstat nevyplněn!'
		)
	);
	
	var $hasAndBelongsToMany = array('Product');
	
	var $hasMany = array('CategoriesProduct');
	
	var $displayField = 'name';
	
	function count_products($id){
		return $this->CategoriesProduct->find('count', array(
			'conditions' => array(
				'category_id' => $id
			)
		));
	}
}
?>