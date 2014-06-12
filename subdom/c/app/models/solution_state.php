<?php
class SolutionState extends AppModel {
	var $name = 'SolutionState';
	
	var $hasMany = array('Solution');
}
?>