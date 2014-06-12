<?
	function my_iconv($string){
		$string = iconv("UTF-8", "windows-1250", $string);
		return $string;
	}

	header('Content-Type: text/html; charset=windows-1250');

	// kontrola, zda je zadane jmeno layoutu, pro ktery budu
	// skladat linky
	if ( !isset($_GET['layout_name']) ){
		die('<!-- ERROR: Neni definovano jmeno layoutu, pro ktery se maji odkazy sestavit. -->');
	} else {
		$layout_name = $_GET['layout_name'];
	}

	// naimportuji si knihovnu pro praci s XML
	require 'lib.xml.php';
	
	// nadefinuji URI
	$uri = isset($_GET['ru']) ? base64_decode($_GET['ru']) : '/';

	// vytvorim si objekt pro praci s XML
	$xml_handler = &new ParseXML;
	
	// natahnu si XML
	$xml = $xml_handler->GetXMLTree($layout_name);

	if ( !empty($xml) ){
		$separator = base64_decode($xml['LAYOUT'][0]['SETTINGS'][0]['SEPARATOR'][0]['VALUE']);
		echo base64_decode($xml['LAYOUT'][0]['SETTINGS'][0]['PRE'][0]['VALUE']);
		$count = count($xml['LAYOUT'][0]['LINK']);
		$links = array();
		for ( $i = 0; $i < $count; $i++ ){
			if ( isset($xml['LAYOUT'][0]['LINK'][$i]['URI'][0]['LOCATION']) ){
				$uris = array();
				$count2 = count($xml['LAYOUT'][0]['LINK'][$i]['URI'][0]['LOCATION']); 
				for ( $j = 0; $j < $count2; $j++ ){
					$uris[] = $xml['LAYOUT'][0]['LINK'][$i]['URI'][0]['LOCATION'][$j]['VALUE'];
				}
				
				// mam vytazene uris, podle toho si urcim, jestli skipuju nektery z linku
				// typ 1 znamena na vsech strankach, vyjma tech co jsou v uris
				if ( $type == 1 && in_array($uri, $uris) ){
					continue;
				}
				
				// typ 2 znamena pouze na vyjmenovanych strankach
				if ( $type == 2 && !in_array($uri, $uris) ){
					continue;
				}
			}
			
			$type = $xml['LAYOUT'][0]['LINK'][$i]['TYPE'][0]['VALUE'];
			$target = html_entity_decode($xml['LAYOUT'][0]['LINK'][$i]['TARGET'][0]['VALUE']);
			$title = html_entity_decode($xml['LAYOUT'][0]['LINK'][$i]['TITLE'][0]['VALUE']);
			$anchor = html_entity_decode($xml['LAYOUT'][0]['LINK'][$i]['ANCHOR'][0]['VALUE']);
			$pre = iconv('windows-1250', 'UTF-8', base64_decode($xml['LAYOUT'][0]['LINK'][$i]['PRE'][0]['VALUE']));
			$post = iconv('windows-1250', 'UTF-8', base64_decode($xml['LAYOUT'][0]['LINK'][$i]['POST'][0]['VALUE']));

			if ( eregi("\n", $anchor) ){
				$anchor = explode("\n", $anchor);
				$max = count($anchor) - 1;
				$index = rand(0, $max);
				$anchor = $anchor[$index];
			}
			
			// sestavim linky
			$links[] = ( !empty($pre) ? $pre . ' ' : '' ) . '<a href="' . $target . '"' . ( !empty($title) ? ' title="' . $title . '"' : '' ) . '>' . $anchor . '</a>' . ( !empty($post) ? ' ' . $post : '' );
		}
	
		$links = implode($separator, $links); 
		echo $links;
		echo base64_decode($xml['LAYOUT'][0]['SETTINGS'][0]['POST'][0]['VALUE']);
	} else {
		die('<!-- ERROR: Soubor s XML je prazdny. -->');
	}
?>