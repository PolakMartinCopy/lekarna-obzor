<?
class StatusesController extends AppController {
	var $name = 'Statuses';

	function beforeFilter(){
		// musim si zavolat puvodni beforeFilter
		parent::beforeFilter();
		
		$this->Status->MailTemplate->recursive = -1;
		$mail_templates = $this->Status->MailTemplate->find('all');
		$mail_templates = Set::combine($mail_templates, '{n}.MailTemplate.id', '{n}.MailTemplate.subject');
		$this->set('mailTemplates', $mail_templates);
	}
	
	function admin_index(){
		$this->layout = 'admin';
		$this->Status->recursive = 0;
		$statuses = $this->Status->find('all');
		$this->set('statuses', $statuses);
	}
	
	function admin_add(){
		$this->layout = 'admin';
		if ( isset($this->data) ){
			if ( $this->Status->save($this->data) ){
				$this->Session->setFlash('Status byl uložen!');
				$this->redirect(array('action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Chyba při ukládání statusu, zkontrolujte prosím všechna pole!');
			}
		}
	}
	
	function admin_edit($id){
		$this->layout = 'admin';
		if ( !isset($this->data) ){
			$this->Status->recursive = -1;
			$this->data = $this->Status->read(null, $id);
			if ( empty($this->data) ){
				$this->Session->setFlash('Neexistující status!');
				$this->redirect(array('action' => 'index'), null, true);
			}
			
			$mail_templates = $this->Status->MailTemplate->find('list');
		} else {
			if ( $this->Status->save($this->data) ){
				$this->Session->setFlash('Status byl upraven!');
				//$this->redirect(array('action' => 'edit', 'id' => $this->Status->id), null, true);
				$this->redirect(array('action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Chyba při úpravě statusu!');
			}
		}
	}
	
	function admin_delete($id){
		if ( !isset($id) ){
			$this->Session->setFlash('Neexistující status!');
			$this->redirect(array('action' => 'index'), null, true);
		} else {
			if ( $this->Status->Order->find('count', array(
				'conditions' => array(
					'status_id' => $id
				)
			)) != 0 ){
				$this->Session->setFlash('Tento status je přiřazen k některým existujícím objednávkám, nelze jej proto vymazat!');
			} else {
				$this->Status->del($id);
				$this->Session->setFlash('Status byl vymazán!');
			}
			$this->redirect(array('action' => 'index'), null, true);
		}
	}
}
?>