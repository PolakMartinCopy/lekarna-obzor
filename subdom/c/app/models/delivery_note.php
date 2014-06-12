<?php 
App::import('Model', 'Transaction');
class DeliveryNote extends Transaction {
	var $name = 'DeliveryNote';
	
	var $useTable = 'transactions';
	
	var $export_file = 'files/delivery_notes.csv';
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
	
	function beforeFind($queryData) {
		$queryData['conditions']['DeliveryNote.transaction_type_id'] = 1;
		return $queryData;
	}
	
	function pdf_generate($id) {
		$file_name = DL_FOLDER . $id . '.pdf';
		if ($fp = fopen($file_name, "w")) {
			$url = 'http://' . $_SERVER['HTTP_HOST'] . '/delivery_notes/view_pdf/' . $id;
			$ch = curl_init($url);
				
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, false);
		
			curl_exec($ch);
			curl_close($ch);
			
			fclose($fp);
		}
	}
	
}
?>