<?
class MailTemplatesController extends AppController{
	var $name = 'MailTemplates';
	
	function admin_index(){
		$mail_templates = $this->MailTemplate->find('all');
		$this->set('mail_templates', $mail_templates);
	}

	function admin_add(){
		if ( isset($this->data) ){
			if ( $this->MailTemplate->save($this->data) ){
				$this->Session->setFlash('Šablona byla uložena.');
				$this->redirect(array('controller' => 'mail_templates', 'action' => 'index'));
			}
			$this->Session->setFlash('Šablona nebyla uložena kvuli chybám ve formuláři, zkontrolujte prosím data.');
		}
	}

	function admin_edit($id){
		if ( !isset($this->data) ){
			$this->MailTemplate->recursive = -1;
			$this->MailTemplate->id = $id;
			$this->data = $this->MailTemplate->read();
		} else {
			if ( $this->MailTemplate->save($this->data) ){
				$this->Session->setFlash('Šablona byla upravena.');
				$this->redirect(array('controller' => 'mail_templates', 'action' => 'index'));
			}
			$this->Session->setFlash('Šablona nebyla uložena kvuli chybám ve formuláři, zkontrolujte prosím data.');
		}
	}

	function admin_del($id){
		$this->Session->setFlash('Šablona nemohla být vymazána.');
		if ( $this->MailTemplate->delete($id) ){
			$this->Session->setFlash('Šablona byla vymazána.');
		}
		$this->redirect(array('controller' => 'mail_templates', 'action' => 'index'));
	}
}
?>