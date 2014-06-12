<?
class AppController extends Controller {
	// beforeFilter se provede pri uplne kazde akci, ktera se vykona
	function beforeFilter(){
		// osetruju pristup na /admin/ a na /admin
		// kdyz nekdo pritupuje na tyto adresy, je presmerovan na
		// stranku s loginem
		Controller::disableCache();

		if ( $_SERVER['REQUEST_URI'] == '/admin/' || $_SERVER['REQUEST_URI'] == '/admin' ){
			$this->redirect(array('controller' => 'administrators', 'action' => 'index', 'prefix' => 'admin', 'admin' => true), null, true);
		}

		// otestuju, zda nekdo chce pristoupit do adminu
		// ale zaroven testuju, zda se nesnazi zalogovat
		if ( isset($this->params['admin']) && $this->params['admin'] == '1' && $this->action != 'admin_login' ){
			// jestlize clovek pristupuje do adminu
			// a jeste se nezalogoval, musim ho presmerovat na stranku
			// s loginem
			if ( !$this->Session->check('Administrator') ){
				// zapamatuju si, kam se chtel clovek zalogovat
				$this->Session->write('request_uri', $_SERVER['REQUEST_URI']);
				// donutim uzivatele zalogovat se
				$this->redirect('/admin/administrators/login', null, true);
			}
		}
		
		if ( isset($this->params['prefix']) && $this->params['prefix'] == 'users') {
			if ( !$this->Session->check('Company') ) {
				// nektere stranky jsou povolene
				$allowed_actions = array(
					'users_login',
					'users_password_recovery'
				);
				
				if ( ( $this->params['controller'] != 'companies' || !in_array($this->params['action'], $allowed_actions) ) ){
					$this->Session->setFlash('Pro přístup k této stránce se musíte přihlásit.');
					$this->redirect('/users/companies/login', null, true);
				}
			}
		}
		
		if ( isset($this->params['prefix']) && $this->params['prefix'] == 'rep') {
			if ( !$this->Session->check('Rep') ) {
				// nektere stranky jsou povolene
				$allowed_actions = array(
					'rep_login'
				);
				
				if ( !in_array($this->params['action'], $allowed_actions) ) {
					$this->Session->setFlash('Pro přístup k této stránce se musíte přihlásit.');
					$this->redirect('/rep/reps/login', null, true);
				}
			}
		}
	}
	
	function beforeRender() {
		// data pro zobrazeni menu v administraci
		App::import('Model', 'Order');
		$this->Order = &new Order;
		$statuses = $this->Order->Status->find('all', array(
			'contain' => array()
		));

		foreach ( $statuses as $key => $value ){
			$statuses[$key]['Status']['count'] = $this->Order->find('count', array(
				'conditions' => array('status_id' => $statuses[$key]['Status']['id'])
			));
		}
		$this->set('menu_statuses', $statuses);
		
		App::import('Model', 'Product');
		$this->Product = &new Product;
		$categories = $this->Product->CategoriesProduct->Category->find('threaded', array(
			'contain' => array(
				'CategoriesProduct' => array(
					'Product' => array(
						'fields' => array('id', 'name')
					)
				)
			)
		));

		$this->set('menu_categories', $categories);
		
		App::import('Model', 'Rep');
		$this->Rep = &new Rep;
		$reps = $this->Rep->find('all');

		$this->set('menu_reps', $reps);
	}

} // konec tridy
?>