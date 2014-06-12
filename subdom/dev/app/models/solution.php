<?php
class Solution extends AppModel {
	var $name = 'Solution';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Imposition', 'SolutionState');
}
?>