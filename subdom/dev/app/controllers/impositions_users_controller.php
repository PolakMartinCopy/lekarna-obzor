<?php 
class ImpositionsUsersController extends AppController {
	var $name = 'ImpositionsUsers';
	
	function user_xls_export() {
		$data = unserialize($this->data['CSV']['data']);
		$export_fields = unserialize($this->data['CSV']['fields']);
		$this->ImpositionsUser->xls_export($data, $export_fields);
		$this->redirect('/' . $this->ImpositionsUser->export_file);
	}
}
?>