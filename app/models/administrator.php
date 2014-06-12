<?
class Administrator extends AppModel{
	var $name = 'Administrator';

	var $hasMany = array('Ordernote');
	
	// @TODO - pridat validaci administratoru
}
?>