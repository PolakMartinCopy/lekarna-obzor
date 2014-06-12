<?php 
class AppController extends Controller {
	var $components = array(
		'Session',
		'Auth' => array(
			'userModel' => 'Administrator',
			'fields' => array('username' => 'login', 'password' => 'password'),
			'loginAction' => array(
				'controller' => 'administrators',
				'action' => 'login',
				'admin' => false
			),
			'loginRedirect' => array('controller' => 'rates', 'action' => 'index', 'admin' => true),
			'loginError' => 'Vloženy nesprávné přihlašovací údaje.',
			'authError' => 'Před vstupem do administrace se prosím přihlašte.',
		)
	);
	
	function beforeFilter() {
		// zakazu cache v prohlizeci
		$this->disableCache();
		// nastaveni hashovaci funkce pro auth component
		Security::setHash('md5');
		// zjistim administratora
		$administrator = $this->Auth->user();
		$this->set('administrator', $administrator);
	}
}
?>