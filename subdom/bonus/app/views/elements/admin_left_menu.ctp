<?php
	if (!isset($left_menu_list)) {
		$left_menu_list = array();
	}
	foreach ($left_menu_list as $left_menu){
		echo $this->element('lm/' . $left_menu);
	}
?>