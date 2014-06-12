<?php
class AdministratorsController extends AppController {

	var $name = 'Administrators';
	
	function admin_index(){
		$this->layout = 'admin';
	}
	
	function admin_login() {
		if (  isset($this->data) ){
			$conditions = array('login' => $this->data['Administrator']['login']);
			$administrator = $this->Administrator->find($conditions);
			if ( empty( $administrator ) ){
				$this->Session->setFlash('Neplatné uživatelské jméno!');
			} else {
				if ( $administrator['Administrator']['password'] != md5($this->data['Administrator']['password']) ){
					$this->Session->setFlash('Neplatné heslo!');
				} else {
					$this->Session->write('Administrator', $administrator['Administrator']);
					$this->redirect(array('controller' => 'administrators', 'action' => 'index'), null, true);
				}
			}
		}
	}

	function admin_logout() {
		$this->Session->del('Administrator');
		$this->redirect(array('controller' => 'administrators', 'action' => 'login'), null, true);
	}

}
?>