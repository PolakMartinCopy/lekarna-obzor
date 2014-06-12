<?php
class AdministratorsController extends AppController {
	var $name = 'Administrators';
	var $helpers = array('Form', 'Html');
	
	/*
	 * @description					Pridani administratora.
	 */
	function admin_add(){
		if ( isset($this->data) ){
			$this->data['Administrator']['password'] = md5($this->data['Administrator']['npass']);
			unset($this->data['Administrator']['npass']);

			if ( $this->Administrator->save($this->data) ){
				$this->Session->setFlash('Administrátor byl uložen.');
				$this->redirect(array('controller' => 'administrators', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Chyba při ukládání administrátora.');
			}
		}
		
		// prihodim si random password, at ho nemusim vymyslet
		$pass = md5( date("Y-m-d H:i:s") );
		$pass = substr($pass, 5, 8);
		$this->data['Administrator']['npass'] = $pass;
	}
	
	/*
	 * @description					Seznam administratoru obchodu.
	 */
	function admin_index(){
		$this->Administrator->recursive = -1;
		$admins = $this->Administrator->find('all', array('order' => array('Administrator.last_name')));

		$this->set('admins', $admins);
	}
	
	/*
	 * @description					Prihlaseni administratora.
	 */
	function admin_login() {
		// navolim layout
		$this->layout = 'empty_page';
		if (  isset($this->data) ){
			$conditions = array('login' => $this->data['Administrator']['login']);
			$this->Administrator->recursive = -1;
			$administrator = $this->Administrator->find($conditions);
			if ( empty( $administrator ) ){
				$this->Session->setFlash('Neplatné uživatelské jméno!');
			} else {
				if ( $administrator['Administrator']['password'] != md5($this->data['Administrator']['password']) ){
					$this->Session->setFlash('Neplatné heslo!');
				} else {
					// upravim si data, ktera si chci pamatovat pro cookies a session
					unset($administrator['Administrator']['login']);
					$administrator['Administrator']['password'] = md5($administrator['Administrator']['password'] . Configure::read('Security.salt')); 
					
					// zkontroluju, jestli nebylo zaskrtnuto
					// "dlouhe prihlaseni" a nastavim cookie na dve hodiny
					if ( $this->data['Administrator']['longlogin'] == '1' ){
						unset($administrator['Administrator']['longlogin']);
						$this->Cookie->write('Administrator', $administrator['Administrator'], true, 7200);
					}
					
					// zapisu data do session
					$this->Session->write('Administrator', $administrator['Administrator']);
					
					// presmeruju
					$this->redirect(array('controller' => 'orders', 'action' => 'index'), null, true);
				}
			}
		}
	}

	/*
	 * @description					Odhlaseni admininistratora.
	 */
	function admin_logout() {
		$this->Cookie->delete('Administrator');
		$this->Session->delete('Administrator');
		
		$this->redirect(array('controller' => 'administrators', 'action' => 'login'), null, true);
	}

}
?>
