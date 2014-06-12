<?php
class Flag extends AppModel {

	var $name = 'Flag';

	var $hasAndBelongsToMany = array('Product');
}
?>