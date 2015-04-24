<?php
/* SVN FILE: $Id: bootstrap.php 6311 2008-01-02 06:33:52Z phpnut $ */
/**
 * Short description for file.
 *
 * Long description for file
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
 * @since			CakePHP(tm) v 0.10.8.2117
 * @version			$Revision: 6311 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-01-01 22:33:52 -0800 (Tue, 01 Jan 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */
define('HP_URI', '/');

function strip_diacritic($text, $strip_dot = true) {
	$text = trim($text);
	
	$text = str_replace(",", "-", $text); // carky
	$text = str_replace("(", "", $text); // leve zavorky
	$text = str_replace(")", "", $text); // prave zavorky
	$text = str_replace("&amp;", "a", $text); // prave zavorky
	$text = str_replace("&", "a", $text); // prave zavorky
	$text = str_replace("?", "", $text); // prave zavorky
	$text = str_replace("%", "", $text); // procenta

	$text = str_replace('´', '', $text); // apostrof
	$text = str_replace("'", "", $text); //apostrof
	$text = str_replace('"', '', $text); //uvozovky
	$text = str_replace("/", "", $text); // lomitko
	$text = str_replace("+", "-", $text);
	
	if ($strip_dot) {
		$text = str_replace(".", "", $text); // tecka
	}

	// odstranim pismena s diakritikou
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', 'Ř'=>'R', 'ř'=>'r', 'Ť'=>'T', 'ť'=>'t', 'Ě'=>'E', 'ě'=>'e',
    	'Ň'=>'N', 'ň'=>'n', 'ú'=>'u', 'Ú'=>'U', 'ů'=>'u', 'Ů'=>'U', 'ď'=>'d', 'Ď'=>'d', 'ü'=>'u'
    );
    $text = strtr($text, $table);

	// mezery nahradim pomlckama (jedna pomlcka i za vice mezer)
	$text = preg_replace('/\s+/', '-', $text);

	// hodim text na mala pismena
	$text = strtolower($text);
	
	// odstranim vic pomlcek za sebou
	while (preg_match('/--/', $text)) {
		$text = preg_replace('/--/', '-', $text);
	}

	return $text;
}

function cz_date_time($datetime){
	$dt = strtotime($datetime);
	$dt = strftime("%d-%m-%Y %H:%M", $dt);
	return $dt;
}

/** Kontrola e-mailové adresy
* @param string $email e-mailová adresa
* @return bool syntaktická správnost adresy
* @copyright Jakub Vrána, http://php.vrana.cz
*/
function valid_email($email) {
    $atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]'; // znaky tvořící uživatelské jméno
    $domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // jedna komponenta domény
    return eregi("^$atom+(\\.$atom+)*@($domain?\\.)+$domain\$", $email);
}

function eval_expression($expression){
	// uprava pole s cenou, aby se mohly vkladat vyrazy
	$code = "\$number = (" . $expression . ") * 1;";
	eval($code);
	return floor($number);
}

function resize($filename, $max_x = 100, $max_y = 100) {
	// musim si u kazdeho obrazku zjistit jeho rozmery
	$i = getimagesize($filename);
	
	if ( $max_x < $i[0] OR $max_y < $i[1]){
		// vim ze rozmer je vetsi nez povolene rozmery
		if ( $max_x < $i[0] ){
			// zmensim ho nejdriv po ose X
			$xratio = $i[0] / $max_x;
			$i[0] = $max_x;
    		$i[1] = round($i[1] / $xratio);
		}
		
		if ( $max_y < $i[1] ){
			// pokud to jeste porad nestacilo po ose X,
			// zmensim si ho po ose Y
			$yratio = $i[1] / $max_y;
			$i[1] = $max_y;
			$i[0] = round($i[0] / $yratio);
		}
	}
	
	return array($i[0], $i[1]);
}

function download_url($url = null) {
	if ($url) {
		$content = false;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}
	return false;
}

define('CUST_MAIL', 'eshop@pharmacorp.cz');
define('CUST_ROOT', 'lekarna-obzor.cz');
define('CUST_NAME', 'Lékarna Obzor CZ');
?>