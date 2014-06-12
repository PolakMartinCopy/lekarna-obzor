<?
class ZipComponent extends Object {
	var $controller = true;

	function startup(&$controller){
	}

	function unzipFile($file_in){
		debug($file_in);

/*		$handle = zip_open($file_in, 'r');
		zip_close($handle);*/

//		$bz = gzfile($file_in);
//		$out = fopen('upload/fout.txt', "wb");

//		$buffer = bzread($bz);
//		debug($bz);
/*		while ( $buffer = bzread($bz, 4096) ){
			debug($buffer);
			fwrite($out, $buffer, 4096);
		}*/
//		bzclose($bz);
//		fclose($out);


/*
			// musim si zkontrolovat priponu obrazku
			$file_ext = explode(".", $this->data['Image']['image']['name']);
			$file_ext = $file_ext[count($file_ext) - 1];
			
			// jedna se o zipovy soubor,
			// musim ho rozbalit a zpracovat
			if ( $file_ext == "zip" ){
				if ( move_uploaded_file($this->data['Image']['image']['tmp_name'], 'upload/' . $this->data['Image']['image']['name']) ){
					$this->Zip->unzipFile('upload/' . $this->data['Image']['image']['name']);
				}
			}
			debug($file_ext);
			die();

*/

	}
}
?>