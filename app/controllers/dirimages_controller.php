<?php
class DirimagesController extends AppController {
   var $name = 'Dirimages';

	function admin_list($id){
		$handle = opendir('upload/images/');
		$files = array();
		while ( false !== ( $file = readdir($handle) ) ) {
			if ($file != "." && $file != "..") {
				$size = $this->Dirimage->getResized($file);
        		$files[] = am($size, array('destination' => '/upload/images/' . $file, 'name' => $file));
			}
		}
		closedir($handle);
		$this->set('files', $files);
		$this->set('product_id', $id);
	}
}
?>