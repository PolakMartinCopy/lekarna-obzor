<?php
class ContentsController extends AppController{
	var $name = 'Contents';

	var $scaffold = 'admin';
	
	function beforeFilter() {
		parent::beforeFilter();
		$helpers = array('Html', 'Javascript', 'Form');
		$this->helpers = array_unique(array_merge($this->helpers, $helpers));
	}
	
	function view($id) {
		// navolim si layout stranky
		$this->layout = 'content';
		// natvrdo layout hlavni stranky
		if (in_array($id, array(1))) {
			$this->layout = 'homepage';
		}

		$page = $this->Content->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));

		if (!empty($page)) {
			$this->set('page_content', $page['Content']['content']);
			$this->set('title_for_content', $page['Content']['title']);
			$this->set('description_for_content', $page['Content']['description']);
			
			// sestavim breadcrumbs
			$breadcrumbs = array(
				array('anchor' => 'Domů', 'href' => HP_URI),
				array('anchor' => $page['Content']['title'], 'href' => '/' . $page['Content']['path'])
			);
			$this->set('breadcrumbs', $breadcrumbs);
			
		} else {
			die('404 nenalezeno');
		}
	}
	
	function admin_index() {
		$contents = $this->Content->find('all', array(
			'contain' => array(),
			'fields' => array('id', 'title', 'path')
		));
		
		$this->set('contents', $contents);
	}

	function admin_edit($id){
		if ( isset($this->data) ){
			$this->Content->id = $id;
			if ( $this->Content->save($this->data) ){
				$this->Session->setFlash('Stránka byla uložena!');
				$this->redirect(array('action' => 'edit', $id), null, true);
			} else {
				$this->Session->setFlash('Stránku se nepodařilo uložit!');
			}
		} else {
			$this->data = $this->Content->read(null, $id);
			$this->set('tinyMce', true);
			$this->set('tinyMceElement', 'ContentContent');
		}
		
	}
	
	function admin_add(){
		$this->set('tinyMce', true);
		$this->set('tinyMceElement', 'ContentContent');
		
		if ( isset($this->data) ){
			if ( $this->Content->save($this->data) ){
				$this->Session->setFlash('Stránka byla uložena!');
				$this->redirect(array('action' => 'edit', $this->Content->id), null, true);
			} else {
				$this->Session->setFlash('Stránku se nepodařilo uložit!');
			}
		}
	}

}
?>