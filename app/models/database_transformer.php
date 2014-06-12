<?

class DatabaseTransformer extends AppModel {

	var $name = 'DatabaseTransformer';

	

	var $useTable = false;

	

	function combine($array) {

		$res = array();

		$first = current($array);

		array_shift($array);

		$tail = $array;

		if (empty($tail)) {

			foreach ($first as $item) {

				$res[] = array($item);

			}

		} else {

			foreach ($first as $item) {

				foreach ($this->combine($tail) as $j) {

					$res[] = array_merge(array($item), $j);

				}

			}

		}

		return $res;

	}

}

?>