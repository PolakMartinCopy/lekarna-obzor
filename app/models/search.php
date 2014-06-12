<?php
class Search extends AppModel {

	var $name = 'Search';
	var $useTable = false;
	
	function doSearch($query, $start){
		// nactu si XML do pole a vratim
		return $this->loadXML($query, $start);
	}

	function loadXML($query, $start){
		// nastaveni vyhledavace pomoci GET parametru
		// nejdriv si parametry nadefinuju

		$params = array();
		$params['cx'] = "010876191616094810175:nh4ey9ow_ii";
		$params['client'] = "google-csbe";
		$params['ie'] = "windows-1250";
		$params['oe'] = "windows-1250";
		$params['output'] = "xml_no_dtd";
		$params['q'] = urlencode($query);
		$params['lr'] = "lang_cs";
		$params['start'] = $start;
		$params['filter'] = 0;

		// pretransformuju si je do URL
		$urlParams = array();
		foreach ( $params as $param => $value ){
			$value = &$value;
			$urlParams[] = "$param=$value";
		}
		$urlParams = implode("&", $urlParams);

		// nazev souboru, ktery budu stahovat
		$file = "http://www.google.com/search?" . $urlParams;

		// natahnu si tridu pro praci s XML
		App::import('Vendor', 'ParseXML', array('file' => 'lib-xml/lib.xml.php'));

		// vytvorim si instanci
		$xmlparser = &new ParseXML;

		// nactu XML a prevedu do pole
		$xml = $xmlparser->GetXMLTree($file);
		return $xml;
	}

}
?>