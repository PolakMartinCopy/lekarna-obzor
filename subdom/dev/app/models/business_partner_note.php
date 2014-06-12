<?php 
class BusinessPartnerNote extends AppModel {
	var $name = 'BusinessPartnerNote';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('BusinessPartner');
}
?>