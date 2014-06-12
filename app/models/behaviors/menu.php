<?
class MenuBehavior extends ModelBehavior{
	var $settings = array();
	
	function setup(&$model, $config = array()){
		$this->setings = $config;
	}
	
	/* callbacks */

	/* custom methods */
	function renderMenu(&$model, $id = null){
		return "hello world";
	}
}
?>