<?php 
class SettingsController extends AppController {
	var $name = 'Settings';
	
	function admin_edit() {
		$settings = $this->Setting->find('first');
		
		if (isset($this->data)) {
			if ($this->Setting->save($this->data)) {
				$this->Session->setFlash('Nastavení bylo upraveno.');
				$this->redirect(array('action' => 'edit'));
			} else {
				$this->Session->setFlash('Nastavení se nepodařilo upravit. Opakujte prosím akci.');
			}
		} else {
			$this->data = $settings;
		}
		
		$this->set('settings', $settings);
	}
}
?>