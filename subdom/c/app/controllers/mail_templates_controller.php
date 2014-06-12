<?
class MailTemplatesController extends AppController{
	var $name = 'MailTemplates';
	
	var $left_menu_list = array('settings', 'mail_templates');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'settings');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index(){
		$mail_templates = $this->MailTemplate->find('all');
		$this->set('mail_templates', $mail_templates);
	}

	function user_add(){
		if ( isset($this->data) ){
			if ( $this->MailTemplate->save($this->data) ){
				$this->Session->setFlash('Šablona byla uložena.');
				$this->redirect(array('controller' => 'mail_templates', 'action' => 'index'));
			}
			$this->Session->setFlash('Šablona nebyla uložena kvuli chybám ve formuláři, zkontrolujte prosím data.');
		}
	}

	function user_edit($id){
		$this->MailTemplate->recursive = -1;
		$this->MailTemplate->id = $id;
		$mail_template = $this->MailTemplate->read();
		$this->set('mail_template', $mail_template);
		
		if ( !isset($this->data) ){
			$this->data = $mail_template;
		} else {
			if ( $this->MailTemplate->save($this->data) ){
				$this->Session->setFlash('Šablona byla upravena.');
				$this->redirect(array('controller' => 'mail_templates', 'action' => 'index'));
			}
			$this->Session->setFlash('Šablona nebyla uložena kvuli chybám ve formuláři, zkontrolujte prosím data.');
		}
	}

	function user_del($id){
		$this->Session->setFlash('Šablona nemohla být vymazána.');
		if ( $this->MailTemplate->delete($id) ){
			$this->Session->setFlash('Šablona byla vymazána.');
		}
		$this->redirect(array('controller' => 'mail_templates', 'action' => 'index'));
	}
}
?>