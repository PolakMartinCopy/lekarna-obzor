<?php 
class FormsController extends AppController {
	var $name = 'Forms';
	
	function prescription_only_medicine() {
		if (isset($this->data)) {
			$this->Form->set($this->data);
			if ($this->Form->validates($this->data)) {
				if ($this->Form->send($this->data)) {
					$this->Session->setFlash('Váš požadavek byl odeslán. V blízké době Vás budeme kontaktovat.');
					$this->redirect(array('controller' => 'forms', 'action' => 'prescription_only_medicine'));
				} else {
					$this->Session->setFlash('Váš požadavek se nepodřilo odeslat.');
				}
			} else {
				$this->Session->setFlash('Váš požadavek se nepodřilo odeslat. Opravte chyby ve formuláři a odešlete jej prosím znovu.');
			}
		}
		$this->layout = 'content';
		$this->set('title_for_content', 'Objednávka léků na předpis');
		$this->set('description_for_content', 'Objednejte si u nás k vyzvednutí léky na předpis nebo jen zjistěte naši cenu!');
	}
}
?>