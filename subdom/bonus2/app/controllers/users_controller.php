<?php
class UsersController extends AppController {
	var $name = 'Users';
	
	var $uses = array('User', 'Tariff');
	
	function beforeRender() {
		parent::beforeRender();
		$this->set('active_tab', 'users');
	}
	
	/**
	 *  The AuthComponent provides the needed functionality
	 *  for login, so you can leave this function blank.
	 */
	function login() {
	}
	
	function logout() {
		$this->redirect($this->Auth->logout());
	}
	
	function user_index() {
		$user = $this->Auth->user();
		
		if (!$user['User']['is_admin']) {
			$this->Session->setFlash('Nemáte oprávnění prohlížet tento obsah.');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		if ($this->Session->check('validationErrors.User')) {
			$this->User->validationErrors = $this->Session->read('validationErrors.User');
			$this->Session->delete('validationErrors.User');
			
			$this->data['User'] = $this->Session->read('data.User');
			$this->Session->delete('data.User');
		}
		
		$users = $this->User->find('all', array(
			'contain' => array(),
			'fields' => array('User.id', 'User.name', 'User.login', 'User.is_admin'),
			'order' => array('User.id' => 'asc')	
		));
		$this->set('users', $users);
		
		if ($this->Session->check('validationErrors.Tariff')) {
			$this->Tariff->validationErrors = $this->Session->read('validationErrors.Tariff');
			$this->Session->delete('validationErrors.Tariff');
			
			$this->data['Tariff'] = $this->Session->read('data.Tariff');
			$this->Session->delete('data.Tariff');
		}

		$tariffs = $this->Tariff->find('all', array(
			'contain' => array(),
			'order' => array('Tariff.id' => 'asc')
		));
		$this->set('tariffs', $tariffs);
	}
	
	function user_add() {
		$user = $this->Auth->user();
		
		if (!$user['User']['is_admin']) {
			$this->Session->setFlash('Nemáte oprávnění prohlížet tento obsah.');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			// pokud je zadane prazdne heslo, vygeneruje se hash. ja chci ale aby pole bylo prazdne, aby neproslo validaci
			if ($this->data['User']['password'] == $this->Auth->password('')) {
				$this->data['User']['password'] = '';
			}
			if ($this->User->save($this->data)) {
				$this->Session->setFlash('Uživatel byl uložen.');
			} else {
				// pokud nejsou vlozena data validni, vypisu hlasku. V sesne si musim zapamatovat validacni hlasky a vlozena data, abych mohl v indexu naplnit form
				$this->Session->setFlash('Uživatele se nepodařilo vložit, opravte chyby ve formuláři a uložte jej znovu.');
				if ($this->Session->check('validationErrors.User')) {
					$this->Session->delete('validationErrors.User');
				}
				$this->Session->write('validationErrors.User', $this->User->validationErrors);
				unset($this->data['User']['password']);
				if ($this->Session->check('data.User')) {
					$this->Session->delete('data.User');
				}
				$this->Session->write('data.User', $this->data['User']);
			}
		}
		$this->redirect(array('controller' => 'users', 'action' => 'index'));		
	}
	
	function user_edit($id = null) {
		$user = $this->Auth->user();
		
		if (!$user['User']['is_admin']) {
			$this->Session->setFlash('Nemáte oprávnění prohlížet tento obsah.');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		if (!$id) {
			$this->Session->setFlash('Není zadán uživatel, kterého chcete upravovat.');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			// pokud je zadane prazdne heslo, nemenim puvodni
			if ($this->data['User']['password'] == $this->Auth->password('')) {
				unset($this->data['User']['password']);
			}
			if ($this->User->save($this->data)) {
		 		$this->Session->setFlash('Uživatel byl upraven.');
		 		$this->redirect(array('controller' => 'users', 'action' => 'index'));
		 	} else {
		 		$this->Session->setFlash('Uživatele se nepodařilo upravit, opravte chyby ve formuláři a uložte jej znovu.');
		 	}
		} else {
			$user = $this->User->find('first', array(
				'conditions' => array('User.id' => $id),
				'contain' => array()
			));
			
			if (empty($user)) {
				$this->Session->setFlash('Uživatel, kterého chcete upravovat, neexistuje.');
				$this->redirect(array('controller' => 'users', 'action' => 'index'));
			}
			
			$this->data = $user;
			unset($this->data['User']['password']);
		}
	}
}