<?php
class Image extends AppModel {

	var $name = 'Image';

	var $order = array(
		'Image.is_main' => 'desc'
	);

	var $validate = array(
		'name' => array(
			'rule' => array('minLength', 1)
		),
		'product_id' => array(
			'rule' => 'numeric'
		),
		'order' => array(
			'rule' => 'numeric'
		),
	);

	function resize($file_in, $file_out, $max_x, $max_y = 0) {
	    $imagesize = getimagesize($file_in);

		if ((!$max_x && !$max_y) || !$imagesize[0] || !$imagesize[1]) {
	        return false;
	    }
	    switch ($imagesize[2]) {
	        case 1:
				$img = imagecreatefromgif($file_in);
			break;
	        case 2:
				$img = imagecreatefromjpeg($file_in);
			break;
	        case 3:
				$img = imagecreatefrompng($file_in);
			break;
	        default:
				return false;
			break;
	    }

	    if (!$img) {
	        return false;
	    }

	    if ($max_x) {
	        $width = $max_x;
	        $height = round($imagesize[1] * $width / $imagesize[0]);
	    }
	    
	    if ($max_y && (!$max_x || $height > $max_y)) {
	        $height = $max_y;
	        $width = round($imagesize[0] * $height / $imagesize[1]);
	    }

	    // vytvori cerny obrazek o definovanych rozmerech (pozadovane velikosti miniatury)
	    $img2 = imagecreatetruecolor($max_x, $max_y);
	    // z cerneho pozadi udelam bile
	    $background = imagecolorallocate ($img2, 255, 255, 255);
	    imagefill ($img2, 0, 0, $background);

	    $dest_x = round(($max_x - $width) / 2);
	    $dest_y = round(($max_y - $height) / 2);

	    // do bileho obrazku o pozadovanych rozmerech vlozim miniaturu puvodniho na stred (z obou boku budou bile pruhy)
	    imagecopyresampled($img2, $img, $dest_x, $dest_y, 0, 0, $width, $height, $imagesize[0], $imagesize[1]);
	    if ($imagesize[2] == 2) {
	        $return = imagejpeg($img2, $file_out, 80);
			@imagedestroy($return);
	    } elseif ($imagesize[2] == 1 && function_exists("imagegif")) {
	        imagetruecolortopalette($img2, false, 256);
	        $return = imagegif($img2, $file_out);
			@imagedestroy($return);
	    } else {
	        $return = imagepng($img2, $file_out);
			@imagedestroy($return);
	    }
		return $return;
	}
	
	function deleteImage($id){
		if ( !$id ){
			return false;
		} else {
			$image = $this->read(array('id', 'name'), $id);
			if ( file_exists('product-images/' . $image['Image']['name']) ){
				unlink('product-images/' . $image['Image']['name']);
			}
			if ( file_exists('product-images/small/' . $image['Image']['name']) ){
				unlink('product-images/small/' . $image['Image']['name']);
			}
			if ( file_exists('product-images/medium/' . $image['Image']['name']) ){
				unlink('product-images/medium/' . $image['Image']['name']);
			}

			if ( $this->delete($id) ){
				return true;
			}
		}
	}

	function deleteAllImages($id = null){
		if ( !$id ){
			return false;
		} else {
			$images = $this->find('all', array(

				'conditions' => array('product_id' => $id),

				'fields' => array('id')

			));
			foreach ( $images as $image ){
				$this->deleteImage($image['Image']['id']);
			}
		}
	}
	
	function makeThumbnails($file_in, $file_out = null){
		if ( !$file_out ){
			$file_out = explode("/", $file_in);
			$file_out = $file_out[count($file_out) - 1];
		} else {
			$file_out = explode("/", $file_out);
			$file_out = $file_out[count($file_out) - 1];
		}

		$file_out_small = 'product-images/small/' . $file_out;
		$file_out_medium = 'product-images/medium/' . $file_out;
		
		// vytvorim si maly nahledovy obrazek
		$result = $this->resize($file_in, $file_out_small, 206, 100);

		// vytvorim si stredni nahledovy obrazek
		$this->resize($file_in, $file_out_medium, 250, 250);
		return (true);
	}

	function isLoadable($file_in){
		$image_properties = getimagesize($file_in);
		$total_memory = $image_properties[0] * $image_properties[1] * $image_properties['bits'];
		if ( $total_memory > 8388607 ){
			return false;
		}
		return true;
	}
	
	function checkName($name_in){
		// predpokladam, ze obrazek s
		// takovym jmenem neexistuje
		$name_out = $name_in;
		
		// pokud existuje, musim zkouset zda neexistuje s _{n}
		// az dokud se najde jmeno s cislem, ktere neexistuje
		if ( file_exists($name_in) ){
			$i = 1;
			$new_fileName = str_replace('.', '_' . $i . '.', $name_in);
			while ( file_exists($new_fileName ) ){
				$i++;
				$new_fileName = str_replace('.', '_' . $i . '.', $name_in);
			}
			$name_out = $new_fileName;
		}
		return $name_out;
	}
}
?>