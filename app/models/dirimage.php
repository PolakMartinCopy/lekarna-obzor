<?
class Dirimage extends AppModel {
	var $name = 'Dirimage';

	var $useTable = false;

	function getResized($file){
		$max_x = 180;
		$max_y = 180;
		$imagesize = getimagesize('upload/images/' . $file);
	    if ($max_x) {
	        $width = $max_x;
	        $height = round($imagesize[1] * $width / $imagesize[0]);
	    }
	    if ($max_y && (!$max_x || $height > $max_y)) {
	        $height = $max_y;
	        $width = round($imagesize[0] * $height / $imagesize[1]);
	    }
		
		return array('width' => $width, 'height' => $height);
	}
}
?>