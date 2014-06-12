<?php
class UsersController extends AppController {
	var $name = 'Users';
	
	var $left_menu_list = array('users');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'users');
		$this->Auth->allow('user_regenerate_password', 'user_confirm', 'build_acl', 'user_initAcl');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_login() {
		$this->left_menu_list = array();
	}
	
	function user_index() {
		$this->set('user_types', $this->User->UserType->find('list'));
		
		$conditions = array('User.active' => true);
		if ($this->user['User']['user_type_id'] == 2) {
			$conditions = array('User.user_type_id !=' => 1);
		}
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'users') {
			$this->Session->delete('Search.UserForm');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['UserForm']['User']['search_form']) && $this->data['UserForm']['User']['search_form'] == 1 ){
			$this->Session->write('Search.UserForm', $this->data['UserForm']); 
			$conditions = $this->User->do_form_search($conditions, $this->data['UserForm']);
		} elseif ($this->Session->check('Search.UserForm')) {
			$this->data['UserForm'] = $this->Session->read('Search.UserForm');
			$conditions = $this->User->do_form_search($conditions, $this->data['UserForm']);
		}
		
		$this->paginate['User'] = array(
			'conditions' => $conditions,
			'contain' => array('UserType'),
			'limit' => 30
		);
		
		$users = $this->paginate('User');
		$this->set('users', $users);
		
		$find = $this->paginate['User'];
		unset($find['limit']);
		$this->set('find', $find);
		
		$export_fields = array(
			array('field' => 'User.id', 'position' => '["User"]["id"]', 'alias' => 'User.id'),
			array('field' => 'User.first_name', 'position' => '["User"]["first_name"]', 'alias' => 'User.first_name'),
			array('field' => 'User.last_name', 'position' => '["User"]["last_name"]', 'alias' => 'User.last_name'),
			array('field' => 'User.phone', 'position' => '["User"]["phone"]', 'alias' => 'User.phone'),
			array('field' => 'User.email', 'position' => '["User"]["email"]', 'alias' => 'User.email'),
			array('field' => 'User.login', 'position' => '["User"]["login"]', 'alias' => 'User.login'),
			array('field' => 'UserType.name', 'position' => '["UserType"]["name"]', 'alias' => 'UserType.name'),
		);
		$this->set('export_fields', $export_fields);
	}
	
	function user_setting() {
		$this->set('active_tab', 'settings');
		$this->left_menu_list = array('settings');
	}
	
	function user_logout() {
		$this->Session->setFlash('Byl jste úspěšně odhlášen ze systému');
		$this->redirect($this->Auth->logout());
	}
	
	function user_add() {
		$conditions = array('UserType.id != 4');
		if ($this->user['User']['user_type_id'] == 2) {
			$conditions = array('UserType.id !=' => 1);
		}
		$user_types = $this->User->UserType->find('list', array(
			'conditions' => $conditions
		));
		$this->set('user_types', $user_types);
		
		if (isset($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash('Uživatel byl vytvořen.');
				$this->redirect(array('controller' => 'users', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Uživatele se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci.');
				unset($this->data['User']['password']);
			}
		}
	}
	
	function user_edit($id = null) {
		if ($this->user['User']['user_type_id'] == 3 && $id != $this->user['User']['id']) {
			$this->Session->setFlash('Neoprávněný přístup');
			$this->redirect(array('controller' => 'anniversaries', 'action' => 'index'));
		} else {
			if ($this->user['User']['user_type_id'] == 3) {
				$own = true;
				$this->set('own', $own);
			}
		}
		
		if (!$id) {
			$this->Session->setFlash('Není zvolen uživatel, kterého chcete upravovat.');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		$user = $this->User->find('first', array(
			'conditions' => array('User.id' => $id),
			'contain' => array()
		));
		
		if (empty($user)) {
			$this->Session->setFlash('Požadovaný uživatel neexistuje.');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		if ($this->user['User']['user_type_id'] == 2 && $user['User']['user_type_id'] == 1) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo upravovat zvoleného uživatele.');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		$conditions = array('UserType.id != 4');
		if ($this->user['User']['user_type_id'] == 2) {
			$conditions = array('UserType.id !=' => 1);
		}
		$user_types = $this->User->UserType->find('list', array(
			'conditions' => $conditions
		));
		$this->set('user_types', $user_types);
		
		if (isset($this->data)) {
			if (empty($this->data['User']['password'])) {
				unset($this->data['User']['password']);
			}
			if ($this->User->save($this->data)) {
				$this->Session->setFlash('Uživatel byl upraven.');
				if (isset($own) && !$own) {
					$this->redirect(array('controller' => 'users', 'action' => 'index'));
				} else {
					$this->redirect(array('controller' => 'users', 'action' => 'edit', $this->data['User']['id']));
				}
			} else {
				$this->Session->setFlash('Uživatele se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci.');
				unset($this->data['User']['password']);
			}
		} else {
			$this->data = $user;
			unset($this->data['User']['password']);
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolen uživatel, kterého chcete smazat.');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		$user = $this->User->find('first', array(
			'conditions' => array('User.id' => $id),
			'contain' => array()
		));
		
		if (empty($user)) {
			$this->Session->setFlash('Požadovaný uživatel neexistuje.');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		if ($this->user['User']['user_type_id'] == 2 && $user['User']['user_type_id'] == 1) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo smazat zvoleného uživatele.');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		if ($this->User->delete($id)) {
			$this->Session->setFlash('Uživatel byl odstraněn.');
		} else {
			$this->Session->setFlash('Uživatele se nepodařilo odstranit, opakujte prosím akci.');
		}
		$this->redirect(array('controller' => 'users', 'action' => 'index'));
	}
	
	/**
	 * 
	 * na zadost uzivatele (kdyz zapomene heslo) vygeneruje a zasle heslo na jeho email
	 */
	function user_regenerate_password() {
		$this->left_menu_list = array();
		
		if (isset($this->data)) {
			$user = $this->User->find('first', array(
				'conditions' => array('User.email' => $this->data['User']['email']),
				'contain' => array()
			));
			if (empty($user)) {
				$this->Session->setFlash('Uživatel se zadanou emailovou adresou neexistuje.');
			} else {
				// udelam hash, pomoci ktereho budu moci identifikovat uzivatele a zaslu ho na jeho emailovou adresu (v dane url)
				$hash = $this->User->createHash($user);
				if ($this->User->sendHash($user, $hash)) {
					$this->Session->setFlash('Výzva k potvrzení změny hesla byla odeslána.');
				} else {
					$this->Session->setFlash('Výzvu k potvrzení změny hesla se nepodařilo odeslat.');
				}
				$this->redirect(array('controller' => 'users', 'action' => 'login'));
			}
		}
	}
	
	function user_confirm($user_id, $hash) {
		$user = $this->User->find('first', array(
			'conditions' => array('User.id' => $user_id),
			'contain' => array()
		));
		
		if ($this->User->createHash($user) == $hash) {
			$password = $this->User->generatePassword($user);
			$md5_password = md5($password);
			$save = array('User' => array(
				'id' => $user['User']['id'],
				'password' => $md5_password
			));
			if ($this->User->save($save)) {
				if ($this->User->sendPassword($password, $user)) {
					$this->Session->setFlash('Nové heslo Vám bylo odesláno na zadaný email.');
				} else {
					$this->Session->setFlash('Heslo bylo změněno, ale odeslání emailu se nezdařilo.');
				}
			} else {
				$this->Session->setFlash('Uložení hesla se nezdařilo. Heslo nebylo změněno.');
			}
		} else {
			$this->Session->setFlash('Neoprávněný požadavek o změnu hesla. Přesvědčte se prosím, že vložena URL odpovídá adrese v emailu s výzvou k potvrzení změny přihlašovacích údajů.');
		}
		$this->redirect(array('controller' => 'users', 'action' => 'login'));
	}
	
	/**
	 * 
	 * ADMIN uzivateli vygeneruje heslo
	 */
/*	function user_generate_password($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolen uživatel, kterému chcete vygenerovat heslo.');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		$user = $this->User->find('first', array(
			'conditions' => array('User.id' => $id),
			'contain' => array()
		));
		
		if (empty($user)) {
			$this->Session->setFlash('Požadovaný uživatel neexistuje.');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		$save = array();
		$save['User']['id'] = $user['User']['id'];
		$password = $this->User->generatePassword($user);
		$save['User']['password'] = md5($password);
		
		if ($this->User->save($save)) {
			if ($this->User->sendPassword($password, $user)) {
				$this->Session->setFlash('Nové heslo Vám bylo odesláno na uživatelův email.');
			} else {
				$this->Session->setFlash('Heslo bylo změněno, ale odeslání emailu se nezdařilo.');
			}
		} else {
			$this->Session->setFlash('Uložení hesla se nezdařilo.');
		}
		$this->redirect(array('controller' => 'users', 'action' => 'index'));
	}*/
	
	function build_acl() {
		if (!Configure::read('debug')) {
			return $this->_stop();
		}
		$log = array();
		$aco =& $this->Acl->Aco;
		$root = $aco->node('controllers');
		if (!$root) {
			$aco->create(array('parent_id' => null, 'model' => null, 'alias' => 'controllers'));
			$root = $aco->save();
			$root['Aco']['id'] = $aco->id;
			$log[] = 'Created Aco node for controllers';
		} else {
			$root = $root[0];
		}
		App::import('Core', 'File');
		$Controllers = App::objects('controller');
		$appIndex = array_search('App', $Controllers);
		if ($appIndex !== false ) {
			unset($Controllers[$appIndex]);
		}
		$baseMethods = get_class_methods('Controller');
		$baseMethods[] = 'build_acl';
		$Plugins = $this->_getPluginControllerNames();
		$Controllers = array_merge($Controllers, $Plugins);
		// look at each controller in app/controllers
		foreach ($Controllers as $ctrlName) {
			$methods = $this->_getClassMethods($this->_getPluginControllerPath($ctrlName));
			// Do all Plugins First
			if ($this->_isPlugin($ctrlName)){
				$pluginNode = $aco->node('controllers/'.$this->_getPluginName($ctrlName));
				if (!$pluginNode) {
					$aco->create(array('parent_id' => $root['Aco']['id'], 'model' => null, 'alias' => $this->_getPluginName($ctrlName)));
					$pluginNode = $aco->save();
					$pluginNode['Aco']['id'] = $aco->id;
					$log[] = 'Created Aco node for ' . $this->_getPluginName($ctrlName) . ' Plugin';
				}
			}
			// find / make controller node
			$controllerNode = $aco->node('controllers/'.$ctrlName);
			if (!$controllerNode) {
				if ($this->_isPlugin($ctrlName)){
					$pluginNode = $aco->node('controllers/' . $this->_getPluginName($ctrlName));
					$aco->create(array('parent_id' => $pluginNode['0']['Aco']['id'], 'model' => null, 'alias' => $this->_getPluginControllerName($ctrlName)));
					$controllerNode = $aco->save();
					$controllerNode['Aco']['id'] = $aco->id;
					$log[] = 'Created Aco node for ' . $this->_getPluginControllerName($ctrlName) . ' ' . $this->_getPluginName($ctrlName) . ' Plugin Controller';
				} else {
					$aco->create(array('parent_id' => $root['Aco']['id'], 'model' => null, 'alias' => $ctrlName));
					$controllerNode = $aco->save();
					$controllerNode['Aco']['id'] = $aco->id;
					$log[] = 'Created Aco node for ' . $ctrlName;
				}
			} else {
				$controllerNode = $controllerNode[0];
			}
			//clean the methods. to remove those in Controller and private actions.
			foreach ($methods as $k => $method) {
				if (strpos($method, '_', 0) === 0) {
					unset($methods[$k]);
					continue;
				}
				if (in_array($method, $baseMethods)) {
					unset($methods[$k]);
					continue;
				}
				$methodNode = $aco->node('controllers/'.$ctrlName.'/'.$method);
				if (!$methodNode) {
					$aco->create(array('parent_id' => $controllerNode['Aco']['id'], 'model' => null, 'alias' => $method));
					$methodNode = $aco->save();
					$log[] = 'Created Aco node for '. $method;
				}
			}
		}

		if(count($log)>0) {
			debug($log);
		}
		die('hotovo');
	}
	function _getClassMethods($ctrlName = null) {
		App::import('Controller', $ctrlName);
		if (strlen(strstr($ctrlName, '.')) > 0) {
			// plugin's controller
			$num = strpos($ctrlName, '.');
			$ctrlName = substr($ctrlName, $num+1);
		}
		$ctrlclass = $ctrlName . 'Controller';
		$methods = get_class_methods($ctrlclass);
		// Add scaffold defaults if scaffolds are being used
		$properties = get_class_vars($ctrlclass);
		if (array_key_exists('scaffold', $properties)) {
			if($properties['scaffold'] == 'admin') {
				$methods = array_merge($methods, array('admin_add', 'admin_edit', 'admin_index', 'admin_view', 'admin_delete'));
			} else {
				$methods = array_merge($methods, array('add', 'edit', 'index', 'view', 'delete'));
			}
		}
		return $methods;
	}
	function _isPlugin($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) > 1) {
			return true;
		} else {
			return false;
		}
	}
	function _getPluginControllerPath($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[0] . '.' . $arr[1];
		} else {
			return $arr[0];
		}
	}
	function _getPluginName($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[0];
		} else {
			return false;
		}
	}
	function _getPluginControllerName($ctrlName = null) {
		$arr = String::tokenize($ctrlName, '/');
		if (count($arr) == 2) {
			return $arr[1];
		} else {
			return false;
		}
	}
	/**
	 * Get the names of the plugin controllers ...
	 *
	 * This function will get an array of the plugin controller names, and
	 * also makes sure the controllers are available for us to get the
	 * method names by doing an App::import for each plugin controller.
	 *
	 * @return array of plugin names.
	 *
	 */
	function _getPluginControllerNames() {
		App::import('Core', 'File', 'Folder');
		$paths = Configure::getInstance();
		$folder =& new Folder();
		$folder->cd(APP . 'plugins');
		// Get the list of plugins
		$Plugins = $folder->read();
		$Plugins = $Plugins[0];
		$arr = array();
		// Loop through the plugins
		foreach($Plugins as $pluginName) {
			// Change directory to the plugin
			$didCD = $folder->cd(APP . 'plugins'. DS . $pluginName . DS . 'controllers');
			// Get a list of the files that have a file name that ends
			// with controller.php
			$files = $folder->findRecursive('.*_controller\.php');
			// Loop through the controllers we found in the plugins directory
			foreach($files as $fileName) {
				// Get the base file name
				$file = basename($fileName);
				// Get the controller name
				$file = Inflector::camelize(substr($file, 0, strlen($file)-strlen('_controller.php')));
				if (!preg_match('/^'. Inflector::humanize($pluginName). 'App/', $file)) {
					if (!App::import('Controller', $pluginName.'.'.$file)) {
						debug('Error importing '.$file.' for plugin '.$pluginName);
					} else {
						/// Now prepend the Plugin name ...
						// This is required to allow us to fetch the method names.
						$arr[] = Inflector::humanize($pluginName) . "/" . $file;
					}
				}
			}
		}
		return $arr;
	}
	
	
	function user_initAcl() {
		$this->User->query('TRUNCATE TABLE aros_acos');
		// ADMIN - povolim vse
		$this->Acl->allow('admin', 'controllers');
		
		// MANAGER
		$this->Acl->allow('manager', 'controllers');
		// zakazu pristup do nastaveni
		$this->Acl->deny('manager', 'controllers/Users/user_setting');
		// a vsechny metody v nastaveni
		$this->Acl->deny('manager', 'controllers/AnniversaryTypes');
		$this->Acl->deny('manager', 'controllers/ImpositionStates');
		$this->Acl->deny('manager', 'controllers/BusinessSessions/user_delete');
		$this->Acl->deny('manager', 'controllers/BusinessSessionTypes');
		$this->Acl->deny('manager', 'controllers/BusinessSessionStates');
		$this->Acl->deny('manager', 'controllers/AddressTypes');
		$this->Acl->deny('manager', 'controllers/MailTemplates');
		$this->Acl->deny('manager', 'controllers/SolutionStates');
		$this->Acl->deny('manager', 'controllers/ImpositionPeriods');
		$this->Acl->deny('manager', 'controllers/Units');
		$this->Acl->deny('manager', 'controllers/TransactionTypes');
		
		// USER
		$this->Acl->allow('user', 'controllers');
		$this->Acl->deny('user', 'controllers/Users');
		$this->Acl->allow('user', 'controllers/Users/user_login');
		$this->Acl->allow('user', 'controllers/Users/user_logout');
		$this->Acl->allow('user', 'controllers/Users/user_edit');
		$this->Acl->deny('user', 'controllers/UserRegions');
		$this->Acl->allow('user', 'controllers/UserRegions/user_index');
		$this->Acl->deny('user', 'controllers/BusinessSessions/user_delete');
		$this->Acl->deny('user', 'controllers/BusinessPartners/user_edit_user');
		$this->Acl->deny('user', 'controllers/Reps');
		
		// zakazu vsechny metody v nastaveni
		$this->Acl->deny('user', 'controllers/AnniversaryTypes');
		$this->Acl->deny('user', 'controllers/ImpositionStates');
		$this->Acl->deny('user', 'controllers/BusinessSessionTypes');
		$this->Acl->deny('user', 'controllers/BusinessSessionStates');
		$this->Acl->deny('user', 'controllers/AddressTypes');
		$this->Acl->deny('user', 'controllers/MailTemplates');
		$this->Acl->deny('user', 'controllers/SolutionStates');
		$this->Acl->deny('user', 'controllers/ImpositionPeriods');
		$this->Acl->deny('user', 'controllers/Units');
		$this->Acl->deny('user', 'controllers/TransactionTypes');
		
		// zakazu vsechno okolo centralniho skladu
		$this->Acl->deny('user', 'controllers/CSCreditNotes');
		$this->Acl->deny('user', 'controllers/CSInvoices');
		$this->Acl->deny('user', 'controllers/Products');
		$this->Acl->deny('user', 'controllers/ProductVariants');
		$this->Acl->deny('user', 'controllers/CSStorings');
		$this->Acl->deny('user', 'controllers/CSTransactionItems');
		$this->Acl->deny('user', 'controllers/CSTransactions');
		
		$this->Acl->deny('rep', 'controllers');
		
		die('hotovo');
	}
}
?>
