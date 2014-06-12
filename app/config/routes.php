<?php
/* SVN FILE: $Id: routes.php 7296 2008-06-27 09:09:03Z gwoo $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 7296 $
 * @modifiedby		$LastChangedBy: gwoo $
 * @lastmodified	$Date: 2008-06-27 02:09:03 -0700 (Fri, 27 Jun 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.thtml)...
 */
if ($_SERVER['REQUEST_URI'] == '/admin' || $_SERVER['REQUEST_URI'] == '/admin/') {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://www.lekarna-obzor.cz/admin/administrators/login");
	exit();
}

// kontrola, jestli jedeme pres spravny host name
if ( $_SERVER['HTTP_HOST'] != 'www.lekarna-obzor.cz'){
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://www.lekarna-obzor.cz" . $_SERVER['REQUEST_URI']);
	exit();
}

	Router::connect('/', array('controller' => 'contents', 'action' => 'view', 1));
	Router::connect('/kosik', array('controller' => 'carts_products', 'action' => 'index'));
	Router::connect('/vysypat-kosik', array('controller' => 'carts', 'action' => 'dump'));
	Router::connect('/rekapitulace-objednavky', array('controller' => 'orders', 'action' => 'recapitulation'));
	Router::connect('/vyhledavani-produktu', array('controller' => 'searches', 'action' => 'do_search'));
	Router::connect('/registrace', array('controller' => 'customers', 'action' => 'add'));
	Router::connect('/obnova-hesla', array('controller' => 'customers', 'action' => 'password'));

	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

	$url = $_SERVER['REQUEST_URI'];
	$url = ltrim($url, "/");
	
	// routovani kategorii
	Router::connect(
		':slug:category_id',
		array('controller' => 'categories_products', 'action' => 'view'),
		array('slug' => '.*\-c', 'category_id' => '\d+', 'pass' => array('category_id'))
	);
	
	// routovani produktu
	Router::connect(
		'/:slug:product_id',
		array('controller' => 'products', 'action' => 'view'),
		array('slug' => '.*\-p', 'product_id' => '\d+', 'pass' => array('product_id'))
	);
	
	// routovani obsahovych stranek
	App::import('Model', 'Content');
	$this->Content = &new Content;
	
	// obsahove stranky
	Router::connect('/osobni-odber', array('controller' => 'contents', 'action' => 'view', 21));
	Router::connect('/obchodni-podminky', array('controller' => 'contents', 'action' => 'view', 22));
	Router::connect('/o-provozovateli', array('controller' => 'contents', 'action' => 'view', 23));
	Router::connect('/jak-nakupovat', array('controller' => 'contents', 'action' => 'view', 24));
	Router::connect('/kontakty', array('controller' => 'contents', 'action' => 'view', 19));
	Router::connect('/cenik-dopravy', array('controller' => 'contents', 'action' => 'view', 20));
	Router::connect('/novinky', array('controller' => 'contents', 'action' => 'view', 25));
	Router::connect('/vse-o-nakupu', array('controller' => 'contents', 'action' => 'view', 16));
	Router::connect('/prodejna', array('controller' => 'contents', 'action' => 'view', 18));

/**
 * Then we connect url '/test' to our test controller. This is helpfull in
 * developement.
 */
	Router::connect('/tests', array('controller' => 'tests', 'action' => 'index'));
?>