<?php
class AppController extends Controller {
	var $components = array(
		'Session',
		'Auth' => array(
			'userModel' => 'User',
			'fields' => array('username' => 'login', 'password' => 'password'),
			'loginAction' => array(
				'controller' => 'users',
				'action' => 'login',
				'user' => false
			),
			'loginRedirect' => array('user' => true, 'controller' => 'customers', 'action' => 'index'),
			'loginError' => 'Vloženy nesprávné přihlašovací údaje.',
			'authError' => 'Před vstupem do systému se prosím přihlašte.',
//			'flashElement' => 'flash_failure'
		)
	);
	
	function beforeFilter() {
		Security::setHash('md5');
	}
	
	function beforeRender() {
		$user = $this->Auth->user();
		$this->set('user', $user);
		
		// zakaz kesovani
		$this->disableCache();
	}
}