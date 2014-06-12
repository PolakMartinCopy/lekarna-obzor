<?
class AppController extends Controller {
	// musim si importnout cookie, potrebuju je pouzivat	
	var $components = array('Cookie', 'Session');

	// beforeFilter se provede pri uplne kazde akci, ktera se vykona
	function beforeFilter() {
		Controller::disableCache();
		
		// pridam si LayoutHelper
		$this->helpers[] = 'Layout';

		if ( !$this->Session->check('Config') || !$this->Session->check('Config.rand') ) {
			$this->Session->_checkValid();
			if ( !$this->Session->check('Config.rand') ) {
				$this->Session->write(array('Config.rand' => rand()));
			}
		}
		
		App::import('Model', 'Redirect');
		$this->Redirect = &new Redirect;
		if ($this->Redirect->hasAny(array('Redirect.request_uri' => $_SERVER['REQUEST_URI']))) {
			$redirect = $this->Redirect->find('first', array(
				'conditions' => array('Redirect.request_uri' => $_SERVER['REQUEST_URI']),
				'contain' => array(),
				'fields' => array('Redirect.target_uri')
			));
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: http://www.lekarna-obzor.cz" . $redirect['Redirect']['target_uri']);
			exit();
		}
		
		// otestuju, zda nekdo chce pristoupit do adminu
		// ale zaroven testuju, zda se nesnazi zalogovat
		if ( isset($this->params['admin']) && $this->params['admin'] == '1' && $this->action != 'admin_login' ){
			// jestlize clovek pristupuje do adminu
			// a jeste se nezalogoval, musim ho presmerovat na stranku
			// s loginem
			if ( !$this->Session->check('Administrator') ){
				// musim zkontrolovat, zda nema clovek ulozenu cookie,
				// na "dlouhe prihlaseni"
				$cookie = $this->Cookie->read('Administrator');
				if ( !empty($cookie) ){
					App::import('Model', 'Administrator');
					$this->Administrator = new Administrator;
					$admin = $this->Administrator->recursive = -1;
					
					// musim zjistit, jestli heslo ulozene v cookies koresponduje
					// s heslem v databazi
					if ( $cookie['password'] == md5($admin['Administrator']['password'] . Configure::read('Security.salt')) ){
						// jestli ma tak si pretahnu data z cookie do session a jedeme dal
						$this->Session->write('Administrator', $cookie['Administrator']);
					} else {
						// donutim uzivatele zalogovat se
						$this->redirect('/admin/administrators/login', null, true);
					}
				} else {
					// donutim uzivatele zalogovat se
					$this->redirect('/admin/administrators/login', null, true);
				}
			}
			
			// administrator ma session timeout 720 sekund * 10 = 2 hodiny
			Configure::write('Session.timeout', 720);

		}
	}
	
	function beforeRender() {
		// pokud jsem ve front endu
		if (!isset($this->params['admin'])) {
			// natahnu si modely pro odeslani doporuceni a prihlaseni se k odberu novinek, abych mohl jejich validacni chybove hlasky vypisovat v pohledu (v paticce)
			$this->loadModel('Recommendation');
			$this->loadModel('Subscriber');
			// volam fci, ktera mi pres sesnu zajisti prenost validacnich hlasek mezi modely
			$this->_persistValidation();
			
			App::import('Model', 'Product');
			$this->Product = &new Product;
			
			$opened_category_id = 5;
			if (isset($this->viewVars['opened_category_id'])) {
				$opened_category_id = $this->viewVars['opened_category_id'];
			}
			
			// data o kosiku
			$this->set('carts_stats', $this->Product->CartsProduct->getStats($this->Product->CartsProduct->Cart->get_id()));

 			// menu kategorie
			$this->set('categories_menu', $this->Product->CategoriesProduct->Category->getSubcategoriesMenuList(5, $opened_category_id));
			
			// nejnovejsi produkt
			if ($this->layout == 'homepage') {
				// nejnovejsi produkty
				$this->set('hp_categories_list', $this->Product->CategoriesProduct->Category->get_homepage_list());
				$this->set('newest', $this->Product->get_list('newest'));
			} 
			if ($this->layout != 'product_detail') {
				$this->set('most_sold', $this->Product->get_list('most_sold'));
			}
		}
		$this->disableCache();
	}
	
	/**
	 * Called with some arguments (name of default model, or model from var $uses),
	 * models with invalid data will populate data and validation errors into the session.
	 *
	 * Called without arguments, it will try to load data and validation errors from session
	 * and attach them to proper models. Also merges $data to $this->data in controller.
	 *
	 * @author poLK
	 * @author drayen aka Alex McFadyen
	 *
	 * Licensed under The MIT License
	 * @license            http://www.opensource.org/licenses/mit-license.php The MIT License
	 */
	function _persistValidation() {
		$args = func_get_args();
	
		if (empty($args)) {
			if ($this->Session->check('Validation')) {
				$validation = $this->Session->read('Validation');
				$this->Session->delete('Validation');
				foreach ($validation as $modelName => $sessData) {
					if ($this->name != $sessData['controller']){
						if (in_array($modelName, $this->modelNames)) {
							$Model =& $this->{$modelName};
						} elseif (ClassRegistry::isKeySet($modelName)) {
							$Model =& ClassRegistry::getObject($modelName);
						} else {
							continue;
						}
	
						$Model->data = $sessData['data'];
						$Model->validationErrors = $sessData['validationErrors'];
						$this->data = Set::merge($sessData['data'],$this->data);
					}
				}
			}
		} else {
			foreach($args as $modelName) {
				if (in_array($modelName, $this->modelNames) && !empty($this->{$modelName}->validationErrors)) {
					$this->Session->write('Validation.'.$modelName, array(
						'controller' => $this->name,
						'data' => $this->{$modelName}->data,
						'validationErrors' => $this->{$modelName}->validationErrors
					));
				}
			}
		}
	}
} // konec tridy
?>