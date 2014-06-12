<?
class PostmansController extends AppController{
	var $name = 'Postmans';

	function send(){
		$objects = array(
			array(
				'name' => 'Order',
				'id' => 100
			),
			array(
				'name' => 'Status',
				'id' => 3
			)
		);
		$this->Postman->send(1, $objects);
	}
}
?>