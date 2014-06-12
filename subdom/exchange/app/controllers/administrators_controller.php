<?php 
class AdministratorsController extends AppController {
	var $name = 'Administrators';
	
	var $scaffold = 'admin';
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('tmp');
	}
	
	function tmp() {
		debug($this->Auth->password('brko')); die('here');
	}
	
	function login() {}
	
	function admin_logout() {
		$this->Session->setFlash('Byl jste úspěšně odhlášen ze systému');
		$this->redirect($this->Auth->logout());
	}
}
?>